<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'price',
    ];

    protected $appends = ['item_name'];

    public function getItemNameAttribute()
    {
        $name = '';
        if ($this->item) {
            $name = $this->item->name;
        }
        return $name;
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
