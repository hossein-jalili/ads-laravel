<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimAdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_name' => $this->owner_name,
            'number' => $this->number,
            'price_suggestion' => $this->price_suggestion,
            'city' => $this->city,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
