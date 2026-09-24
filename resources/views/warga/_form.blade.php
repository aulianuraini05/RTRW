<div class="space-y-6">
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="no_whatsapp" value="No. WhatsApp" />
            <x-text-input id="no_whatsapp" name="no_whatsapp" type="text" class="mt-1 block w-full" :value="old('no_whatsapp', $user->no_whatsapp ?? '')" required placeholder="08xxxxxxxxxx" />
            <x-input-error class="mt-2" :messages="$errors->get('no_whatsapp')" />
        </div>
        <div>
            <x-input-label for="rt_id" value="RT Warga" />
            @if($rts->count() === 1)
                <x-text-input type="text" class="mt-1 block w-full bg-gray-100" :value="$rts->first()->name" disabled />
                <input type="hidden" name="rt_id" value="{{ old('rt_id', $user->rt_id ?? $rts->first()->id) }}" />
                <p class="mt-1 text-xs text-gray-500">Ketua RT hanya bisa menambah warga di {{ $rts->first()->name }}.</p>
            @else
                <select id="rt_id" name="rt_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Pilih RT</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}" @selected((string)old('rt_id', $user->rt_id ?? '') === (string)$rt->id)>{{ $rt->name }} — {{ $rt->code }}</option>
                    @endforeach
                </select>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('rt_id')" />
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="nik" value="NIK (16 digit)" />
            <x-text-input id="nik" name="nik" type="text" maxlength="16" class="mt-1 block w-full" :value="old('nik', $user->nik ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('nik')" />
        </div>
        <div>
            <x-input-label for="no_kk" value="No. KK (16 digit)" />
            <x-text-input id="no_kk" name="no_kk" type="text" maxlength="16" class="mt-1 block w-full" :value="old('no_kk', $user->no_kk ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('no_kk')" />
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="password" :value="isset($user) ? __('Password Baru (kosongkan jika tidak ganti)') : __('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" :required="!isset($user)" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" :required="!isset($user)" />
        </div>
    </div>

    <div class="border-t pt-6">
        <p class="text-sm font-semibold text-gray-700">Data Profil (opsional — bisa dilengkapi warga nanti)</p>
        <div class="mt-4 grid gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full" :value="old('tempat_lahir', $user->tempat_lahir ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('tempat_lahir')" />
            </div>
            <div>
                <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir', isset($user->tanggal_lahir) ? $user->tanggal_lahir->format('Y-m-d') : '')" />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
            </div>
            <div>
                <x-input-label for="jenis_kelamin" value="Jenis Kelamin" />
                <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih</option>
                    <option value="L" @selected(old('jenis_kelamin', $user->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $user->jenis_kelamin ?? '') === 'P')>Perempuan</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
            </div>
            <div>
                <x-input-label for="agama" value="Agama" />
                <x-text-input id="agama" name="agama" type="text" class="mt-1 block w-full" :value="old('agama', $user->agama ?? '')" placeholder="Islam / Kristen / dll" />
                <x-input-error class="mt-2" :messages="$errors->get('agama')" />
            </div>
            <div>
                <x-input-label for="alamat_rumah" value="Alamat Rumah" />
                <x-text-input id="alamat_rumah" name="alamat_rumah" type="text" class="mt-1 block w-full" :value="old('alamat_rumah', $user->alamat_rumah ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('alamat_rumah')" />
            </div>
            <div>
                <x-input-label for="no_rumah" value="No. Rumah" />
                <x-text-input id="no_rumah" name="no_rumah" type="text" class="mt-1 block w-full" :value="old('no_rumah', $user->no_rumah ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('no_rumah')" />
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('warga.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
