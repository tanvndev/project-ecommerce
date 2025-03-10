<?php

namespace App\Http\Resources\Product\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;

        return [
            'product_id'    => $this->product_id,
            'fullname'      => $user->fullname,
            'image'         => $user->image,
            'images'        => $this->images ?? '[]',
            'rating'        => $this->rating,
            'percent_rate'  => starsToPercent($this->rating),
            'comment'       => $this->comment,
            'created_at'    => $this->created_at->format('d-m-Y H:i:s'),
            'replies'       => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    return [
                        'fullname'   => $reply->user->fullname,
                        'image'      => $reply->user->image,
                        'comment'    => $reply->comment,
                        'created_at' => $reply->created_at->format('d-m-Y H:i:s'),
                    ];
                });
            })->first(),
        ];
    }
}
