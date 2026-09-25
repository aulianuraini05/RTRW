<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activities = Activity::query()
            ->with(['user', 'rt'])
            // Warga: hanya riwayat sendiri. Ketua RT: riwayat RT-nya. RW/Admin: semua.
            ->when($user->isWarga(), fn ($q) => $q->where('user_id', $user->id))
            ->when($user->isRt(), function ($q) use ($user) {
                if (empty($user->rt_id)) {
                    return $q->whereRaw('0 = 1');
                }

                return $q->where('rt_id', $user->rt_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.trim($request->string('search')).'%';

                return $q->where(function ($sub) use ($search) {
                    $sub->where('subject', 'like', $search)
                        ->orWhere('action', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search));
                });
            })
            ->when($request->filled('module'), function ($q) use ($request) {
                $modules = ['pengumuman', 'aspirasi', 'aset', 'kas', 'iuran', 'surat', 'marketplace'];
                $module = $request->string('module')->toString();

                return in_array($module, $modules, true) ? $q->where('module', $module) : $q;
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('activities.index', compact('activities'));
    }
}
