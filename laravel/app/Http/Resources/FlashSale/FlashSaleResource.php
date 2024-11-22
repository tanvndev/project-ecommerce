<?php

namespace App\Http\Resources\FlashSale;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'                   => $this->id,
            'key'                  => $this->id,
            'name'                 => $this->name,
            'origin_start_date'    => $this->start_date,
            'origin_end_date'      => $this->end_date,
            'start_date'           => Carbon::parse($this->start_date)->format('d/m/Y H:i:s'),
            'end_date'             => Carbon::parse($this->end_date)->format('d/m/Y H:i:s'),
            'publish'              => $this->publish,
            'product_variants'     => FlashSaleProductVariantResource::collection($this->whenLoaded('product_variants')),
            'status'               => $this->getStatus(),
        ];
    }

    private function getStatus()
    {
        $now = now();

        if ($this->start_date && $this->end_date) {
            if ($now->isBefore(Carbon::parse($this->start_date))) {
                return [
                    'text'  => 'Chưa diễn ra',
                    'color' => 'orange',
                ];
            } elseif ($now->isBetween(Carbon::parse($this->start_date), Carbon::parse($this->end_date), true)) {
                return [
                    'text'  => 'Đang diễn ra',
                    'color' => 'green',
                ];
            } elseif ($now->isAfter(Carbon::parse($this->end_date))) {
                return [
                    'text'  => 'Đã kết thúc',
                    'color' => 'red',
                ];
            }
        }

        return 'unknown';
    }
}
