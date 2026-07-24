<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nisn' => 'nullable|string|unique:users,nisn|max:255',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
                         ->with('success', 'Akun "' . $request->name . '" berhasil dibuat.');
    }

    public function show(User $user)
    {
        $loans = $user->loans()->with('book')->latest()->paginate(15);
        $totalLoans = $user->loans()->count();
        $activeLoans = $user->loans()->whereNull('returned_at')->count();
        $totalDenda = $user->loans()->sum('denda');
        $overdueLoans = $user->loans()->whereNull('returned_at')->where('due_date', '<', now())->count();

        return view('users.show', compact('user', 'loans', 'totalLoans', 'activeLoans', 'totalDenda', 'overdueLoans'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nisn' => 'nullable|string|unique:users,nisn,' . $user->id . '|max:255',
            'role' => 'required|in:admin,user',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'confirmed', Password::min(6)]]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
                         ->with('success', 'Akun "' . $user->name . '" berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus akun admin terakhir.');
        }

        $activeLoans = $user->loans()->whereNull('returned_at')->count();
        if ($activeLoans > 0) {
            return back()->with('error', 'Tidak dapat menghapus "' . $user->name . '" karena masih memiliki ' . $activeLoans . ' peminjaman aktif.');
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'Akun "' . $user->name . '" berhasil dihapus.');
    }
}
