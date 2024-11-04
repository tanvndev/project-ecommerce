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

        $flashSaleProductVariant = DB::table('flash_sale_product_variants')
            ->join('flash_sales', 'flash_sale_product_variants.flash_sale_id', '=', 'flash_sales.id')
            ->where('flash_sale_product_variants.product_variant_id', $productVariant->id)
            ->where('flash_sales.start_date', '<=', now())
            ->where('flash_sales.end_date', '>=', now())
            ->where('flash_sales.publish', true)
            ->where('flash_sale_product_variants.max_quantity', '>', 0)
            ->first();


        if ($flashSaleProductVariant) {
            return $flashSaleProductVariant->sale_price;
        }

        return null;
    }


    public function getSubTotal()
    {
        $salePrice = $this->handleSalePrice();
        $subTotal = ($salePrice ?? $this->product_variant->price) * $this->quantity;

        return $subTotal;
    }
}
