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

        if (!is_null($token) && now()->greaterThan($token->expires_at)) {

            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->spotify_refresh_token,
                'client_id' => config('spotify.client_id'),
                'client_secret' => config('spotify.client_secret'),
            ]);

            $data = $response->json();

            // Update token
            $token->update([
                'spotify_access_token'  => $data['access_token'],
                'expires_at'            => now()->addSeconds($data['expires_in'])
            ]);
        }

        return $next($request);
    }
}
