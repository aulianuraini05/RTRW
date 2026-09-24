<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Code - paling atas -->
        <div>
            <x-input-label for="rt_code" :value="__('Kode Akses Registrasi')" />
            <x-text-input id="rt_code" class="block mt-1 w-full" type="text" name="rt_code" :value="old('rt_code')" required autofocus />
            <x-input-error :messages="$errors->get('rt_code')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Masukkan kode pendaftaran sesuai peran Anda. Kode dapat diperoleh dari pengurus RT/RW.</p>
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- No Whatsapp -->
        <div class="mt-4">
            <x-input-label for="no_whatsapp" :value="__('No. WhatsApp')" />
            <x-text-input id="no_whatsapp" class="block mt-1 w-full" type="text" name="no_whatsapp" :value="old('no_whatsapp')" required placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('no_whatsapp')" class="mt-2" />
        </div>

        <!-- NIK -->
        <div class="mt-4">
            <x-input-label for="nik" :value="__('NIK (16 digit)')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required maxlength="16" placeholder="3201xxxxxxxxxxxx" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- No KK -->
        <div class="mt-4">
            <x-input-label for="no_kk" :value="__('No. KK (16 digit)')" />
            <x-text-input id="no_kk" class="block mt-1 w-full" type="text" name="no_kk" :value="old('no_kk')" required maxlength="16" placeholder="3201xxxxxxxxxxxx" />
            <x-input-error :messages="$errors->get('no_kk')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">1 KK bisa punya beberapa NIK berbeda.</p>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <p class="mt-4 text-xs text-gray-500">Data lengkap (TTL, JK, alamat detail, dll) bisa dilengkapi setelah login di menu <strong>Profil → Lengkapi Profil</strong>.</p>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah terdaftar?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
