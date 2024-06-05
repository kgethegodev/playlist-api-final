<?php

namespace App\Observers;

use App\Jobs\CreateAndPopulateSpotifyPlaylist;
use App\Models\Playlist;

class PlaylistObserver
{
    /**
     * Handle the Playlist "created" event.
     */
    public function created(Playlist $playlist): void
    {
        CreateAndPopulateSpotifyPlaylist::dispatch($playlist);
    }

    /**
     * Handle the Playlist "updated" event.
     */
    public function updated(Playlist $playlist): void
    {
        //
    }

    /**
     * Handle the Playlist "deleted" event.
     */
    public function deleted(Playlist $playlist): void
    {
        //
    }

    /**
     * Handle the Playlist "restored" event.
     */
    public function restored(Playlist $playlist): void
    {
        //
    }

    /**
     * Handle the Playlist "force deleted" event.
     */
    public function forceDeleted(Playlist $playlist): void
    {
        //
    }
}
