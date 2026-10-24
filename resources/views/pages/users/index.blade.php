@extends('layouts.app')

@section('title', 'Pengguna')
@section('crumb', 'Data umum')

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Pengguna</h1>
            <p class="page-head__lead">Akun staf dan kasir yang dapat masuk ke aplikasi ARCH. Akun admin tidak ditampilkan di sini.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah pengguna</a>
        </div>
    </div>

    @include('layouts.alert')

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('users.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="name" value="{{ $search }}" placeholder="Cari nama atau email" aria-label="Cari pengguna">
                </div>
                <select name="role" class="custom-select" style="width:auto" onchange="this.form.submit()" aria-label="Filter peran">
                    <option value="">Semua peran</option>
                    <option value="staff" @selected($role === 'staff')>Staff ({{ $roleCounts['staff'] ?? 0 }})</option>
                    <option value="user" @selected($role === 'user')>User ({{ $roleCounts['user'] ?? 0 }})</option>
                </select>
                @if ($search || $role)
                    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <span class="count-chip">{{ $users->total() }} pengguna</span>
        </div>

        @if ($users->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-user-group"></i></div>
                <h3>{{ $search || $role ? 'Tidak ada pengguna yang cocok' : 'Belum ada pengguna' }}</h3>
                <p>{{ $search || $role ? 'Coba kata kunci lain atau hapus filter.' : 'Tambahkan akun untuk staf atau kasir agar bisa masuk ke aplikasi.' }}</p>
                @unless ($search || $role)
                    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah pengguna</a>
                @endunless
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Peran</th>
                            <th>Terdaftar</th>
                            <th class="t-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php
                                $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                            @endphp
                            <tr>
                                <td>
                                    <div class="cell-media">
                                        <span class="avatar-sm">{{ $initials }}</span>
                                        <div>
                                            <div class="cell-main">{{ $user->name }}</div>
                                            <div class="cell-sub">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="tag {{ $user->role === 'staff' ? 'tag--gold' : '' }} text-capitalize">{{ $user->role }}</span>
                                </td>
                                <td>
                                    <div>{{ $user->created_at?->translatedFormat('d M Y') }}</div>
                                    <div class="cell-sub">{{ $user->created_at?->diffForHumans() }}</div>
                                </td>
                                <td class="t-actions">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-light btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        data-confirm="Akun {{ $user->name }} akan dihapus dan tidak bisa lagi masuk ke aplikasi kasir."
                                        data-confirm-title="Hapus pengguna?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $user->name }}"><i class="fas fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        @endif
    </section>
@endsection
