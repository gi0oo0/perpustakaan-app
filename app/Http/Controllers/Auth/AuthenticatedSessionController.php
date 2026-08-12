<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        $this->seedDemoUsersIfMissing();

        return view('auth.login');
    }

    private function seedDemoUsersIfMissing(): void
    {
        $demoUsers = [
            [
                'name' => 'Admin',
                'email' => 'admin@perpustakaan.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nisn' => '0000000001',
            ],
            [
                'name' => 'User',
                'email' => 'user@perpustakaan.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'nisn' => '0000000002',
            ],
        ];

        try {
            foreach ($demoUsers as $user) {
                $exists = DB::table('users')->where('email', $user['email'])->exists();

                if (!$exists) {
                    DB::table('users')->insert(array_merge($user, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        } catch (\Exception $e) {
            // role/nisn column may not exist yet; try seeding without them
            try {
                foreach ($demoUsers as $user) {
                    $exists = DB::table('users')->where('email', $user['email'])->exists();

                    if (!$exists) {
                        DB::table('users')->insert([
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'password' => $user['password'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e2) {
                // ignore, seeding is best-effort
            }
        }
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
