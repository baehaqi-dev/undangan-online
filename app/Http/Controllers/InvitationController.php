<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $invitations = $request->user()
            ->invitations()
            ->withCount(['guests', 'rsvps'])
            ->latest()
            ->paginate(10);

        return InvitationResource::collection($invitations);
    }

    public function store(StoreInvitationRequest $request)
    {
        $invitation = $request->user()->invitations()->create($request->validated());

        return new InvitationResource($invitation);
    }

    public function show(Invitation $invitation)
    {
        $this->authorize('view', $invitation);

        $invitation->loadCount(['guests', 'rsvps']);

        return new InvitationResource($invitation);
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation)
    {
        $this->authorize('update', $invitation);

        $invitation->update($request->validated());

        Cache::forget("invitation.{$invitation->id}");

        return new InvitationResource($invitation);
    }

    public function destroy(Invitation $invitation)
    {
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json([
            'message' => 'Invitation deleted successfully',
        ]);
    }
}