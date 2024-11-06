<?php

namespace App\Http\Resources\Cart;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'         => $this->product_variant->product_id,
            'product_variant_id' => $this->product_variant_id,
            'image'              => $this->product_variant->image,
            'name'               => $this->product_variant->name,
            'slug'               => $this->product_variant->slug,
            'price'              => $this->product_variant->price,
            'stock'              => $this->product_variant->stock,
            'attributes'         => implode(', ', $this->product_variant->attribute_values->pluck('name')->toArray()) ?? 'Mặc định',
            'quantity'           => $this->quantity,
            'sale_price'         => $this->handleSalePrice(),
            'is_selected'        => $this->is_selected,
            'sub_total'          => $this->getSubTotal(),
        ];
    }

    private function handleSalePrice()
    {
        $productVariant = $this->product_variant;
        if (!$productVariant->sale_price || !$productVariant->price) {
            return null;
        }

        // if (!$productVariant->is_discount_time) {
        //     return $productVariant->sale_price;
        // }

        if ($productVariant->sale_price_start_at && $productVariant->sale_price_end_at) {
            $now = new \DateTime();
            $start = new \DateTime($productVariant->sale_price_start_at);
            $end = new \DateTime($productVariant->sale_price_end_at);

            if ($now < $start || $now > $end) {
                return null;
            }
        }


        return $productVariant->sale_price;
    }


    public function getSubTotal()
    {
        $salePrice = $this->handleSalePrice();
        $subTotal = ($salePrice ?? $this->product_variant->price) * $this->quantity;

        return $subTotal;
    }
}
