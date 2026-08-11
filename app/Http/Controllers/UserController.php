<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        $usersJson = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nisn' => $user->nisn,
                'role' => $user->role,
                'show_url' => route('users.show', $user),
                'edit_url' => route('users.edit', $user),
                'destroy_url' => route('users.destroy', $user),
            ];
        })->values();

        return view('users.index', compact('usersJson'));
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
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,staff,user',
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
            'role' => 'required|in:admin,staff,user',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'confirmed', Password::defaults()]]);
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

    public function showImport()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $rows = $this->parseCsv($request->file('csv_file'));

        if (empty($rows) || !isset($rows[0]['name'], $rows[0]['email'])) {
            return back()->with('error', 'Kolom wajib "nama" dan "email" tidak ditemukan di dalam file CSV.');
        }

        $existingEmails = User::whereIn('email', array_map('strtolower', array_column($rows, 'email')))
            ->pluck('email')
            ->flip();
        $existingNisns = User::whereNotNull('nisn')
            ->whereIn('nisn', array_filter(array_column($rows, 'nisn')))
            ->pluck('nisn')
            ->flip();

        $imported = [];
        $failed = [];
        $fileEmails = [];
        $fileNisns = [];

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? '')));
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $role = strtolower(trim((string) ($row['role'] ?? '')));
            $password = (string) ($row['password'] ?? '');

            if ($role === '' || $role === 'anggota') {
                $role = 'user';
            }

            $errors = [];

            if ($name === '') {
                $errors[] = 'Nama kosong';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email tidak valid';
            }
            if ($existingEmails->has($email) || isset($fileEmails[$email])) {
                $errors[] = 'Email sudah terdaftar';
            }
            if ($nisn !== '' && ($existingNisns->has($nisn) || isset($fileNisns[$nisn]))) {
                $errors[] = 'NISN sudah terdaftar';
            }
            if (!in_array($role, ['admin', 'staff', 'user'], true)) {
                $errors[] = 'Role tidak dikenali (gunakan admin, staff, atau user)';
            }
            if ($password !== '') {
                $validator = Validator::make(['password' => $password], ['password' => Password::defaults()]);
                if ($validator->fails()) {
                    $errors[] = 'Password minimal 8 karakter, kombinasi huruf dan angka';
                }
            }

            $fileEmails[$email] = true;
            if ($nisn !== '') {
                $fileNisns[$nisn] = true;
            }

            if (!empty($errors)) {
                $failed[] = ['name' => $name, 'email' => $email, 'errors' => $errors];
                continue;
            }

            $finalPassword = $password !== '' ? $password : Str::random(10);

            User::create([
                'name' => $name,
                'email' => $email,
                'nisn' => $nisn !== '' ? $nisn : null,
                'password' => $finalPassword,
                'role' => $role,
            ]);

            $imported[] = [
                'name' => $name,
                'email' => $email,
                'nisn' => $nisn,
                'role' => $role,
                'password' => $finalPassword,
            ];
        }

        return view('users.import', compact('imported', 'failed'));
    }

    public function downloadTemplate()
    {
        $filename = 'template_import_anggota.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nama', 'email', 'nisn', 'role', 'password']);
            fputcsv($file, ['Budi Santoso', 'budi@example.com', '0012345678', 'user', 'santos12345']);
            fputcsv($file, ['Siti Aminah', 'siti@example.com', '0098765432', '', 'aminah2026']);
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    private function parseCsv(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());

        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        $firstLine = strtok($content, "\r\n");
        $comma = substr_count($firstLine, ',');
        $semicolon = substr_count($firstLine, ';');
        $tab = substr_count($firstLine, "\t");
        $delimiter = ',';
        if ($semicolon > $comma && $semicolon >= $tab) {
            $delimiter = ';';
        } elseif ($tab > $comma && $tab > $semicolon) {
            $delimiter = "\t";
        }

        $temp = tmpfile();
        fwrite($temp, $content);
        fseek($temp, 0);

        $rows = [];
        $header = null;
        while (($line = fgetcsv($temp, 0, $delimiter)) !== false) {
            if ($header === null) {
                $header = array_map(fn ($col) => strtolower(trim((string) $col)), $line);
                continue;
            }
            if (count(array_filter($line, fn ($col) => trim((string) $col) !== '')) === 0) {
                continue;
            }
            $rows[] = $line;
        }
        fclose($temp);

        $map = [
            'nama' => 'name',
            'nama lengkap' => 'name',
            'name' => 'name',
            'email' => 'email',
            'nisn' => 'nisn',
            'password' => 'password',
            'kata sandi' => 'password',
            'role' => 'role',
            'peran' => 'role',
        ];

        $result = [];
        foreach ($rows as $line) {
            $row = [];
            foreach ($header as $i => $col) {
                if (isset($map[$col])) {
                    $row[$map[$col]] = trim((string) ($line[$i] ?? ''));
                }
            }
            $result[] = $row;
        }

        return $result;
    }
}
