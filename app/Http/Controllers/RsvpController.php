<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Http\Resources\RsvpResource;
use App\Models\Guest;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RsvpController extends Controller
{
    public function showByToken(string $token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();

        if (is_null($guest->invited_at)) {
            $guest->update(['invited_at' => Carbon::now()]);
        }

        $invitationData = Cache::remember(
            "invitation.{$guest->invitation_id}",
            now()->addHours(6),
            function () use ($guest) {
                $guest->loadMissing('invitation');

                return [
                    'groom_name' => $guest->invitation->groom_name,
                    'bride_name' => $guest->invitation->bride_name,
                    'event_date' => $guest->invitation->event_date->format('Y-m-d'),
                    'akad_time' => $guest->invitation->akad_time,
                    'resepsi_time' => $guest->invitation->resepsi_time,
                    'location' => $guest->invitation->location,
                    'location_url' => $guest->invitation->location_url,
                    'description' => $guest->invitation->description,
                    'cover_image_url' => $guest->invitation->cover_image_url,
                ];
            }
        );

        return response()->json([
            'guest_name' => $guest->name,
            'invitation' => $invitationData,
        ]);
    }

    public function storeByToken(StoreRsvpRequest $request, string $token)
    {
        $guest = Guest::where('unique_token', $token)->firstOrFail();

        $rsvp = $guest->rsvps()->create([
            ...$request->validated(),
            'invitation_id' => $guest->invitation_id,
        ]);

        return new RsvpResource($rsvp);
    }

    public function index(Invitation $invitation)
    {
        $this->authorize('view', $invitation);

        $rsvps = $invitation->rsvps()->latest()->paginate(15);

        return RsvpResource::collection($rsvps);
    }
}