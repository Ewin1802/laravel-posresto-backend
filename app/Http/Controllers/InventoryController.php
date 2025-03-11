<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Bahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InventoryController extends Controller
{

    public function index()
    {
        $inventories = Inventory::latest()->paginate(10);
        return view('pages.inventories.index', compact('inventories'));
    }

    public function create() {
        $materials = Bahan::orderBy('nama_bahan', 'asc')->get(); // Ambil semua bahan
        return view('pages.inventories.create', compact('materials'));
    }

    public function store(Request $request) {
        $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'satuan' => 'required|string',
            'amount' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string',
            'receiver' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Ambil bahan berdasarkan bahan_id
        $bahan = Bahan::findOrFail($request->bahan_id);

        // Persiapkan data untuk disimpan
        $data = $request->all();
        $data['nama_bahan'] = $bahan->nama_bahan;
        $data['saldo_awal'] = $request->quantity;
        $data['tanggal_masuk'] = Carbon::now(); // Menyimpan tanggal & jam saat transaksi dibuat

        // Simpan gambar jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory_images', 'public');
        }

        // Simpan data ke database
        Inventory::create($data);

        return redirect()->route('inventories.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }


    public function edit(Inventory $inventory) {
        // Ambil semua bahan untuk dropdown
        $materials = Bahan::all();

        return view('pages.inventories.edit', compact('inventory', 'materials'));
    }

    // Update transaksi inventaris
    public function update(Request $request, Inventory $inventory) {
        $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'amount' => 'required|numeric',
            'saldo_awal' => 'required|integer',
            'quantity' => 'required|integer',
            'supplier' => 'nullable|string',
            'receiver' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Ambil bahan berdasarkan bahan_id
        $bahan = Bahan::findOrFail($request->bahan_id);

        // Persiapkan data untuk diperbarui
        $data = $request->all();
        $data['nama_bahan'] = $bahan->nama_bahan;
        $data['satuan'] = $bahan->satuan;

        // Jika ada gambar baru, hapus gambar lama dan simpan yang baru
        if ($request->hasFile('image')) {
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('inventory_images', 'public');
        }

        // Update data inventaris
        $inventory->update($data);

        return redirect()->route('inventories.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        //
    }
}
