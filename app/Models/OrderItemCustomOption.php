<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemCustomOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'custom_option_id',
        'quantity',
        'extra_price',
    ];

    public $timestamps = false;
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customOption()
    {
        return $this->belongsTo(CustomOption::class);
    }

}
