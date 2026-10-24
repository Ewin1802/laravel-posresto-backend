<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = ['staff', 'user'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('name'));
        $role = $request->input('role');

        $users = User::query()
            ->where('role', '!=', 'admin')
            // Dikelompokkan agar pencarian email tidak ikut menampilkan akun admin.
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, self::ROLES, true), fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'roleCounts' => User::where('role', '!=', 'admin')
                ->selectRaw('role, COUNT(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(self::ROLES)],
        ], $this->messages());

        User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna {$data['name']} berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);

        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', '!=', 'admin')->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            // Admin boleh menaikkan pengguna menjadi admin (seperti versi sebelumnya).
            'role' => ['required', Rule::in([...self::ROLES, 'admin'])],
        ], $this->messages());

        $user->name = $data['name'];
        $user->email = strtolower($data['email']);
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            // Paksa login ulang di aplikasi kasir bila kata sandi diganti.
            $user->tokens()->delete();
        }

        $user->save();

        return redirect()->route('users.index')->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $user = User::where('role', '!=', 'admin')->find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Pengguna tidak ditemukan atau tidak boleh dihapus.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun yang sedang dipakai.');
        }

        $name = $user->name;
        $user->tokens()->delete();
        $user->delete();

        return redirect()->route('users.index')->with('success', "Pengguna {$name} telah dihapus.");
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Pilih peran pengguna.',
            'role.in' => 'Peran tidak valid.',
        ];
    }
}
