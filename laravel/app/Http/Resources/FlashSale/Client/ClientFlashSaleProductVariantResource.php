<?php

namespace App\Http\Resources\FlashSale\Client;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientFlashSaleProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $maxQuantity = $this->pivot->max_quantity;
        $soldQuantity = $this->pivot->sold_quantity;

        $percentSold = $this->calculatePercentSold($maxQuantity, $soldQuantity);

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'image'         => $this->image,
            'max_quantity'  => $maxQuantity,
            'sold_quantity' => $soldQuantity,
            'sale_price'    => $this->sale_price,
            'price'         => $this->price,
            'product_id'    => $this->product_id,
            'slug'          => $this->slug,
            'discount'      => $this->handleDiscountValue(),
            'percent_sold'  => $percentSold,
        ];
    }

    private function calculatePercentSold($maxQuantity, $soldQuantity)
    {
        if ($maxQuantity <= 0) {
            return 0;
        }

        return round(($soldQuantity / $maxQuantity) * 100, 2);
    }

    private function handleDiscountValue()
    {
        if ( ! $this->sale_price || ! $this->price) {
            return null;
        }

        if (
            $this->is_discount_time
            && $this->sale_price_start_at
            && $this->sale_price_end_at
        ) {
            $now = new DateTime;
            $start = new DateTime($this->sale_price_start_at);
            $end = new DateTime($this->sale_price_end_at);

            if ($now < $start || $now > $end) {
                return null;
            }
        }

        $originalPrice = (float) $this->price;
        $discountPrice = (float) $this->sale_price;

        if ($originalPrice <= 0) {
            return null;
        }

        $discountValue = $originalPrice - $discountPrice;
        $discountPercentage = ($discountValue / $originalPrice) * 100;

        return round($discountPercentage, 0);
    }
}
