<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreatePlaylist implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $prompt)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $geminiService = new GeminiService();

        try {
            $response = $geminiService->generateContent('Create a playlist of songs based on this query: ' . $this->prompt . ', Make sure these songs exist on Spotify. Use the JSON format given below.
            "{ \"title\": \"Hip Hop Playlist\", \"description\": \"Dope hip hop playlist\", \"tracks\": [
                    { \"title\": \"Marvins Room\", \"artist\": \"Drake\", \"genre\": \"Hip Hop\", \"album\": \"Take Care\"},\n
                    { \"title\": \"Good News\", \"artist\": \"Mac Miller\", \"genre\": \"Hip Hop\", \"album\": \"Circles\" },\n
                    etc
                ]
               }"
            ');

            if(str_contains($response["candidates"][0]["content"]["parts"][0]["text"], '```json')) {
                $input_string = json_decode(str_replace(['```json','```'],'',$response["candidates"][0]["content"]["parts"][0]["text"]));
            } else {
                $input_string = json_decode($response["candidates"][0]["content"]["parts"][0]["text"]);
            }

            // Extract the title
            $title = $input_string->title ?? '';

            // Extract the description
            $description = $input_string->description ?? '';

            // Decode the JSON string
            $songs = $input_string->tracks ?? '';

            if(!$title || !$description || !$songs){
                Log::error(json_encode([
                    'message'       => 'input missing',
                    'response'      => $response,
                    'input_string'  => $input_string,
                    'title'         => trim($title),
                    'description'   => trim($description),
                    'songs'         => $songs
                ]));
            }

            Playlist::create([
                'title'             => $title,
                'description'       => $description,
                'songs'             => json_encode($songs)
            ]);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
