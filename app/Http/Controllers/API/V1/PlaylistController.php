<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use function Laravel\Prompts\error;

class PlaylistController extends Controller
{
    public function create(Request $request): JsonResponse|Response
    {
        $request->validate([
            'prompt' => ['required', 'string'],
            'name' => ['required', 'string'],
            'contact_number' => ['required', 'string'],
        ]);

        $geminiService = new GeminiService();

        try {
            $response = $geminiService->generateContent('Create a playlist of songs based on this query: ' . $request->input('prompt') . ', Make sure these songs exist on Spotify. Use the JSON format given below.
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

                return Inertia::render('Home', [
                    'message'   => 'Oops something went wrong.',
                    'status'    => 'failed'
                ]);
            }

            Playlist::create([
                'title'             => $title,
                'description'       => $description,
                'songs'             => json_encode($songs),
                'name'              => $request->input('name'),
                'contact_number'    => $request->input('contact_number'),
            ]);

            return Inertia::render('Home', [
                'message'   => 'Playlist created.',
                'status'    => 'success'
            ]);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());

            return Inertia::render('Home', [
                'message'   => 'Oops something went wrong.',
                'status'    => 'failed'
            ]);
        }
    }

    public function addTracks(int $playlist_id)
    {
        $playlist = Playlist::find($playlist_id);
        $uri_array = [];
        $spotify = new SpotifyService();
        foreach(json_decode($playlist->songs) as $song) {
             try {
             $response = $spotify->search($song->genre,$song->title,$song->album,$song->artist);
             if($response["tracks"]["items"][0]["type"] === "track")
                 $uri_array[] = $response["tracks"]["items"][0]["uri"];
             }
             catch (\Exception $e) {
             dd($e->getMessage());
             }
         }
        try {
            $spotify->addItemToPlaylist($playlist->external_id, $uri_array);
            dd("Awe hond");
        }
        catch(\Exception $e) {
            dd($e->getMessage());
        }
    }
}
