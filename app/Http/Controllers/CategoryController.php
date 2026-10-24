<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('name'));

        $categories = Category::query()
            ->withCount('products')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('pages.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('pages.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ], $this->messages());

        // Simpan dulu agar ID tersedia untuk nama file gambar.
        // (Versi lama menamai file sebelum ID ada, sehingga semua gambar bernama ".jpg" dan saling menimpa.)
        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $category->image = $this->storeImage($request, $category);
        $category->save();

        return redirect()->route('categories.index')->with('success', "Kategori {$category->name} berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        return view('pages.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ], $this->messages());

        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;

        if ($request->hasFile('image')) {
            $category->image = $this->storeImage($request, $category);
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', "Kategori {$category->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->find($id);

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak ditemukan.');
        }

        if ($category->products_count > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Kategori {$category->name} masih dipakai {$category->products_count} produk. Pindahkan produknya terlebih dahulu.");
        }

        try {
            $this->deleteImage($category->image);
            $category->delete();
        } catch (QueryException $e) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    /** Simpan gambar dengan pola lama: storage/categories/{id}.{ext} */
    private function storeImage(Request $request, Category $category): ?string
    {
        if (!$request->hasFile('image')) {
            return $category->image;
        }

        $image = $request->file('image');
        $ext = strtolower($image->getClientOriginalExtension() ?: $image->extension());

        // Hapus file lama bila ekstensinya berbeda (yang sama akan tertimpa).
        if ($category->image && $category->image !== "storage/categories/{$category->id}.{$ext}") {
            $this->deleteImage($category->image);
        }

        Storage::disk('public')->putFileAs('categories', $image, "{$category->id}.{$ext}");

        return "storage/categories/{$category->id}.{$ext}";
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
            'name.required' => 'Nama kategori wajib diisi.',
            'image.required' => 'Foto kategori wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, svg, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
