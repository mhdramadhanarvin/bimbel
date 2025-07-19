<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Daftar sekarang')" :description="__('Lengkapi form dibawah ini')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
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

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Konfirmasi password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Konfirmasi password')"
            viewable
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
