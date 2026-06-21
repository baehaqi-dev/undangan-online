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

    public function statistics(Invitation $invitation)
    {
        $this->authorize('view', $invitation);

        $totalGuests = $invitation->guests()->count();
        $totalInvited = $invitation->guests()->whereNotNull('invited_at')->count();

        $rsvpBreakdown = $invitation->rsvps()
            ->selectRaw('attendance, COUNT(*) as count, SUM(total_guests) as total_people')
            ->groupBy('attendance')
            ->get()
            ->keyBy('attendance');

        return response()->json([
            'total_guests' => $totalGuests,
            'total_invited' => $totalInvited,
            'total_rsvp_responses' => $invitation->rsvps()->count(),
            'breakdown' => [
                'hadir' => [
                    'responses' => $rsvpBreakdown->get('hadir')->count ?? 0,
                    'total_people' => (int) ($rsvpBreakdown->get('hadir')->total_people ?? 0),
                ],
                'tidak_hadir' => [
                    'responses' => $rsvpBreakdown->get('tidak_hadir')->count ?? 0,
                ],
                'ragu_ragu' => [
                    'responses' => $rsvpBreakdown->get('ragu_ragu')->count ?? 0,
                ],
            ],
        ]);
    }

    public function exportGuests(Invitation $invitation)
    {
        $this->authorize('view', $invitation);

        $guests = $invitation->guests()->with('rsvps')->get();

        $filename = "guests-{$invitation->slug}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($guests) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Name', 'Phone', 'Invited At', 'RSVP Status', 'Total Guests', 'Message']);

            foreach ($guests as $guest) {
                $latestRsvp = $guest->rsvps->last();

                fputcsv($file, [
                    $guest->name,
                    $guest->phone,
                    $guest->invited_at?->format('Y-m-d H:i'),
                    $latestRsvp->attendance ?? 'Belum respon',
                    $latestRsvp->total_guests ?? '-',
                    $latestRsvp->message ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}