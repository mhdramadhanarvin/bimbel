<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Daftar sekarang')" :description="__('Lengkapi form dibawah ini')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">

        <label class="text-lg font-semibold"> Data Pribadi Siswa</label>
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Nama')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Nama lengkap')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <flux:select wire:model="gender" label="Jenis Kelamin" placeholder="Pilih Jenis Kelamin">
            <flux:select.option value="male">Laki-Laki</flux:select.option>
            <flux:select.option value="female">Perempuan</flux:select.option>
        </flux:select>

        <flux:input
            wire:model="place_of_birth"
            :label="__('Tempat Lahir')"
            type="text"
            required
            :placeholder="__('Tempat Lahir')"
        />

        <flux:input
            wire:model="date_of_birth"
            :label="__('Tanggal Lahir')"
            type="date"
            required
            :placeholder="__('Tanggal Lahir')"
        />

        <flux:select wire:model="gender" label="Agama" placeholder="Pilih Agama">
            <flux:select.option value="islam">Islam</flux:select.option>
            <flux:select.option value="protestan">Kristen Protestan</flux:select.option>
            <flux:select.option value="katolik">Kristen Katolik</flux:select.option>
            <flux:select.option value="buddha">Buddha</flux:select.option>
            <flux:select.option value="hindu">Hindu</flux:select.option>
            <flux:select.option value="konghucu">Konghucu</flux:select.option>
        </flux:select>

        <flux:textarea
            wire:model="address"
            :label="__('Alamat Lengkap')"
            required
            :placeholder="__('Alamat Lengkap')"
        />

        <flux:input
            wire:model="phone_number"
            :label="__('Nomor HP Siswa')"
            type="text"
            required
            :placeholder="__('Nomor HP Siswa')"
        />

        <flux:select wire:model="programme" label="Program" placeholder="Pilih Program">
            <flux:select.option value="polri">Bintara Polri</flux:select.option>
            <flux:select.option value="tni">Bintara TNI</flux:select.option>
            <flux:select.option value="kedinasan">Sekolah Kedinasan</flux:select.option>
        </flux:select>

        <flux:input
            wire:model="origin_school"
            :label="__('Asal Sekolah')"
            type="text"
            required
            :placeholder="__('Asal Sekolah')"
        />

        <label class="text-lg font-semibold mt-4"> Data Pribadi Siswa</label>

        <flux:input
            wire:model="parent_name"
            :label="__('Nama Lengkap Orangtua')"
            type="text"
            required
            :placeholder="__('Nama Lengkap Orangtua')"
        />

        <flux:input
            wire:model="parent_phone_number"
            :label="__('No HP')"
            type="text"
            required
            :placeholder="__('No HP')"
        />


        <flux:textarea
            wire:model="parent_address"
            :label="__('Alamat Lengkap')"
            required
            :placeholder="__('Alamat Lengkap')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full font-semibold">
                {{ __('Daftar sekarang') }}
            </flux:button>
        </div>
    </form>

    <!--<div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Sudah punya akun?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>-->
</div>
