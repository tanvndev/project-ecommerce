<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory, QueryScopes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'product_id',
        'attribute_value_combine',
        'sku',
        'weight',
        'length',
        'width',
        'height',
        'image',
        'album',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'is_used',
        'low_stock_amount',
        'is_discount_time',
        'sale_price_start_at',
        'sale_price_end_at',
    ];

    protected $casts = [
        'album'            => 'json',
        'is_discount_time' => 'boolean',
        'is_used'          => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = self::generateUniqueSlug($model->name);
        });

        static::updating(function ($model) {
            $model->slug = self::generateUniqueSlug($model->name, $model->id);

            $model->sale_price_start_at = formatIso8601ToDatetime($model->sale_price_start_at);
            $model->sale_price_end_at = formatIso8601ToDatetime($model->sale_price_end_at);
        });
    }

    public static function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            self::where('slug', $slug)
            ->where('id', '!=', $excludeId)
            ->exists()
        ) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute_values()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value', 'product_variant_id', 'attribute_value_id');
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    //relationship Wishlist
    public function wishLists()
    {
        return $this->hasMany(WishList::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function flashSales()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_product_variants')
            ->withPivot('max_quantity', 'sold_quantity', 'sale_price')
            ->withTimestamps();
    }

    public function product_views()
    {
        return $this->hasMany(ProductView::class);
    }
}
