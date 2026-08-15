<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-[24px] font-semibold tracking-tight text-white">Pengaturan Akun</h2>
            <p class="font-body text-[13px] text-[#8B949E] mt-1">Kelola profil, password, dan keamanan akun</p>
        </div>
    </x-slot>

    <div class="max-w-[800px] mx-auto space-y-4">
        <div class="glass rounded-[12px] p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="glass rounded-[12px] p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-[12px] p-6 bg-[#E76B73]/[0.035] border border-[#E76B73]/[0.15]">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>