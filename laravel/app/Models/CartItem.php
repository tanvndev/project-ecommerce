<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory,QueryScopes;

    protected $fillable = ['cart_id', 'product_variant_id', 'quantity', 'is_selected'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    protected $casts = [
        'is_selected' => 'boolean',
    ];
}
