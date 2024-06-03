<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generateContent($prompt)
    {
        $response = Http::post(config('gemini.url') . ':generateContent?key=' . config('gemini.key'), [
            'contents' => [
                'parts' => [
                    'text' => $prompt
                ]
            ]
        ]);

        if($response->status() === 200){
            return $response->json();
        } else {
            Log::error($response);
            throw($response->toException());
        }
    }
}