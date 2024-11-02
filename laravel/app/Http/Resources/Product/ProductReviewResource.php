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
            'key'           => $this->id,
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'product_name'  => $this->product->name,
            'product_image' => $this->product->variants,
            'user_id'       => $this->user->id,
            'order_id'      => $this->order_id,
            'order_code'    => $this->order->code,
            'fullname'      => $this->user->fullname,
            'rating'        => $this->rating,
            'comment'       => $this->comment,
            'images'        => $this->images,
            'publish'       => $this->publish,
            'average_rating' => $this->product->reviews()->avg('rating'),
            'created_at'    => \Carbon\Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'replies'       => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    return [
                        'user_id'    => $reply->user->id,
                        'fullname'   => $reply->user->fullname,
                        'comment'    => $reply->comment,
                        'created_at' => \Carbon\Carbon::parse($reply->created_at)->format('d-m-Y H:i:s'),
                    ];
                });
            }),
        ];
    }
}
