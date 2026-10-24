<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Inventory_out;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryOutController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $inventory_outs = Inventory_out::query()
            ->with('inventory:id,nama_bahan,satuan,amount')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('receiver', 'like', "%{$search}%")
                        ->orWhereHas('inventory', fn ($i) => $i->where('nama_bahan', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $monthValue = Inventory_out::query()
            ->join('inventories', 'inventories.id', '=', 'inventory_outs.inventory_id')
            ->where('inventory_outs.created_at', '>=', now()->startOfMonth())
            ->selectRaw('COALESCE(SUM(inventory_outs.quantity_out * inventories.amount),0) as v, COUNT(*) as c')
            ->first();

        return view('pages.inventories_out.index', compact('inventory_outs', 'search', 'monthValue'));
    }

    public function create()
    {
        $inventories = Inventory::where('quantity', '>', 0)
            ->orderBy('nama_bahan')
            ->orderBy('tanggal_masuk')
            ->get();

        return view('pages.inventories_out.create', compact('inventories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity_out' => 'required|integer|min:1',
            'receiver' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'inventory_id.required' => 'Pilih bahan yang dikeluarkan.',
            'quantity_out.required' => 'Jumlah keluar wajib diisi.',
            'quantity_out.min' => 'Jumlah keluar minimal 1.',
            'receiver.required' => 'Nama penerima wajib diisi.',
        ]);

        // Dikunci dalam transaksi agar dua input bersamaan tidak membuat stok minus.
        $result = DB::transaction(function () use ($data) {
            $inventory = Inventory::whereKey($data['inventory_id'])->lockForUpdate()->firstOrFail();

            if ($inventory->quantity < $data['quantity_out']) {
                return "Jumlah keluar melebihi stok tersedia ({$inventory->quantity} {$inventory->satuan}).";
            }

            $inventory->decrement('quantity', $data['quantity_out']);
            Inventory_out::create($data);

            return true;
        });

        if ($result !== true) {
            return back()->withInput()->with('error', $result);
        }

        return redirect()->route('inventory_out.index')->with('success', 'Transaksi keluar berhasil disimpan.');
    }
}
