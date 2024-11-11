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
            'product_image' => $this->product->variants->first()->image,
            'user_id'       => $this->user->id,
            'order_id'      => $this->order_id,
            'order_code'    => $this->order->code,
            'fullname'      => $this->user->fullname,
            'rating'        => $this->rating,
            'comment'       => $this->comment,
            'images'        => $this->images,
            'publish'       => $this->publish,
            'status'       => $this->getStatus(),
            'average_rating' => $this->product->reviews()->avg('rating'),
            'rating_count'  => $this->product->reviews()->whereNull('parent_id')->count(),
            'created_at'    => $this->created_at->format('d-m-Y H:i:s'),
            'replies'       => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    return [
                        'fullname'   => $reply->user->fullname,
                        'comment'    => $reply->comment,
                        'created_at' => $reply->created_at->format('d-m-Y H:i:s'),
                    ];
                });
            })->first(),
        ];
    }

    private function getStatus()
    {
        if ($this->replies->count() > 0) {
            return [
                'color' => 'green',
                'text' => 'Đã phản hồi'
            ];
        }

        return [
            'color' => 'red',
            'text' => 'Chưa phản hồi'
        ];
    }
}
