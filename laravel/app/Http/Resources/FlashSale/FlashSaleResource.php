<?php

namespace App\Http\Resources\FlashSale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product\FlashSaleProductVariantResource;

class FlashSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'start_date'    => \Carbon\Carbon::parse($this->start_date)->format('d/m/Y'),
            'end_date'      => \Carbon\Carbon::parse($this->end_date)->format('d/m/Y'),
            'publish'       => $this->publish,
            'productVariants' => FlashSaleProductVariantResource::collection($this->whenLoaded('productVariants')), // Load related data if present
        ];
    }
}
