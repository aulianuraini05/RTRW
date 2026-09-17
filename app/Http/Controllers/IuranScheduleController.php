<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\IuranSchedule;
use App\Models\Rt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Jadwal iuran bulanan per RT.
 *
 * Ketua RT mengisi nominal iuran bulan berjalan untuk RT-nya sendiri.
 * Begitu disimpan, sistem otomatis menerbitkan tagihan (pending)
 * untuk seluruh warga di RT tersebut. Warga tinggal memilih
 * metode pembayaran — nominal sudah dikunci dari jadwal.
 */
class IuranScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = IuranSchedule::query()->with(['rt', 'creator'])->withCount([
            'contributions',
            'contributions as paid_count' => fn ($q) => $q->where('payment_status', 'lunas'),
        ]);

        // Ketua RT hanya melihat jadwal RT-nya sendiri.
        if ($request->user()->isRt()) {
            $query->where('rt_id', $request->user()->rt_id);
        } elseif ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        $schedules = $query->orderByDesc('year')->orderByDesc('month')->paginate(10)->withQueryString();
        $rts = Rt::orderBy('name')->get();

        return view('iuran_schedules.index', compact('schedules', 'rts'));
    }

    public function create()
    {
        $user = request()->user();

        // Ketua RT wajib terdaftar di suatu RT dan hanya bisa mengisi untuk RT-nya.
        if ($user->isRt() && empty($user->rt_id)) {
            return redirect()->route('iuran_schedules.index')
                ->with('error', 'Akun Ketua RT Anda belum terhubung ke data RT. Hubungi admin.');
        }

        $rts = Rt::orderBy('name')->get();
        $defaultPeriode = now()->format('Y-m');

        return view('iuran_schedules.create', compact('rts', 'defaultPeriode'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'rt_id' => [$user->isRt() ? 'nullable' : 'required', 'exists:rts,id'],
            'jenis' => ['required', 'string', Rule::in(IuranSchedule::jenisOptions())],
            'periode' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        // Ketua RT selalu memakai RT-nya sendiri, abaikan input rt_id.
        $rtId = $user->isRt() ? $user->rt_id : $validated['rt_id'];
        abort_if(empty($rtId), 422, 'RT tidak valid.');

        $date = Carbon::createFromFormat('Y-m', $validated['periode']);
        $month = (int) $date->format('m');
        $year = (int) $date->format('Y');
        $jenis = $validated['jenis'];

        // Satu RT tidak boleh dobel untuk jenis + bulan yang sama.
        $exists = IuranSchedule::where('rt_id', $rtId)
            ->where('jenis', $jenis)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['periode' => 'Iuran '.$jenis.' bulan '.$date->translatedFormat('F Y').' untuk RT ini sudah ditentukan.']);
        }

        $schedule = IuranSchedule::create([
            'rt_id' => $rtId,
            'jenis' => $jenis,
            'month' => $month,
            'year' => $year,
            'amount' => $validated['amount'],
            'created_by' => $user->id,
        ]);

        $generated = $this->generateBills($schedule);

        return redirect()->route('iuran_schedules.show', $schedule)->with(
            'success',
            $schedule->full_label.' sebesar Rp '.number_format((float) $schedule->amount, 0, ',', '.')
            .' diterbitkan ke '.$generated.' warga '.($schedule->rt?->name ?? '').'.'
        );
    }

    public function show(IuranSchedule $iuranSchedule)
    {
        $user = request()->user();

        // Ketua RT hanya boleh membuka jadwal RT-nya sendiri.
        if ($user->isRt() && (int) $iuranSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        $iuranSchedule->load(['rt', 'creator']);

        $contributions = $iuranSchedule->contributions()->with('user')->latest('id')->paginate(15);

        $totalWarga = $iuranSchedule->contributions()->count();
        $totalLunas = (clone $iuranSchedule->contributions())->where('payment_status', 'lunas')->count();
        $totalTerkumpul = (clone $iuranSchedule->contributions())->where('payment_status', 'lunas')->sum('amount');

        return view('iuran_schedules.show', compact(
            'iuranSchedule',
            'contributions',
            'totalWarga',
            'totalLunas',
            'totalTerkumpul',
        ));
    }

    /**
     * Terbitkan tagihan susulan untuk warga yang belum punya tagihan
     * pada jadwal ini (misal warga baru terdaftar setelah jadwal dibuat).
     */
    public function sync(IuranSchedule $iuranSchedule)
    {
        $user = request()->user();

        if ($user->isRt() && (int) $iuranSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        $generated = $this->generateBills($iuranSchedule);

        return back()->with('success', $generated > 0
            ? $generated.' tagihan susulan berhasil diterbitkan.'
            : 'Semua warga sudah memiliki tagihan untuk periode ini.');
    }

    public function destroy(IuranSchedule $iuranSchedule)
    {
        $user = request()->user();

        if ($user->isRt() && (int) $iuranSchedule->rt_id !== (int) $user->rt_id) {
            abort(403);
        }

        // Hapus tagihan yang belum dibayar; yang sudah lunas tetap tersimpan
        // sebagai riwayat (tautan jadwalnya otomatis dikosongkan).
        $removed = $iuranSchedule->contributions()->where('payment_status', 'pending')->delete();
        $label = $iuranSchedule->full_label;
        $iuranSchedule->delete();

        return redirect()->route('iuran_schedules.index')->with(
            'success',
            'Jadwal iuran '.$label.' dihapus. '.$removed.' tagihan yang belum dibayar ikut dihapus; pembayaran yang sudah lunas tetap tersimpan.'
        );
    }

    /**
     * Buatkan tagihan pending untuk warga RT yang belum punya tagihan
     * pada jadwal ini. Mengembalikan jumlah tagihan yang dibuat.
     */
    private function generateBills(IuranSchedule $schedule): int
    {
        $existingUserIds = $schedule->contributions()->whereNotNull('user_id')->pluck('user_id');

        $warga = User::query()
            ->where('role', 'warga')
            ->where('rt_id', $schedule->rt_id)
            ->whereNotIn('id', $existingUserIds)
            ->orderBy('name')
            ->get();

        foreach ($warga as $user) {
            Contribution::create([
                'user_id' => $user->id,
                'iuran_schedule_id' => $schedule->id,
                'rt_id' => $schedule->rt_id,
                'payer_name' => $user->name,
                'amount' => $schedule->amount,
                'payment_method' => null,
                'payment_code' => 'IUR-'.$schedule->year.sprintf('%02d', $schedule->month).'-'.strtoupper(Str::random(6)),
                'payment_status' => 'pending',
            ]);
        }

        return $warga->count();
    }
}
