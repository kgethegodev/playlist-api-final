<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Models\WhatsappMessage;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Playlist $playlist;

    /**
     * Create a new job instance.
     */
    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $whatsapp = new WhatsappService();

        try {
            $response = $whatsapp->sendPlaylist($this->playlist->contact_number, $this->playlist->name, $this->playlist->link);
            WhatsappMessage::create([
                'playlist_id'   => $this->playlist->id,
                'external_id'   => $response['messages'][0]['id'],
                'status'        => $response['messages'][0]['message_status'],
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function failed(\Exception $e): void
    {
        Log::error($e->getMessage());
    }
}
