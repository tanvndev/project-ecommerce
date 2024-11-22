<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartAction extends Model
{
    use HasFactory, QueryScopes;

    const ACTION_ADDED = 'added';

    const ACTION_REMOVED = 'removed';

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'action',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
