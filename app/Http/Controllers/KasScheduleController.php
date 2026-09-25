<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\KasSchedule;
use App\Models\Rt;
use App\Models\User;
use App\Services\Notifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Jadwal kas bulanan per RT.
 *
 * Ketua RT mengisi nominal kas bulan berjalan untuk RT-nya sendiri.
 * Begitu disimpan, sistem otomatis menerbitkan tagihan (pending)
 * untuk seluruh warga di RT tersebut. Warga tinggal memilih
 * metode pembayaran — nominal sudah dikunci dari jadwal.
 */
class KasScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = KasSchedule::query()->with(['rt', 'creator'])->withCount([
            'transactions',
            'transactions as paid_count' => fn ($q) => $q->where('payment_status', 'lunas'),
        ]);

        // Ketua RT hanya melihat jadwal RT-nya sendiri.
        if ($request->user()->isRt()) {
            $query->where('rt_id', $request->user()->rt_id);
        } elseif ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        $schedules = $query->orderByDesc('year')->orderByDesc('month')->paginate(10)->withQueryString();
        $rts = Rt::orderByRaw("CAST(substr(name, 4) AS INTEGER)")->get();

        return view('kas_schedules.index', compact('schedules', 'rts'));
    }

    public function create()
    {
        $user = request()->user();

        // Ketua RT wajib terdaftar di suatu RT dan hanya bisa mengisi untuk RT-nya.
        if ($user->isRt() && empty($user->rt_id)) {
            return redirect()->route('kas_schedules.index')
                ->with('error', 'Akun Ketua RT Anda belum terhubung ke data RT. Hubungi admin.');
        }

        $rts = Rt::orderByRaw("CAST(substr(name, 4) AS INTEGER)")->get();
        $defaultPeriode = now()->format('Y-m');

        return view('kas_schedules.create', compact('rts', 'defaultPeriode'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'rt_id' => [$user->isRt() ? 'nullable' : 'required', 'exists:rts,id'],
            'periode' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        // Ketua RT selalu memakai RT-nya sendiri, abaikan input rt_id.
        $rtId = $user->isRt() ? $user->rt_id : $validated['rt_id'];
        abort_if(empty($rtId), 422, 'RT tidak valid.');

        $date = Carbon::createFromFormat('Y-m', $validated['periode']);
        $month = (int) $date->format('m');
        $year = (int) $date->format('Y');

        // Satu RT hanya boleh punya satu jadwal per bulan.
        $exists = KasSchedule::where('rt_id', $rtId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['periode' => 'Kas bulan '.$date->translatedFormat('F Y').' untuk RT ini sudah ditentukan.']);
        }

        $schedule = KasSchedule::create([
            'rt_id' => $rtId,
            'month' => $month,
            'year' => $year,
            'amount' => $validated['amount'],
            'created_by' => $user->id,
        ]);

        $generated = $this->generateBills($schedule);

        return redirect()->route('kas_schedules.show', $schedule)->with(
            'success',
            'Kas '.$schedule->period_label.' sebesar Rp '.number_format((float) $schedule->amount, 0, ',', '.')
            .' diterbitkan ke '.$generated.' warga '.($schedule->rt?->name ?? '').'.'
        );
    }

    public function show(KasSchedule $kasSchedule)
    {
        $user = request()->user();

        // Ketua RT hanya boleh membuka jadwal RT-nya sendiri.
        if ($user->isRt() && (int) $kasSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        $kasSchedule->load(['rt', 'creator']);

        $transactions = $kasSchedule->transactions()->with('user')->latest('id')->paginate(15);

        $totalWarga = $kasSchedule->transactions()->count();
        $totalLunas = (clone $kasSchedule->transactions())->where('payment_status', 'lunas')->count();
        $totalTerkumpul = (clone $kasSchedule->transactions())->where('payment_status', 'lunas')->sum('amount');

        return view('kas_schedules.show', compact(
            'kasSchedule',
            'transactions',
            'totalWarga',
            'totalLunas',
            'totalTerkumpul',
        ));
    }

    /**
     * Terbitkan tagihan susulan untuk warga yang belum punya tagihan
     * pada jadwal ini (misal warga baru terdaftar setelah jadwal dibuat).
     */
    public function sync(KasSchedule $kasSchedule)
    {
        $user = request()->user();

        if ($user->isRt() && (int) $kasSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        $generated = $this->generateBills($kasSchedule);

        return back()->with('success', $generated > 0
            ? $generated.' tagihan susulan berhasil diterbitkan.'
            : 'Semua warga sudah memiliki tagihan untuk periode ini.');
    }

    public function destroy(KasSchedule $kasSchedule)
    {
        $user = request()->user();

        if ($user->isRt() && (int) $kasSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        // Hapus tagihan yang belum dibayar; yang sudah lunas tetap tersimpan
        // sebagai riwayat (tautan jadwalnya otomatis dikosongkan).
        $removed = $kasSchedule->transactions()->where('payment_status', 'pending')->delete();
        $label = $kasSchedule->period_label;
        $kasSchedule->delete();

        return redirect()->route('kas_schedules.index')->with(
            'success',
            'Jadwal kas '.$label.' dihapus. '.$removed.' tagihan yang belum dibayar ikut dihapus; pembayaran yang sudah lunas tetap tersimpan.'
        );
    }

    /**
     * Buatkan tagihan pending untuk warga RT yang belum punya tagihan
     * pada jadwal ini. Mengembalikan jumlah tagihan yang dibuat.
     */
    private function generateBills(KasSchedule $schedule): int
    {
        $existingUserIds = $schedule->transactions()->whereNotNull('user_id')->pluck('user_id');

        $warga = User::query()
            ->where('role', 'warga')
            ->where('rt_id', $schedule->rt_id)
            ->whereNotIn('id', $existingUserIds)
            ->orderBy('name')
            ->get();

        foreach ($warga as $user) {
            $txn = CashTransaction::create([
                'user_id' => $user->id,
                'kas_schedule_id' => $schedule->id,
                'rt_id' => $schedule->rt_id,
                'payer_name' => $user->name,
                'amount' => $schedule->amount,
                'payment_method' => null,
                'payment_code' => 'KAS-'.$schedule->year.sprintf('%02d', $schedule->month).'-'.strtoupper(Str::random(6)),
                'payment_status' => 'pending',
            ]);
            Notifier::send(
                $user,
                'Tagihan kas baru',
                'Tagihan kas '.$schedule->period_label.' sebesar Rp '.number_format((float) $schedule->amount, 0, ',', '.').' telah terbit.',
                route('cash_transactions.show', $txn),
            );
        }

        return $warga->count();
    }
}
