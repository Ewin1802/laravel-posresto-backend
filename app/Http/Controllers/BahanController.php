<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('nama_bahan'));

        $bahans = Bahan::query()
            ->withCount('inventories')
            ->withSum('inventories as stok', 'quantity')
            ->when($search !== '', fn ($q) => $q->where('nama_bahan', 'like', "%{$search}%"))
            ->orderBy('nama_bahan')
            ->paginate(15)
            ->withQueryString();

        return view('pages.bahan.index', compact('bahans', 'search'));
    }

    public function edit(Bahan $bahan)
    {
        return view('pages.bahan.edit', compact('bahan'));
    }

    /**
     * Dipanggil via AJAX (modal "Tambah bahan") dan juga form biasa.
     * Respons JSON dipertahankan agar halaman Persediaan Masuk tetap berfungsi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
        ]);

        $namaBahan = $this->normalize($request->nama_bahan);
        $satuan = $this->normalize($request->satuan);

        $exists = Bahan::where('nama_bahan', $namaBahan)->where('satuan', $satuan)->exists();

        if ($exists) {
            $msg = 'Nama bahan dengan satuan yang sama sudah ada.';
            return $request->expectsJson()
                ? response()->json(['error' => $msg], 422)
                : back()->withInput()->with('error', $msg);
        }

        try {
            $bahan = Bahan::create(['nama_bahan' => $namaBahan, 'satuan' => $satuan]);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan bahan', ['error' => $e->getMessage()]);
            $msg = 'Terjadi kesalahan saat menyimpan bahan.';
            return $request->expectsJson()
                ? response()->json(['error' => $msg], 500)
                : back()->withInput()->with('error', $msg);
        }

        return $request->expectsJson()
            ? response()->json($bahan)
            : redirect()->route('bahans.index')->with('success', "Bahan {$bahan->nama_bahan} ({$bahan->satuan}) berhasil ditambahkan.");
    }

    public function update(Request $request, Bahan $bahan)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
        ]);

        $namaBahan = $this->normalize($request->nama_bahan);
        $satuan = $this->normalize($request->satuan);

        $exists = Bahan::where('nama_bahan', $namaBahan)
            ->where('satuan', $satuan)
            ->where('id', '!=', $bahan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Nama bahan dengan satuan yang sama sudah ada.');
        }

        try {
            $bahan->update(['nama_bahan' => $namaBahan, 'satuan' => $satuan]);
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui bahan', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui bahan.');
        }

        return redirect()->route('bahans.index')->with('success', 'Bahan berhasil diperbarui.');
    }

    public function destroy(Bahan $bahan)
    {
        try {
            $name = $bahan->nama_bahan;
            $bahan->delete();
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus bahan.');
        }

        return redirect()->route('bahans.index')->with('success', "Bahan {$name} berhasil dihapus.");
    }

    private function normalize(string $value): string
    {
        return ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $value))));
    }
}
