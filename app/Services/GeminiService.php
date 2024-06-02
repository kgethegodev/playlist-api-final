<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
            throw($response->toException());
        }
    }
}