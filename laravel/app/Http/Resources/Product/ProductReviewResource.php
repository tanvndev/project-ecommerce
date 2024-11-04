<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'user_id'    => $this->user->id,
            'order_id'   => $this->order_id,
            'fullname'   => $this->user->fullname,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'replies'    => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    return [
                        'user_id'    => $reply->user->id,
                        'fullname'   => $reply->user->fullname,
                        'comment'    => $reply->comment,
                        'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
        ];
    }
}
