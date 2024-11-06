<?php

namespace App\Http\Resources\FlashSale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'image'       => $this->image,
            'max_quantity' => $this->pivot->max_quantity,
            'sold_quantity' => $this->pivot->sold_quantity,
            'sale_price' => $this->sale_price,
        ];
    }
}
