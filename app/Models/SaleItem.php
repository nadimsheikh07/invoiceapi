<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'price',
    ];
}
