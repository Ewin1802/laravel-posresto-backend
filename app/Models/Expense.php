<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /** Kategori pengeluaran cafe: kunci disimpan di kolom category. */
    public const CATEGORIES = [
        'bahan_baku' => ['label' => 'Bahan baku', 'icon' => 'fa-seedling'],
        'gaji' => ['label' => 'Gaji & upah', 'icon' => 'fa-user-group'],
        'listrik_air' => ['label' => 'Listrik, air & internet', 'icon' => 'fa-bolt'],
        'sewa' => ['label' => 'Sewa tempat', 'icon' => 'fa-house'],
        'gas_bbm' => ['label' => 'Gas & BBM', 'icon' => 'fa-fire-flame-simple'],
        'perlengkapan' => ['label' => 'Perlengkapan & kemasan', 'icon' => 'fa-box'],
        'perawatan' => ['label' => 'Perawatan & perbaikan', 'icon' => 'fa-screwdriver-wrench'],
        'pemasaran' => ['label' => 'Pemasaran', 'icon' => 'fa-bullhorn'],
        'transportasi' => ['label' => 'Transportasi', 'icon' => 'fa-truck'],
        'lainnya' => ['label' => 'Lain-lain', 'icon' => 'fa-ellipsis'],
    ];

    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function categoryLabel(?string $key): string
    {
        return self::CATEGORIES[$key]['label'] ?? ($key ? ucfirst($key) : 'Lain-lain');
    }

    public static function categoryIcon(?string $key): string
    {
        return self::CATEGORIES[$key]['icon'] ?? 'fa-receipt';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabel($this->category);
    }
}
