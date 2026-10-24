<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('name'));
        $categoryId = $request->input('category');
        $status = $request->input('status');

        $products = Product::query()
            ->with('category:id,name')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(in_array($status, ['0', '1'], true), fn ($q) => $q->where('status', (int) $status))
            ->orderByDesc('is_favorite')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Jumlah terjual 30 hari terakhir untuk produk di halaman ini.
        $sold = DB::table('order_items')
            ->whereIn('product_id', $products->pluck('id'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->pluck('qty', 'product_id');

        return view('pages.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'sold' => $sold,
            'search' => $search,
            'categoryId' => $categoryId,
            'status' => $status,
            'totals' => [
                'all' => Product::count(),
                'active' => Product::where('status', 1)->count(),
            ],
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('pages.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true), $this->messages());

        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'category_id' => $data['category_id'],
                'stock' => $data['stock'],
                'status' => $data['status'],
                'is_favorite' => $data['is_favorite'],
            ]);

            $product->image = $this->storeImage($request, $product);
            $product->save();

            return $product;
        });

        return redirect()->route('products.index')->with('success', "Produk {$product->name} berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('pages.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validate($this->rules(false), $this->messages());

        $product->fill([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'stock' => $data['stock'],
            'status' => $data['status'],
            'is_favorite' => $data['is_favorite'],
        ]);

        if ($request->hasFile('image')) {
            $product->image = $this->storeImage($request, $product);
        }

        $product->save();

        return redirect()->route('products.index')->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
        }

        $name = $product->name;
        $image = $product->image;

        // Perilaku lama dipertahankan: item order terkait ikut dihapus agar
        // foreign key tidak menghalangi. Dibungkus transaksi supaya atomik.
        DB::transaction(function () use ($product) {
            DB::table('order_items')->where('product_id', $product->id)->delete();
            $product->delete();
        });

        $this->deleteImage($image);

        return redirect()->route('products.index')->with('success', "Produk {$name} telah dihapus.");
    }

    private function rules(bool $creating): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
            'is_favorite' => ['required', 'boolean'],
            'image' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /** Pola penyimpanan lama dipertahankan: storage/products/{id}.{ext} (dipakai aplikasi kasir). */
    private function storeImage(Request $request, Product $product): ?string
    {
        if (!$request->hasFile('image')) {
            return $product->image;
        }

        $image = $request->file('image');
        $ext = strtolower($image->getClientOriginalExtension() ?: $image->extension());
        $newPath = "storage/products/{$product->id}.{$ext}";

        if ($product->image && $product->image !== $newPath) {
            $this->deleteImage($product->image);
        }

        Storage::disk('public')->putFileAs('products', $image, "{$product->id}.{$ext}");

        return $newPath;
    }

    private function deleteImage(?string $publicPath): void
    {
        if ($publicPath && str_starts_with($publicPath, 'storage/')) {
            Storage::disk('public')->delete(substr($publicPath, strlen('storage/')));
        }
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'category_id.required' => 'Pilih kategori produk.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'status.required' => 'Pilih status produk.',
            'is_favorite.required' => 'Tentukan apakah produk favorit.',
            'image.required' => 'Foto produk wajib diunggah.',
            'image.image' => 'File harus berupa gambar (jpeg, png, jpg, webp).',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
