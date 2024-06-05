<?php

namespace App\Http\Middleware;

use App\Models\SpotifyToken;
use Closure;
use Illuminate\Support\Facades\Http;

class SpotifyTokenRefresh
{
    public function handle($request, Closure $next)
    {
        $token = SpotifyToken::query()->first();

        if ($token && now()->greaterThan($token->expires_at)) {
            $state = $token->state;

            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => session('spotify_refresh_token'),
                'client_id' => config('spotify.client_id'),
                'client_secret' => config('spotify.client_secret'),
            ]);

            $data = $response->json();

            $spotify_tokens = SpotifyToken::all();

            // Deleting tokens
            foreach($spotify_tokens as $spotify_token){
                $spotify_token->delete();
            }

            SpotifyToken::create([
                'state'                 => $state,
                'spotify_access_token'  => $data['access_token'],
                'expires_at'            => now()->addSeconds($data['expires_in'])
            ]);
        }

        return $next($request);
    }
}
