<?php

namespace App\Http\Resources\Product\Client;

use App\Http\Resources\Product\ProductCatalogueResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $upsellIds = $this->upsell_ids ?? [];
        $productVariants = ProductVariant::whereIn('id', $upsellIds)
            ->whereHas('product', function ($query) {
                $query->where('publish', 1);
            })
            ->get();

        return [
            'id'                        => $this->id,
            'key'                       => $this->id,
            'name'                      => $this->name,
            'brand_id'                  => $this->brand_id,
            'brand_name'                => $this->brand->name,
            'description'               => $this->description,
            'excerpt'                   => $this->excerpt,
            'upsells'                   => ClientProductVariantResource::collection($productVariants),
            'canonical'                 => $this->canonical,
            'meta_title'                => $this->meta_title,
            'meta_description'          => $this->meta_description,
            'variants'                  => ClientProductVariantResource::collection($this->variants),
            'catalogues'                => ProductCatalogueResource::collection($this->catalogues),
            'attribute_not_enabled'     => ClientProductAttributeResource::collection($this->attributes->where('enable_variation', false)),
            'attribute_not_enabled_ids' => $this->attributes->where('enable_variation', false)->pluck('attribute_id'),
            'attribute_enabled'         => ClientProductAttributeResource::collection($this->attributes->where('enable_variation', true)),
            'attribute_enabled_ids'     => $this->attributes->where('enable_variation', true)->pluck('attribute_id'),
            'product_catalogue_ids'     => $this->catalogues->pluck('id'),
        ];
    }
}
