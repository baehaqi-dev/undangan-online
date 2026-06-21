<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index(Invitation $invitation)
    {
        $this->authorize('view', $invitation);

        $guests = $invitation->guests()->latest()->paginate(15);

        return GuestResource::collection($guests);
    }

    public function store(StoreGuestRequest $request, Invitation $invitation)
    {
        $this->authorize('update', $invitation);

        $guest = $invitation->guests()->create([
            ...$request->validated(),
            'unique_token' => Str::random(16),
        ]);

        return new GuestResource($guest);
    }

    public function show(Guest $guest)
    {
        $this->authorize('view', $guest->invitation);

        return new GuestResource($guest);
    }

    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        $this->authorize('update', $guest->invitation);

        $guest->update($request->validated());

        return new GuestResource($guest);
    }

    public function destroy(Guest $guest)
    {
        $this->authorize('update', $guest->invitation);

        $guest->delete();

        return response()->json([
            'message' => 'Guest deleted successfully',
        ]);
    }
}