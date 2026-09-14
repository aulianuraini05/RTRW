<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rt_code' => ['required', 'string'],
        ]);

        $code = strtoupper(trim($request->rt_code));

        $superAdminCode = env('SUPERADMIN_REGISTRATION_CODE', 'ADMIN-UTAMA');
        $rwCode = env('RW_REGISTRATION_CODE', 'KETUA-RW');

        $role = null;
        $rtId = null;

        if ($code === strtoupper($superAdminCode) || Rw::where('admin_code', $code)->exists()) {
            $role = 'superadmin';
        } elseif ($code === strtoupper($rwCode) || Rw::where('code', $code)->exists()) {
            $role = 'rw';
        } else {
            // Check if matches RT admin code (e.g. KETUA-RT01)
            $rtAdmin = Rt::where('admin_code', $code)->first();
            if ($rtAdmin) {
                $role = 'rt';
                $rtId = $rtAdmin->id;
            } else {
                // Check if matches Warga code (e.g. WARGA-RT01)
                $rtWarga = Rt::where('code', $code)->first();
                if ($rtWarga) {
                    $role = 'warga';
                    $rtId = $rtWarga->id;
                }
            }
        }

        if (! $role) {
            throw ValidationException::withMessages([
                'rt_code' => ['Kode pendaftaran tidak valid. Masukkan kode yang diberikan oleh pengurus.'],
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'rt_id' => $rtId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
