<?php

namespace App\Jobs;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Guest $guest
    ) {}

    public function handle(): void
    {
        if (empty($this->guest->phone)) {
            Log::warning("Guest {$this->guest->id} has no phone number, skipping WhatsApp notification.");

            return;
        }

        $this->guest->loadMissing('invitation');

        $message = $this->buildMessage();

        // Simulasi kirim WhatsApp - di production, ganti dengan API provider asli (Fonnte, Twilio, dll)
        Log::info('WhatsApp notification sent', [
            'to' => $this->guest->phone,
            'guest_name' => $this->guest->name,
            'message' => $message,
        ]);

        $this->guest->update(['invited_at' => now()]);
    }

    protected function buildMessage(): string
    {
        $invitation = $this->guest->invitation;

        return "Halo {$this->guest->name}, Anda diundang ke acara pernikahan "
            ."{$invitation->groom_name} & {$invitation->bride_name} "
            ."pada {$invitation->event_date->format('d M Y')}. "
            ."Lihat undangan: ".url('/rsvp/'.$this->guest->unique_token);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send WhatsApp to guest {$this->guest->id}: {$exception->getMessage()}");
    }
}