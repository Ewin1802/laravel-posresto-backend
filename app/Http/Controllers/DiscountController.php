<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('name'));

        $discounts = Discount::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('value')
            ->paginate(12)
            ->withQueryString();

        return view('pages.discounts.index', compact('discounts', 'search'));
    }

    public function create()
    {
        return view('pages.discounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        // Tipe tetap "percentage" (default tabel) karena aplikasi kasir membaca nilai sebagai persen.
        $discount = Discount::create($data);

        return redirect()->route('discounts.index')->with('success', "Diskon {$discount->name} berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $discount = Discount::findOrFail($id);

        return view('pages.discounts.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->update($request->validate($this->rules(), $this->messages()));

        return redirect()->route('discounts.index')->with('success', "Diskon {$discount->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return redirect()->route('discounts.index')->with('error', 'Diskon tidak ditemukan.');
        }

        $discount->delete();

        return redirect()->route('discounts.index')->with('success', "Diskon {$discount->name} telah dihapus.");
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama diskon wajib diisi.',
            'description.required' => 'Keterangan wajib diisi.',
            'value.required' => 'Besar diskon wajib diisi.',
            'value.numeric' => 'Besar diskon harus berupa angka.',
            'value.min' => 'Besar diskon tidak boleh negatif.',
            'value.max' => 'Besar diskon maksimal 100%.',
        ];
    }
}
