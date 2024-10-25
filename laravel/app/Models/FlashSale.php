<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{

    use HasFactory, QueryScopes;

    protected $fillable = ['name', 'start_date', 'end_date', 'publish'];

    public function product_variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'flash_sale_product_variants')
            ->withPivot('max_quantity', 'sold_quantity', 'sale_price')
            ->withTimestamps();
    }
}
