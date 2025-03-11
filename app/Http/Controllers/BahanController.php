<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bahan;
use Illuminate\Support\Facades\Log; // Tambahkan untuk logging

class BahanController extends Controller
{
    public function index() {
        $bahans = Bahan::latest()->paginate(10); // Ambil semua bahan dengan pagination
        return view('pages.bahan.index', compact('bahans'));
    }

    public function edit(Bahan $bahan) {
        return view('pages.bahan.edit', compact('bahan'));
    }

    public function store(Request $request) {
        try {
            // Log request untuk debugging
            Log::info('Data diterima:', $request->all());

            // Validasi input sebelum melakukan pengecekan database
            $request->validate([
                'nama_bahan' => 'required|string|max:255',
                'satuan' => 'required|string|max:50'
            ]);

            // Normalisasi input (format title case)
            $namaBahan = ucwords(strtolower(trim($request->nama_bahan)));
            $satuan = ucwords(strtolower(trim($request->satuan)));

            // Cek apakah kombinasi nama_bahan dan satuan sudah ada
            $existingBahan = Bahan::where('nama_bahan', $namaBahan)
                ->where('satuan', $satuan) // Sekarang cek kombinasi yang sama
                ->exists();

            if ($existingBahan) {
                return response()->json(['error' => 'Nama bahan dengan satuan yang sama sudah ada.'], 422);
            }

            // Simpan ke database jika kombinasi belum ada
            $bahan = Bahan::create([
                'nama_bahan' => $namaBahan,
                'satuan' => $satuan,
            ]);

            // Log sukses
            Log::info('Bahan berhasil disimpan:', $bahan->toArray());

            return response()->json($bahan);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Gagal menyimpan bahan:', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan bahan.'], 500);
        }
    }

    public function update(Request $request, Bahan $bahan) {
        try {
            // Validasi input berdasarkan kombinasi nama_bahan & satuan
            $request->validate([
                'nama_bahan' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
            ]);

            // Normalisasi input
            $namaBahan = ucwords(strtolower(trim($request->nama_bahan)));
            $satuan = ucwords(strtolower(trim($request->satuan)));

            // Cek apakah kombinasi nama_bahan dan satuan sudah ada di data lain
            $existingBahan = Bahan::where('nama_bahan', $namaBahan)
                ->where('satuan', $satuan)
                ->where('id', '!=', $bahan->id) // Hindari cek terhadap dirinya sendiri
                ->exists();

            if ($existingBahan) {
                return redirect()->back()->with('error', 'Nama bahan dengan satuan yang sama sudah ada.');
            }

            // Update data bahan
            $bahan->update([
                'nama_bahan' => $namaBahan,
                'satuan' => $satuan,
            ]);

            return redirect()->route('bahans.index')->with('success', 'Bahan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui bahan.');
        }
    }


    public function destroy(Bahan $bahan) {
        try {
            $bahan->delete();
            return redirect()->route('bahans.index')->with('success', 'Bahan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus bahan.');
        }
    }

}
