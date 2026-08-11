<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Pengaturan Akun</h2>
            <p class="font-body text-sm text-white/45 mt-1">Kelola profil, password, dan keamanan akun</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="reveal">
        <div class="glass p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="glass p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="glass p-6 sm:p-8 border-rose-400/20">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
