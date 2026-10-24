@php $editing = isset($user); @endphp

<div class="form-section">
    <h2 class="form-section__title">Identitas</h2>
    <p class="form-section__lead">Nama tampil di struk dan laporan sebagai nama kasir.</p>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="name">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror" autocomplete="off" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                class="form-control @error('email') is-invalid @enderror" autocomplete="off" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h2 class="form-section__title">Akses</h2>
    <p class="form-section__lead">{{ $editing ? 'Kosongkan kata sandi bila tidak ingin menggantinya.' : 'Kata sandi minimal 8 karakter.' }}</p>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="password">{{ $editing ? 'Kata sandi baru' : 'Kata sandi' }}</label>
            <div class="password-field">
                <input id="password" type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                <button type="button" data-toggle-password aria-label="Tampilkan kata sandi"><i class="fas fa-eye"></i></button>
            </div>
            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @if ($editing)
                <span class="hint">Mengganti kata sandi akan mengeluarkan akun ini dari aplikasi kasir.</span>
            @endif
        </div>
        <div class="col-md-6 form-group">
            <span class="form-label d-block">Peran</span>
            @php
                $roleNow = old('role', $user->role ?? 'staff');
                $roles = ['staff' => 'Staff', 'user' => 'User'];
                if ($editing) { $roles['admin'] = 'Admin'; }
            @endphp
            <div class="segmented" role="radiogroup" aria-label="Peran">
                @foreach ($roles as $value => $label)
                    <label>
                        <input type="radio" name="role" value="{{ $value }}" @checked($roleNow === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @if ($editing)
                <span class="hint">Pengguna yang dijadikan admin tidak lagi tampil di daftar ini.</span>
            @endif
        </div>
    </div>
</div>
