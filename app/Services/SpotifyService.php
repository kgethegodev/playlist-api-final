<?php

namespace App\Services;

use App\Models\SpotifyToken;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    public $access_token = '';
    public $user_id;

    public function __construct()
    {
        $this->access_token = SpotifyToken::first()->spotify_access_token;
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
            'uris' => $uri_array
        ];

        $response = Http::withToken($this->access_token)->post(config('spotify.url') . '/playlists/' . $playlist_id . '/tracks', $data);

        dd($response);

        if($response->status() === 200){
            return $response->json();
        } else {
            throw($response->toException());
        }
    }

    public function search($genre, $title, $artist, $type = 'track')
    {
        $response = Http::withToken($this->access_token)->get(config('spotify.url') . '/search', [
            'q' => 'genre' . $genre . 'track' . $title . 'artist' . $artist . 'market ZA',
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