<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotifyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'spotify_access_token',
        'spotify_refresh_token',
        'expires_at',
    ];
}
