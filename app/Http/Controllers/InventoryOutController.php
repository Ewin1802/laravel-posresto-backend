<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory_out; // Import model
use App\Models\Inventory; // Import model Inventory jika digunakan

class InventoryOutController extends Controller
{
    public function index()
    {
        $inventory_outs = Inventory_out::with('inventory')->latest()->paginate(10);
        return view('pages.inventories_out.index', compact('inventory_outs'));
    }

    public function create()
    {
        $inventories = Inventory::where('quantity', '>', 0)->get(); // Hanya ambil bahan dengan stok tersedia
        return view('pages.inventories_out.create', compact('inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity_out' => 'required|integer|min:1',
            'receiver' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->quantity < $request->quantity_out) {
            return back()->with('error', 'Jumlah keluar melebihi stok yang tersedia.');
        }

        // Kurangi stok di inventory
        $inventory->decrement('quantity', $request->quantity_out);

        // Simpan data transaksi keluar
        Inventory_out::create($request->all());

        return redirect()->route('inventory_out.index')->with('success', 'Transaksi keluar berhasil disimpan.');
    }
}

