<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $products = \App\Models\Product::orderBy('id', 'desc')->get();

        $products->load('category');
        return response()->json([
            'success' => true,
            'message' => 'List Data Product',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|min:3',
    //         'price' => 'required|integer',
    //         'stock' => 'required|integer',
    //         'category_id' => 'required',
    //         'is_best_seller' => 'required',
    //         'image' => 'required|image|mimes:png,jpg,jpeg'
    //     ]);

    //     $filename = time() . '.' . $request->image->extension();

    //     $product = \App\Models\Product::create([
    //         'name' => $request->name,
    //         'price' => (int) $request->price,
    //         'stock' => (int) $request->stock,
    //         'category_id' => $request->category_id,
    //         'is_best_seller' => $request->is_best_seller,
    //         'image' => $filename,

    //     ]);

    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
    //         $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
    //         $product->save();
    //     }

    //     if ($product) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Product Created',
    //             'data' => $product
    //         ], 201);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Product Failed to Save',
    //         ], 409);
    //     }
    // }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'nullable|string',
            'price' => 'required|integer', // Pastikan validasi mengharuskan integer
            'stock' => 'required|integer',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'status' => 'required|in:1,0',
            'is_favorite' => 'required|in:1,0',
        ]);

        // Simpan data ke database
        $product = \App\Models\Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price, // Nilai asli sudah berupa angka murni
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'is_favorite' => $request->is_favorite,
        ]);

        // Handle upload gambar jika ada
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->image = 'products/' . $filename;
            $product->save();
        }

        // Redirect dengan pesan sukses
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }



    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request,)
    // {
    //     $request->validate([
    //         'id' => 'required',
    //         'name' => 'required',
    //         'price' => 'required|numeric',
    //         'stock' => 'required|numeric',
    //         'category_id' => 'required',
    //         'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
    //     ]);
    //     $product = \App\Models\Product::findOrFail($request->id);
    //     $product->name = $request->name;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     if ($request->hasFile('image')) {
    //         Storage::delete('public/products/' . $product->image);
    //         $filename = time() . '.' . $request->image->extension();
    //         $request->image->storeAs('public/products', $filename);
    //         $product->image = $filename;
    //     }
    //     $product->save();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Product Updated',
    //         'data' => $product
    //     ]);
    // }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'price' => 'required|numeric', // Pastikan validasi sebagai angka
            'stock' => 'required|numeric',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Ambil produk berdasarkan ID
        $product = \App\Models\Product::findOrFail($request->id);

        // Update data produk
        $product->name = $request->name;
        $product->price = (int) $request->price; // Nilai asli sudah berupa angka tanpa pemisah ribuan
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;

        // Handle upload gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }

            // Simpan file baru
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->image = $filename;
        }

        // Simpan perubahan ke database
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product Updated',
            'data' => $product
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        Storage::delete('public/products/' . $product->image);
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product Deleted',
        ]);
    }
}
