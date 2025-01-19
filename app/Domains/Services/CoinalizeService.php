<?php

namespace App\Domains\Services;

use Illuminate\Support\Facades\Http;

class CoinalizeService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('api.coinalize_base_url', 'https://api.coinalyze.net/');
        $this->apiKey = config('api.coinalize_api');
    }

    public function getBtcInterests($symbol = 'BTCUSDT_PERP.A')
    {
        $response = Http::withHeaders([
            'api_key' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . 'v1/open-interest',[
            'symbols' => $symbol,
            'convert_to_usd' => "true"
        ]);
dd($response->json());
        if ($response->successful()) {
            return $response->json();
        }

        return null;

    }
}
