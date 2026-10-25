<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /** Kategori pengeluaran cafe: kunci disimpan di kolom category. */
    public const CATEGORIES = [
        'bahan_baku' => ['label' => 'Bahan baku', 'icon' => 'fa-seedling',
            'hint' => 'Kopi, susu, gula, sirup, es batu, sayur, daging, dan bahan lain yang habis dipakai untuk menu.'],
        'gaji' => ['label' => 'Gaji & upah', 'icon' => 'fa-user-group',
            'hint' => 'Gaji barista dan kasir, upah harian, uang lembur, bonus, uang makan karyawan.'],
        'listrik_air' => ['label' => 'Listrik, air & internet', 'icon' => 'fa-bolt',
            'hint' => 'Token atau tagihan PLN, PDAM, WiFi, pulsa dan paket data operasional.'],
        'sewa' => ['label' => 'Sewa tempat', 'icon' => 'fa-house',
            'hint' => 'Sewa lapak atau bangunan, retribusi kawasan wisata, iuran kebersihan.'],
        'gas_bbm' => ['label' => 'Gas & BBM', 'icon' => 'fa-fire-flame-simple',
            'hint' => 'Isi ulang tabung gas, bensin atau solar genset.'],
        'perlengkapan' => ['label' => 'Perlengkapan & kemasan', 'icon' => 'fa-box',
            'hint' => 'Cup, sedotan, tutup gelas, kantong, tisu, sabun cuci, alat makan sekali pakai.'],
        'perawatan' => ['label' => 'Perawatan & perbaikan', 'icon' => 'fa-screwdriver-wrench',
            'hint' => 'Servis mesin kopi, perbaikan kulkas atau blender, cat ulang, ganti lampu.'],
        'pemasaran' => ['label' => 'Pemasaran', 'icon' => 'fa-bullhorn',
            'hint' => 'Iklan Instagram, cetak menu atau spanduk, promo, endorse.'],
        'transportasi' => ['label' => 'Transportasi', 'icon' => 'fa-truck',
            'hint' => 'Ongkos kirim belanja, sewa mobil angkut, ojek untuk beli bahan.'],
        'lainnya' => ['label' => 'Lain-lain', 'icon' => 'fa-ellipsis',
            'hint' => 'Biaya yang tidak masuk kategori lain. Tulis keterangan sejelas mungkin.'],
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
