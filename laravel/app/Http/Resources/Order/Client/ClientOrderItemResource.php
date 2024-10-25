<?php

namespace App\Http\Resources\Order\Client;

use App\Http\Resources\Product\Client\ClientProductReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'                            => $this->id,
            'order_id'                      => $this->order_id,
            'product_variant_id'            => $this->product_variant_id,
            'uuid'                          => $this->uuid,
            'product_variant_name'          => $this->product_variant_name,
            'quantity'                      => $this->quantity,
            'price'                         => $this->price,
            'sale_price'                    => $this->sale_price,
            'image'                         => $this->product_variant->image ?? '',
            'slug'                          => $this->product_variant->slug ?? '',
            'product_id'                    => $this->product_variant->product_id ?? '',
            'attribute_values'              => $this->product_variant->attribute_values->pluck('name')->implode(' - ') ?? 'Default',
            'is_reviewed'                   => $this->hasUserReviewed($user, $this->product_variant->product_id, $this->order_id)
        ];
    }

    private function hasUserReviewed($user, $productVariantId, $orderId): bool
    {
        if ($user) {
            return $user->product_reviews()
                ->where('product_id', $productVariantId)
                ->where('order_id', $orderId)
                ->exists();
        }

        return false;
    }
}
