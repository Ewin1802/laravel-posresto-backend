@php
    $today = now();
    $presets = [
        'Hari ini' => [$today->toDateString(), $today->toDateString()],
        '7 hari' => [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
        'Bulan ini' => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
        'Bulan lalu' => [$today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()],
        'Tahun ini' => [$today->copy()->startOfYear()->toDateString(), $today->copy()->endOfYear()->toDateString()],
    ];
    $extra = $extra ?? [];
@endphp

<section class="panel">
    <div class="panel__body" style="padding:18px 22px">
        <form method="GET" action="{{ $action }}" class="range-form">
            @foreach ($extra as $k => $v)
                @if ($v !== null && $v !== '')
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach
            <div class="form-group">
                <label for="start_date">Dari tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start }}" required>
            </div>
            <div class="form-group">
                <label for="end_date">Sampai tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end }}" required>
            </div>
            <button type="submit" class="btn btn-dark" style="height:44px"><i class="fas fa-filter"></i> Terapkan</button>
            @isset($actions)
                <div class="ml-auto d-flex" style="gap:8px">{!! $actions !!}</div>
            @endisset
        </form>
        <div class="range-presets">
            @foreach ($presets as $label => [$s, $e])
                <a href="{{ $action }}?{{ http_build_query(array_filter(array_merge($extra, ['start_date' => $s, 'end_date' => $e]), fn ($v) => $v !== null && $v !== '')) }}"
                    class="chip {{ $start === $s && $end === $e ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>
</section>
