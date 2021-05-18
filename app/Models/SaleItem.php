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

    protected $appends = ['item_name', 'total'];

    public function getItemNameAttribute()
    {
        $name = '';
        if ($this->item) {
            $name = $this->item->name;
        }
        return $name;
    }
    public function getTotalAttribute()
    {
        $value = 0;
        if ($this->quantity && $this->price) {
            $value = ($this->quantity * $this->price);
        }
        return $value;
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
