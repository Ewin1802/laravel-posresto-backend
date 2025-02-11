<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id') // Join ke tabel categories
            ->select('products.*', 'categories.name as category_name') // Pilih kolom yang diperlukan
            ->when($request->input('name'), function ($query, $name) {
                $query->where('products.name', 'like', '%' . $name . '%'); // Filter berdasarkan nama produk
            })
            ->paginate(10);

        return view('pages.products.index', compact('products'));
    }

    // create
    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('pages.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error kustom
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'The product name is required.',
            'description.required' => 'The description is required.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'category_id.required' => 'Please select a category.',
            'stock.required' => 'The stock is required.',
            'stock.numeric' => 'The stock must be a valid number.',
            'status.required' => 'The product status is required.',
            'is_favorite.required' => 'Please select if the product is a favorite.',
            'image.required' => 'The product image is required.',
            'image.image' => 'The file must be an image (jpeg, png, jpg).',
            'image.mimes' => 'The image must be in jpeg, png, or jpg format.',
            'image.max' => 'The image size must not exceed 2MB.',
        ]);

        // Simpan data produk
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        // Simpan gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }


    public function show($id)
    {
        return view('pages.products.show');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    // public function update(Request $request, $id)
    // {
    //     // Validasi termasuk gambar
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required',
    //         'stock' => 'required|numeric',
    //         'status' => 'required|boolean',
    //         'is_favorite' => 'required|boolean',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi gambar opsional
    //     ]);

    //     // Update data produk
    //     $product = Product::findOrFail($id);
    //     $product->name = $request->name;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     $product->status = $request->status;
    //     $product->is_favorite = $request->is_favorite;
    //     $product->save();

    //     // Simpan gambar jika ada
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
    //         $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
    //         $product->save();
    //     }

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully');
    // }

    public function update(Request $request, $id)
    {
        // Validasi termasuk gambar
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil data produk
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;

        // Jika ada gambar baru yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            // Simpan gambar baru dengan nama unik
            $imagePath = $request->file('image')->store('public/products');
            $product->image = str_replace('public/', 'storage/', $imagePath);
        }

        // Simpan perubahan ke database
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    // destroy
    // public function destroy($id)
    // {
    //     // delete the request...
    //     $product = Product::find($id);
    //     $product->delete();

    //     return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    // }
    public function destroy($id)
    {
        // 🔥 Hapus semua order_items yang terkait sebelum menghapus produk
        DB::table('order_items')->where('product_id', $id)->delete();

        // Cari produk berdasarkan ID
        $product = Product::find($id);

        // Jika produk ditemukan, hapus
        if ($product) {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        }

        return redirect()->route('products.index')->with('error', 'Product not found');
    }

}
