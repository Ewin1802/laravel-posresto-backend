@if ($message = session('success'))
    <div class="flash flash--success" role="status">
        <i class="fas fa-circle-check"></i>
        <div class="flash__body">{{ $message }}</div>
        <button type="button" class="flash__close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($message = session('error'))
    <div class="flash flash--error" role="alert">
        <i class="fas fa-circle-exclamation"></i>
        <div class="flash__body">{{ $message }}</div>
        <button type="button" class="flash__close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($message = session('info'))
    <div class="flash flash--info" role="status">
        <i class="fas fa-circle-info"></i>
        <div class="flash__body">{{ $message }}</div>
        <button type="button" class="flash__close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if (isset($errors) && $errors->any() && ($showErrors ?? false))
    <div class="flash flash--error" role="alert">
        <i class="fas fa-circle-exclamation"></i>
        <div class="flash__body">
            Periksa kembali isian berikut:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="flash__close" aria-label="Tutup">&times;</button>
    </div>
@endif
