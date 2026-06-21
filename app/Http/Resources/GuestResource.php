<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'unique_token' => $this->unique_token,
            'rsvp_link' => url('/rsvp/'.$this->unique_token),
            'invited_at' => $this->invited_at,
            'created_at' => $this->created_at,
        ];
    }
}