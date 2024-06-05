<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public string $access_key;
    public function __construct()
    {
        $this->access_key = config('whatsapp.access_token');
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function sendPlaylist(string $to, string $name, string $url): array|null
    {
        $data = [
            'messaging_product' => 'whatsapp',
            "recipient_type" => "individual",
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'playlist',
                'language' => [
                    'code' => 'en',
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $name,
                            ],
                            [
                                'type' => 'text',
                                'text' => $url,
                            ]
                        ]
                    ],
                ]
            ]
        ];
        $response = Http::withToken($this->access_key)->post(config('whatsapp.url') . config('whatsapp.phone_id') . '/messages', $data);

        if ($response->status() === 200) {
            return $response->json();
        } else {
            throw($response->toException());
        }
    }

    /**
     * @throws RequestException
     */
    public function me()
    {
        $response = Http::get(config('whatsapp.url') . 'me?access_token=' . $this->access_key);
        if ($response->status() === 200) {
            return $response->json();
        } else {
            throw($response->toException());
        }
    }
}
