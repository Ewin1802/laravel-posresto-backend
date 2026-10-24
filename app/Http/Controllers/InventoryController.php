<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $stock = $request->input('stock');

        $inventories = Inventory::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('nama_bahan', 'like', "%{$search}%")
                        ->orWhere('supplier', 'like', "%{$search}%")
                        ->orWhere('receiver', 'like', "%{$search}%");
                });
            })
            ->when($stock === 'empty', fn ($q) => $q->where('quantity', '<=', 0))
            ->when($stock === 'low', fn ($q) => $q->where('quantity', '>', 0)->whereRaw('quantity <= saldo_awal * 0.2'))
            ->when($stock === 'available', fn ($q) => $q->where('quantity', '>', 0))
            ->latest('tanggal_masuk')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $summary = Inventory::query()
            ->selectRaw('COUNT(*) as batches, COALESCE(SUM(quantity * amount),0) as stock_value, SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as empty_count')
            ->first();

        return view('pages.inventories.index', compact('inventories', 'search', 'stock', 'summary'));
    }

    public function create()
    {
        $materials = Bahan::orderBy('nama_bahan')->get();

        return view('pages.inventories.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'receiver' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ], $this->messages());

        $bahan = Bahan::findOrFail($request->bahan_id);

        $data = $request->only(['bahan_id', 'amount', 'quantity', 'supplier', 'receiver', 'description']);
        $data['nama_bahan'] = $bahan->nama_bahan;
        $data['satuan'] = $bahan->satuan;           // selalu dari master bahan
        $data['saldo_awal'] = $request->quantity;    // jumlah pembelian awal
        $data['tanggal_masuk'] = Carbon::now();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory_images', 'public');
        }

        Inventory::create($data);

        return redirect()->route('inventories.index')->with('success', "Persediaan {$bahan->nama_bahan} berhasil dicatat.");
    }

    public function edit(Inventory $inventory)
    {
        $materials = Bahan::orderBy('nama_bahan')->get();
        $inventory->loadSum('inventoryOut as total_keluar', 'quantity_out');

        return view('pages.inventories.edit', compact('inventory', 'materials'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        // Catatan: form lama tidak mengirim saldo_awal padahal wajib, sehingga
        // perubahan selalu gagal validasi. Sekarang saldo_awal opsional.
        $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'amount' => 'required|numeric|min:0',
            'saldo_awal' => 'nullable|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'receiver' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ], $this->messages());

        $bahan = Bahan::findOrFail($request->bahan_id);

        $data = $request->only(['bahan_id', 'amount', 'quantity', 'supplier', 'receiver', 'description']);
        $data['nama_bahan'] = $bahan->nama_bahan;
        $data['satuan'] = $bahan->satuan;
        if ($request->filled('saldo_awal')) {
            $data['saldo_awal'] = (int) $request->saldo_awal;
        }

        if ($request->hasFile('image')) {
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('inventory_images', 'public');
        }

        $inventory->update($data);

        return redirect()->route('inventories.index')->with('success', "Persediaan {$bahan->nama_bahan} berhasil diperbarui.");
    }

    private function messages(): array
    {
        return [
            'bahan_id.required' => 'Pilih bahan terlebih dahulu.',
            'bahan_id.exists' => 'Bahan tidak ditemukan.',
            'amount.required' => 'Harga per satuan wajib diisi.',
            'amount.numeric' => 'Harga harus berupa angka.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.integer' => 'Jumlah harus angka bulat.',
            'quantity.min' => 'Jumlah tidak valid.',
            'image.image' => 'Bukti transaksi harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
