@extends('layouts.app')

@section('title', 'Pengeluaran')
@section('crumb', 'Keuangan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') . ' – ' . \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y');
    $maxCat = max(1, (float) $byCategory->max('total'));
    $topCat = $byCategory->first();
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Pengeluaran</h1>
            <p class="page-head__lead">Semua uang keluar untuk operasional cafe, {{ $rangeLabel }}. Dipakai sebagai pembanding di Analisa keuangan.</p>
        </div>
        <div class="page-head__actions">
            <button type="button" class="btn btn-light" data-guide-open="expenses" data-guide-auto="arch.guide.expenses"><i class="fas fa-book-open"></i> Panduan</button>
            <a href="{{ route('financial.report', ['start_date' => $start_date, 'end_date' => $end_date]) }}" class="btn btn-light"><i class="fas fa-chart-line"></i> Analisa keuangan</a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
        </div>
    </div>

    @include('layouts.alert')

    @include('pages.partials.range-filter', [
        'action' => route('expenses.index'),
        'start' => $start_date,
        'end' => $end_date,
        'extra' => ['category' => $category, 'q' => $search],
    ])

    <div class="row">
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-wallet"></i> Total pengeluaran</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($total) }}</div>
                <div class="stat__meta">{{ $count }} catatan pada periode ini</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-calendar-day"></i> Rata-rata per hari</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($perDay) }}</div>
                <div class="stat__meta">Total dibagi jumlah hari dalam rentang</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-chart-pie"></i> Pos terbesar</div>
                <div class="stat__value" style="font-size:22px">{{ $topCat ? \App\Models\Expense::categoryLabel($topCat->category) : '—' }}</div>
                <div class="stat__meta">
                    @if ($topCat && $total > 0)
                        Rp {{ $rp($topCat->total) }} · {{ number_format($topCat->total / $total * 100, 0) }}% dari total
                    @else
                        Belum ada pengeluaran
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <div class="panel__head">
                    <form method="GET" action="{{ route('expenses.index') }}" class="toolbar">
                        <input type="hidden" name="start_date" value="{{ $start_date }}">
                        <input type="hidden" name="end_date" value="{{ $end_date }}">
                        <div class="search">
                            <i class="fas fa-magnifying-glass"></i>
                            <input type="search" class="form-control" name="q" value="{{ $search }}" placeholder="Cari keterangan" aria-label="Cari pengeluaran">
                        </div>
                        <select name="category" class="custom-select" style="width:auto" onchange="this.form.submit()" aria-label="Filter kategori">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $key => $c)
                                <option value="{{ $key }}" @selected($category === $key)>{{ $c['label'] }}</option>
                            @endforeach
                        </select>
                        @if ($category || $search)
                            <a href="{{ route('expenses.index', ['start_date' => $start_date, 'end_date' => $end_date]) }}" class="btn btn-ghost btn-sm">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($expenses->isEmpty())
                    <div class="empty">
                        <div class="empty__icon"><i class="fas fa-wallet"></i></div>
                        <h3>{{ $category || $search ? 'Tidak ada yang cocok' : 'Belum ada pengeluaran' }}</h3>
                        <p>{{ $category || $search ? 'Coba kata kunci atau kategori lain.' : 'Catat belanja bahan, gaji, listrik, dan biaya lain agar laba bisa dihitung.' }}</p>
                        @unless ($category || $search)
                            <div class="d-flex justify-content-center flex-wrap" style="gap:10px">
                                <button type="button" class="btn btn-light" data-guide-open="expenses"><i class="fas fa-book-open"></i> Baca panduan</button>
                                <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
                            </div>
                        @endunless
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table-x">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Kategori</th>
                                    <th class="t-right">Jumlah</th>
                                    <th class="t-actions"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $e)
                                    <tr>
                                        <td>
                                            <div class="num">{{ $e->expense_date->translatedFormat('d M') }}</div>
                                            <div class="cell-sub">{{ $e->expense_date->translatedFormat('l') }}</div>
                                        </td>
                                        <td>
                                            <div class="cell-main">{{ $e->description }}</div>
                                            <div class="cell-sub text-truncate" style="max-width:320px">
                                                {{ $e->notes ? $e->notes . ' · ' : '' }}dicatat {{ $e->user->name ?? 'sistem' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="tag tag--plain"><i class="fas {{ \App\Models\Expense::categoryIcon($e->category) }}" style="font-size:11px"></i> {{ $e->category_label }}</span>
                                        </td>
                                        <td class="t-right num font-weight-bold">Rp {{ $rp($e->amount) }}</td>
                                        <td class="t-actions">
                                            <a href="{{ route('expenses.edit', $e) }}" class="btn btn-light btn-sm" aria-label="Ubah {{ $e->description }}"><i class="fas fa-pen"></i></a>
                                            <form action="{{ route('expenses.destroy', $e) }}" method="POST" class="d-inline"
                                                data-confirm="Pengeluaran &quot;{{ $e->description }}&quot; sebesar Rp {{ $rp($e->amount) }} akan dihapus dan tidak lagi dihitung di Analisa keuangan."
                                                data-confirm-title="Hapus pengeluaran?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $e->description }}"><i class="fas fa-trash-can"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $expenses->links() }}
                @endif
            </section>
        </div>

        <div class="col-lg-4">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Per kategori</h2>
                        <p class="panel__sub">Klik untuk menyaring daftar</p>
                    </div>
                </div>
                <div class="panel__body pt-2">
                    @forelse ($byCategory as $row)
                        <a href="{{ route('expenses.index', ['start_date' => $start_date, 'end_date' => $end_date, 'category' => $row->category]) }}"
                            class="d-block py-2 {{ $category === $row->category ? 'font-weight-bold' : '' }}" style="color:inherit">
                            <div class="d-flex justify-content-between align-items-center" style="font-size:13.5px; gap:10px">
                                <span class="d-inline-flex align-items-center" style="gap:8px">
                                    <i class="fas {{ \App\Models\Expense::categoryIcon($row->category) }} text-muted" style="width:16px"></i>
                                    {{ \App\Models\Expense::categoryLabel($row->category) }}
                                </span>
                                <span class="num">Rp {{ $rp($row->total) }}</span>
                            </div>
                            <div class="meter {{ $loop->first ? 'meter--red' : '' }} mt-1"><span style="width: {{ max(2, $row->total / $maxCat * 100) }}%"></span></div>
                        </a>
                    @empty
                        <p class="text-muted my-3">Belum ada data pada rentang ini.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection

@include('pages.expenses._guide')
