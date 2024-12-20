<?php

namespace App\Http\Resources\User;

use App\Http\Resources\UserAddress\UserAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'key'            => $this->id,
            'fullname'       => $this->fullname,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'image'          => $this->image,
            'birthday'       => $this->birthday,
            'publish'        => $this->publish,
            'catalogue_id'   => $this->user_catalogue_id,
            'catalogue_name' => $this->user_catalogue->name,
            'catalogue_code' => $this->user_catalogue->code,
            'hint_email'     => hintEmail($this->email, ''),
            'hint_phone'     => hintPhoneNumber($this->phone, ''),
            'addresses'      => UserAddressResource::collection($this->user_addresses),
        ];
    }
}
