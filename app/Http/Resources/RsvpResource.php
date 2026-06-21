<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RsvpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guest_name' => $this->guest_name,
            'attendance' => $this->attendance,
            'total_guests' => $this->total_guests,
            'message' => $this->message,
            'created_at' => $this->created_at,
        ];
    }
}