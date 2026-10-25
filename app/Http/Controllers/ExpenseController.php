<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('expenses.index')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $category = $request->input('category');
        $search = trim((string) $request->input('q'));

        $base = Expense::query()->whereBetween('expense_date', [$start_date, $end_date]);

        // Ringkasan per kategori (tidak terpengaruh filter kategori/pencarian).
        $byCategory = (clone $base)
            ->selectRaw('category, COUNT(*) as items, COALESCE(SUM(amount),0) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $total = (float) $byCategory->sum('total');
        $count = (int) $byCategory->sum('items');

        $days = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date)) + 1;

        $expenses = (clone $base)
            ->with('user:id,name')
            ->when($category && array_key_exists($category, Expense::CATEGORIES), fn ($q) => $q->where('category', $category))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('description', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.expenses.index', [
            'expenses' => $expenses,
            'byCategory' => $byCategory,
            'total' => $total,
            'count' => $count,
            'perDay' => $days > 0 ? $total / $days : 0,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'category' => $category,
            'search' => $search,
            'categories' => Expense::CATEGORIES,
        ]);
    }

    public function create(Request $request)
    {
        return view('pages.expenses.create', [
            'categories' => Expense::CATEGORIES,
            'defaultCategory' => $request->input('category', 'bahan_baku'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $data['user_id'] = auth()->id();

        $expense = Expense::create($data);

        $msg = 'Pengeluaran "' . $expense->description . '" sebesar Rp ' . number_format($expense->amount, 0, ',', '.') . ' tersimpan.';

        // Tombol "Simpan & catat lagi" untuk input beruntun (misal belanja harian).
        if ($request->boolean('again')) {
            return redirect()
                ->route('expenses.create', ['category' => $expense->category])
                ->with('success', $msg)
                ->withInput(['expense_date' => $expense->expense_date->toDateString()]);
        }

        return redirect()->route('expenses.index', $this->rangeFor($expense))->with('success', $msg);
    }

    public function edit(Expense $expense)
    {
        return view('pages.expenses.edit', [
            'expense' => $expense->load('user:id,name'),
            'categories' => Expense::CATEGORIES,
            'defaultCategory' => $expense->category,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($request->validate($this->rules(), $this->messages()));

        return redirect()->route('expenses.index', $this->rangeFor($expense))
            ->with('success', 'Pengeluaran "' . $expense->description . '" diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        $range = $this->rangeFor($expense);
        $label = $expense->description;
        $expense->delete();

        return redirect()->route('expenses.index', $range)->with('success', "Pengeluaran \"{$label}\" dihapus.");
    }

    /** Arahkan kembali ke bulan transaksi agar data yang baru disimpan langsung terlihat. */
    private function rangeFor(Expense $expense): array
    {
        $d = Carbon::parse($expense->expense_date);

        return [
            'start_date' => $d->copy()->startOfMonth()->toDateString(),
            'end_date' => $d->copy()->endOfMonth()->toDateString(),
        ];
    }

    private function rules(): array
    {
        return [
            'category' => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'expense_date' => ['required', 'date', 'before_or_equal:' . now()->addDay()->toDateString()],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function messages(): array
    {
        return [
            'category.required' => 'Pilih kategori pengeluaran.',
            'category.in' => 'Kategori tidak valid.',
            'description.required' => 'Tulis untuk apa uang ini dikeluarkan.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah minimal Rp 1.',
            'expense_date.required' => 'Tanggal wajib diisi.',
            'expense_date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        ];
    }
}
