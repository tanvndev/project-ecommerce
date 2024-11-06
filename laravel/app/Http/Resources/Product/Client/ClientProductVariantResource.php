<?php

namespace App\Http\Resources\Product\Client;

use App\Http\Resources\Attribute\AttributeValueResource;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'product_id'              => $this->product_id,
            'name'                    => $this->name,
            'slug'                    => $this->slug,
            'price'                   => $this->price,
            'attribute_value_combine' => $this->attribute_value_combine,
            'image'                   => $this->image ?? [],
            'album'                   => $this->album,
            'sku'                     => $this->sku,
            'price'                   => $this->price,
            'sale_price'              => $this->sale_price ?? null,
            'is_discount_time'        => $this->is_discount_time,
            'sale_price_time'         => [
                $this->sale_price_start_at,
                $this->sale_price_end_at,
            ],
            'discount'                => $this->handleDiscountValue(),
            'stock'                   => $this->stock ?? 0,
            'attributes'              => AttributeValueResource::collection($this->attribute_values),
            'attribute_values'        => $this->attribute_values->pluck('name')->implode(' - ') ?? 'Default',
        ];
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
