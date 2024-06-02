<?php

use App\Http\Controllers\ProfileController;
use App\Models\SpotifyToken;
use App\Services\SpotifyService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    $state = csrf_token();
    SpotifyToken::create(['state' => $state]);

    $query = http_build_query([
        'client_id' => config('spotify.client_id'),
        'response_type' => 'code',
        'redirect_uri' => config('spotify.redirect_url'),
        'scope' => 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public',
        'state' => $state
    ]);

    return redirect('https://accounts.spotify.com/authorize?' . $query);
});
Route::get('/callback', function (Request $request) {
    $code = $request->input('code');
    $state = $request->input('state');

    if ($state !== session()->token()) {
        abort(403, 'Invalid state');
    }

    $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => config('spotify.redirect_url'),
        'client_id' => config('spotify.client_id'),
        'client_secret' => config('spotify.client_secret'),
    ]);

    $data = $response->json();

    $accessToken = $data['access_token'];
    $refreshToken = $data['refresh_token'];
    $expiresIn = $data['expires_in'];

    // Store tokens in session or database
    session([
        'spotify_access_token' => $accessToken,
        'spotify_refresh_token' => $refreshToken,
        'spotify_expires_in' => now()->addSeconds($expiresIn)
    ]);

    return redirect('/');
});

Route::get('/', function () {
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/auth.php';
