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
        try {
            DB::table('users')->updateOrInsert(
                ['email' => 'admin@perpustakaan.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'user@perpustakaan.com'],
                [
                    'name' => 'User',
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            // role column may not exist yet, try without it
            DB::table('users')->updateOrInsert(
                ['email' => 'admin@perpustakaan.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'user@perpustakaan.com'],
                [
                    'name' => 'User',
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return view('auth.login');
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
