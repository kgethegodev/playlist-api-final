<?php

namespace App\Models;

use App\Observers\PlaylistObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PlaylistObserver::class])]
class Playlist extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'songs', 'external_id', 'link', 'contact_number', 'name'];

    protected $casts = [
        'songs' => 'json'
    ];
}
