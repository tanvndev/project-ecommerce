<?php

namespace App\Http\Resources\Post\Client;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_name'         => $this->user->fullname,
            'user_image'        => $this->user->image,
            'name'              => $this->name,
            'image'             => $this->image,
            'description'       => $this->description,
            'content'           => $this->content,
            'canonical'         => $this->canonical,
            'meta_title'        => $this->meta_title,
            'meta_description'  => $this->meta_description,
            'created_at'        => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
