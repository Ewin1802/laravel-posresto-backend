<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory_out extends Model
{
    use HasFactory;
    protected $table = 'inventory_outs';
    protected $fillable = ['inventory_id', 'quantity_out', 'receiver', 'description'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
