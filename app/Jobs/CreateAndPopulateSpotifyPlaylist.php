<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\SpotifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateAndPopulateSpotifyPlaylist implements ShouldQueue
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
        try {
            $spotify = new SpotifyService();

            $new_playlist = $spotify->createPlaylist($this->playlist->title, $this->playlist->description);

            $this->playlist->update([
                'external_id'   => $new_playlist['id'],
                'link'          => $new_playlist['external_urls']['spotify']
            ]);

            $uri_array = [];
            foreach(json_decode($this->playlist->songs) as $song) {
                 try {
                     $response = $spotify->search($song->genre,$song->title,$song->album,$song->artist);
                     if($response["tracks"]["items"][0]["type"] === "track")
                         $uri_array[] = $response["tracks"]["items"][0]["uri"];
                 }
                 catch (\Exception $e) {
                     Log::info($song);
                     Log::error($e->getMessage());
                 }
             }
            try {
                $spotify->addItemToPlaylist($this->playlist->external_id, $uri_array);
            }
            catch(\Exception $e) {
                Log::info($this->playlist);
                Log::info(json_encode($uri_array));
                Log::error($e->getMessage());
            }

//            SendWhatsAppMessage::dispatch($this->playlist);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function failed(\Exception $e): void
    {
        Log::error($e->getMessage());
    }
}
