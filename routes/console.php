<?php

use App\Models\Playlist;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\error;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('spotify:me', function() {
  $spotify = new SpotifyService();

  try{
    // $spotify->auth();
    $response = $spotify->me();
    dd($response);
  }
  catch(\Exception $e){
    dd($e->getMessage());
  }
  
});

Artisan::command('spotify:add', function() {
  $playlist = Playlist::findOrFail(21)->first();
  $uri_array = [];
  $spotify = new SpotifyService();
  foreach(json_decode($playlist->songs) as $song) {
    try {
      $response = $spotify->search($song->genre,$song->title,$song->artist);
      if($response["tracks"]["items"][0]["type"] === "track")
      if(in_array("ZA", $response["tracks"]["items"][0]["available_markets"]))
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
});
