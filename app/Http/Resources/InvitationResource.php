<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'groom_name' => $this->groom_name,
            'bride_name' => $this->bride_name,
            'event_date' => $this->event_date->format('Y-m-d'),
            'akad_time' => $this->akad_time,
            'resepsi_time' => $this->resepsi_time,
            'location' => $this->location,
            'location_url' => $this->location_url,
            'description' => $this->description,
            'cover_image_url' => $this->cover_image_url,
            'guests_count' => $this->whenCounted('guests'),
            'rsvps_count' => $this->whenCounted('rsvps'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}