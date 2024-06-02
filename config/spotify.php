<?php

return [
    'url' => 'https://api.spotify.com/v1',
    'key' => env('SPOTIFY_ACCESS_KEY'),
    'client_id' => env('SPOTIFY_CLIENT_ID'),
    'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    'redirect_url' => env('SPOTIFY_REDIRECT_URI'),
];
