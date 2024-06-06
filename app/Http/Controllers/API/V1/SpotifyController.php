<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SpotifyToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpotifyController extends Controller
{
    public function callback(Request $request)
    {
        $code = $request->input('code');
        $state = $request->input('state');

        $spotify_token = SpotifyToken::query()->where('state', $state)->firstOrFail();

        if (!$spotify_token) {
            abort(403, 'Invalid state');
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('spotify.redirect_url'),
            'client_id' => config('spotify.client_id'),
            'client_secret' => config('spotify.client_secret'),
        ]);

        $data = $response->json();

        $accessToken = $data['access_token'];
        $refreshToken = $data['refresh_token'];
        $expiresIn = $data['expires_in'];

        $spotify_tokens = SpotifyToken::all();

        // Deleting tokens
        foreach($spotify_tokens as $spotify_token){
            $spotify_token->delete();
        }

        // Store tokens
        $token = SpotifyToken::create([
            'state'                     => $state,
            'spotify_access_token'      => $accessToken,
            'spotify_refresh_token'     => $refreshToken,
            'expires_at'                => now()->addSeconds($expiresIn)
        ]);

        return response()->json(['message' => 'Awe']);
    }
}
