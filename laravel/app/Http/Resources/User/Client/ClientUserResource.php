<?php

namespace App\Http\Resources\User\Client;

use App\Http\Resources\UserAddress\UserAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientUserResource extends JsonResource
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
            'fullname'       => $this->fullname,
            'image'          => $this->image,
            'birthday'       => $this->birthday,
            'hint_email'     => hintEmail($this->email),
            'email'     => $this->email,
            'hint_phone'     => hintPhoneNumber($this->phone),
            'addresses'      => UserAddressResource::collection($this->user_addresses),
        ];
    }
}
