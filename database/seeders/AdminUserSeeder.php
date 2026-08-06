<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUser('admin@perpustakaan.com', 'Admin', 'admin');
        $this->createUser('user@perpustakaan.com', 'User', 'user');
    }

    private function createUser(string $email, string $name, string $role): void
    {
        $user = User::firstOrNew(['email' => $email]);

        if ($user->exists) {
            return;
        }

        $user->fill([
            'name' => $name,
            'password' => Hash::make('password'),
            'role' => $role,
        ])->save();

        $this->command->warn(
            "Akun {$email} dibuat dengan password default \"password\" — segera ganti di production."
        );
    }
}
