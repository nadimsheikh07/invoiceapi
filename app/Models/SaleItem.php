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
        'total',
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

    public function setTotalAttribute()
    {
        $this->attributes['total'] = ((float)$this->price * (float)$this->quantity);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
