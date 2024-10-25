<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory,QueryScopes;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'uuid',
        'product_variant_name',
        'quantity',
        'price',
        'sale_price',
        'cost_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
