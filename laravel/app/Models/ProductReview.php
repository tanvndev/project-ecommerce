<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory, QueryScopes;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'parent_id',
        'comment',
        'images',
        'publish',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProductReview::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductReview::class, 'parent_id');
    }
}
