<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'birthdate' => $this->birthdate,
            'location' => $this->location,
            'biography' => $this->biography,
            'user_id' => $this->user_id,
        ];
    }
}
