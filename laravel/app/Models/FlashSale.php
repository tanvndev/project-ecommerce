<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FlashSale extends Model
{

    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = ['name', 'start_date', 'end_date', 'publish'];

    public function product_variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'flash_sale_product_variants')
            ->withPivot('max_quantity', 'sold_quantity')
            ->withTimestamps();
    }

    /**
     * Check if the flash sale is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = Carbon::now();

        return $now->isBetween(Carbon::parse($this->start_date), Carbon::parse($this->end_date), true);
    }

    /**
     * Check if purchases can still be made.
     *
     * @param string $variantId
     * @return bool
     */
    public function canPurchase(string $variantId): bool
    {
        return DB::transaction(function () use ($variantId) {
            $variant = $this->product_variants()
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if ($variant) {
                return $variant->pivot->sold_quantity < $variant->pivot->max_quantity;
            }

            return false;
        });
    }
}
