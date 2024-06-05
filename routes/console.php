<?php

use App\Models\Playlist;
use App\Services\GeminiService;
use App\Services\SpotifyService;
use App\Services\WhatsappService;
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
  $playlist = Playlist::find(6);
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

Artisan::command('whatsapp:send', function () {
    $to = "0659114806";
    $name = "Kgethego Masilo";
    $url = "https://baee-2c0f-f4c0-902a-a5e0-312a-8f94-2f45-3fca.ngrok-free.app/";

    $whatsapp = new WhatsappService();

    try {
        $response = $whatsapp->sendPlaylist($to, $name, $url);
        dd($response);

    } catch (\Exception $e) {
        dd($e);
    }
});
