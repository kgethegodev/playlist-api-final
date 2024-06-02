<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class PlaylistController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'prompt' => ['required', 'string']
        ]);

        $geminiService = new GeminiService();

        try {
            $response = $geminiService->generateContent('Create a playlist of songs based on this query: ' . $request->input('prompt') . ', like this:
            "
            **Title:** Title \n
            \n
            **Description:** Description \n
            \n
            ```json\n
            [\n
            { \"title\": \"Marvins Room\", \"artist\": \"Drake\", \"genre\": \"Hip Hop\" },\n
            { \"title\": \"Good News\", \"artist\": \"Mac Miller\", \"genre\": \"Hip Hop\" },\n
            etc
            ]\n
            "
            ');

            $input_string = $response["candidates"][0]["content"]["parts"][0]["text"];

            // Extract the title
            preg_match('/##\s*(.*?)\s*\n/', $input_string, $titleMatches);
            $title = $titleMatches[1] ?? '';

            // Extract the description
            preg_match('/\**Description:\** (.*?)\n\n```json|\*\*Description:\*\* (.*?)\n\n```json|Description: (.*?)\n\n```json/s', $input_string, $descriptionMatches);
            $description = $descriptionMatches[1] ?? $descriptionMatches[2] ?? $descriptionMatches[3] ?? '';

            // Extract the JSON string
            preg_match('/```json\n(.*?)\n```/s', $input_string, $jsonMatches);
            $jsonString = $jsonMatches[1] ?? '';

            // Decode the JSON string
            $songs = json_decode($jsonString, true);

            if(!$title || !$description || !$songs){
                error(json_encode([
                    'message'       => 'input missing',
                    'input_string'  => $input_string,
                    'title'         => trim($title),
                    'description'   => trim($description),
                    'songs'         => $songs
                ]));

                return response()->json([
                    'error' => "Oops something went wrong."
                ],400);
            }
                
            $playlist = Playlist::create([
                'title'         => $title,
                'description'   => $description,
                'songs'         => json_encode($songs),
            ]);

            $spotify = new SpotifyService();

            $new_playlist = $spotify->createPlaylist($playlist->title, $playlist->description);

            $playlist->update([
                'external_id'   => $new_playlist['id'],
                'link'          => $new_playlist['external_urls']['spotify']
            ]);

            return response()->json([
                'message'   => 'Playlist created.',
                'playlist'  => $playlist
            ]);
        }
        catch(\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        
    }
}
