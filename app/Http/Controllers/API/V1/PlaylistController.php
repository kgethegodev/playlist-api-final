<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Jobs\CreatePlaylist;
use App\Models\Playlist;
use App\Models\User;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use function Laravel\Prompts\error;

class PlaylistController extends Controller
{
    public ?Authenticatable $user;
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    /**
     * Get all playlists
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $playlists = Playlist::all();

        return response()->json($playlists);
    }

    public function create(Request $request): JsonResponse|Response
    {
        $request->validate([
            'prompt' => ['required', 'string'],
        ]);

        dispatch(new CreatePlaylist($request->get('prompt')));

        return response()->json(['message' => 'Playlists being created.']);
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
