<?php

namespace App\Services;

use App\Models\SpotifyToken;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    public string $access_token = '';
    public mixed $user_id;

    public function __construct()
    {
        $this->access_token = SpotifyToken::firstOrFail()->spotify_access_token;
        $user = $this->me();
        $this->user_id = $user['id'];
    }

    public function createPlaylist($name, $description, $public = true)
    {
        $data = [
            'name'          => $name,
            'description'   => $description,
            'public'        => $public
        ];

        $response = Http::withToken($this->access_token)->post(
            config('spotify.url') . '/users/' . $this->user_id . '/playlists', $data);

        if($response->status() === 201){
            return $response->json();
        } else {
            throw($response->toException());
        }
    }

    public function addItemToPlaylist(string $playlist_id, array $uri_array)
    {
        $data = [
            'uris'      => $uri_array,
            'position'  => 0
        ];

        $response = Http::withToken($this->access_token)->withHeaders([
            'Content-Type' => 'application/json',
            'Content-Length' => 0,
        ])->post(config('spotify.url') . '/playlists/' . $playlist_id . '/tracks', $data);

        if($response->status() === 200){
            return $response->json();
        } else {
            throw($response->toException());
        }
    }

    public function search($genre, $title, $artist, $album, $type = 'track')
    {
        $response = Http::withToken($this->access_token)->get(config('spotify.url') . '/search', [
            'q' => 'track' . $title . 'artist' . $artist . 'album' . $album . 'genre' . $genre,
            'type' => $type
        ]);


        if($response->status() === 200){
            return $response->json();
        } else {
            throw($response->toException());
        }
    }

    public function me() {
        $response = Http::withToken($this->access_token)->get(config('spotify.url') . '/me');

        if($response->status() === 200){
            return $response->json();
        } else {
            throw($response->toException());
        }
    }
}
