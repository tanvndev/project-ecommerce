<?php

namespace App\Http\Resources\FlashSale\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ClientFlashSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $remainingTime = $this->calculateRemainingTime($this->end_date);
        return [
            'id'            => $this->id,
            'remaining_time'  => $remainingTime,
            'product_variants' => ClientFlashSaleProductVariantResource::collection($this->whenLoaded('product_variants')),
        ];
    }

    private function calculateRemainingTime($endDate)
    {
        $now = Carbon::now();
        $end = Carbon::parse($endDate);

        if ($now < $end) {
            $remainingTime = $now->diff($end);
            return [
                'h' => $remainingTime->h,
                'i' => $remainingTime->i,
                's' => $remainingTime->s,
            ];
        }

        return [
            'h' => 0,
            'i' => 0,
            's' => 0,
        ];
    }
}
