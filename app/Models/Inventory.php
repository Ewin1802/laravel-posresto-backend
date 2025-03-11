<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_bahan',
        'bahan_id',
        'amount',
        'saldo_awal',
        'quantity',
        'satuan',
        'supplier',
        'receiver',
        'description',
        'image',
        'tanggal_masuk',
        // 'created_at',
        // 'updated_at'
    ];

    // public $timestamps = true;
    protected $dates = ['tanggal_masuk'];

    public function inventoryOut()
    {
        return $this->hasMany(Inventory_out::class, 'inventory_id');
    }

    public function bahan() {
        return $this->belongsTo(Bahan::class);
    }
}
