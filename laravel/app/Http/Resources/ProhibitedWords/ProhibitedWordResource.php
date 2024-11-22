<?php

namespace App\Http\Resources\ProhibitedWords;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProhibitedWordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key'     => $this->id,
            'keyword' => $this->keyword,
        ];
    }
}
