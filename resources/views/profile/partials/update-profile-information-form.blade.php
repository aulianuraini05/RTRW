<section>
    <header>
        <h2 class="text-base font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
        @if (! $user->isProfileComplete())
            <div class="mt-3 rounded-md bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                Profil belum lengkap. Lengkapi TTL, jenis kelamin, dan alamat agar bisa cetak surat resmi.
            </div>
        @endif
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="no_whatsapp" :value="__('No. WhatsApp')" />
            <x-text-input id="no_whatsapp" name="no_whatsapp" type="text" class="mt-1 block w-full" :value="old('no_whatsapp', $user->no_whatsapp)" placeholder="08xxxxxxxxxx" />
            <x-input-error class="mt-2" :messages="$errors->get('no_whatsapp')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="nik" :value="__('NIK (16 digit)')" />
                <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full" :value="old('nik', $user->nik)" maxlength="16" />
                <x-input-error class="mt-2" :messages="$errors->get('nik')" />
            </div>
            <div>
                <x-input-label for="no_kk" :value="__('No. KK (16 digit)')" />
                <x-text-input id="no_kk" name="no_kk" type="text" class="mt-1 block w-full" :value="old('no_kk', $user->no_kk)" maxlength="16" />
                <x-input-error class="mt-2" :messages="$errors->get('no_kk')" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="tempat_lahir" :value="__('Tempat Lahir')" />
                <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full" :value="old('tempat_lahir', $user->tempat_lahir)" />
                <x-input-error class="mt-2" :messages="$errors->get('tempat_lahir')" />
            </div>
            <div>
                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih --</option>
                    <option value="L" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
            </div>
            <div>
                <x-input-label for="status_perkawinan" :value="__('Status Perkawinan')" />
                <select id="status_perkawinan" name="status_perkawinan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih --</option>
                    <option value="belum_kawin" @selected(old('status_perkawinan', $user->status_perkawinan) === 'belum_kawin')>Belum Kawin</option>
                    <option value="kawin" @selected(old('status_perkawinan', $user->status_perkawinan) === 'kawin')>Kawin</option>
                    <option value="cerai_hidup" @selected(old('status_perkawinan', $user->status_perkawinan) === 'cerai_hidup')>Cerai Hidup</option>
                    <option value="cerai_mati" @selected(old('status_perkawinan', $user->status_perkawinan) === 'cerai_mati')>Cerai Mati</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status_perkawinan')" />
            </div>
        </div>

        <div>
            <x-input-label for="agama" :value="__('Agama')" />
            <select id="agama" name="agama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih --</option>
                @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $ag)
                    <option value="{{ $ag }}" @selected(old('agama', $user->agama) === $ag)>{{ $ag }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('agama')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="pendidikan_terakhir" :value="__('Pendidikan Terakhir')" />
                <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih --</option>
                    @foreach (['Tidak Sekolah','SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $p)
                        <option value="{{ $p }}" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) === $p)>{{ $p }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('pendidikan_terakhir')" />
            </div>
            <div>
                <x-input-label for="pekerjaan" :value="__('Pekerjaan')" />
                <x-text-input id="pekerjaan" name="pekerjaan" type="text" class="mt-1 block w-full" :value="old('pekerjaan', $user->pekerjaan)" placeholder="Wiraswasta / PNS / dll" />
                <x-input-error class="mt-2" :messages="$errors->get('pekerjaan')" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <x-input-label for="alamat_rumah" :value="__('Alamat Rumah')" />
                <x-text-input id="alamat_rumah" name="alamat_rumah" type="text" class="mt-1 block w-full" :value="old('alamat_rumah', $user->alamat_rumah)" placeholder="Jl. Mawar No. ..." />
                <x-input-error class="mt-2" :messages="$errors->get('alamat_rumah')" />
            </div>
            <div>
                <x-input-label for="no_rumah" :value="__('No. Rumah')" />
                <x-text-input id="no_rumah" name="no_rumah" type="text" class="mt-1 block w-full" :value="old('no_rumah', $user->no_rumah)" placeholder="12A" />
                <x-input-error class="mt-2" :messages="$errors->get('no_rumah')" />
            </div>
        </div>

        @php $rw = \App\Models\Rw::first(); @endphp
        @if($rw)
            <div class="rounded-md bg-gray-50 border p-3 text-xs text-gray-600">
                <strong>Wilayah (single - otomatis dari RW):</strong><br>
                {{ $rw->alamat_lengkap ?? '-' }} — Kel. {{ $rw->kelurahan ?? '-' }}, Kec. {{ $rw->kecamatan ?? '-' }}, {{ $rw->kota_kabupaten ?? '-' }} {{ $rw->kode_pos ?? '' }} {{ $rw->provinsi ? ', '.$rw->provinsi : '' }}
                <br><span class="text-gray-500">Jika salah, minta Ketua RW update data RW, bukan per warga input kota/kecamatan.</span>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
