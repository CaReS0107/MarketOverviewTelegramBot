<?php

namespace App\Domains\Services;

use Illuminate\Support\Facades\Http;

class CoinMarketCapService
{

    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url', 'https://pro-api.coinmarketcap.com/');
        $this->apiKey = config('api.api_key');
    }

    public function getFearAndGreed(): ?array
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . 'v3/fear-and-greed/latest');

        if ($response->successful()) {
            return $response->json();
        }

        return null;

    }

    /**
     * Fetch cryptocurrency quotes by symbol.
     *
     * @param string $symbol
     * @param string $convert
     * @return array|null
     */
    public function getCmc100Latest()
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . 'v3/index/cmc100-latest');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getLatestQuotes()
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . 'v1/global-metrics/quotes/latest');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getLatestPrice()
    {
        $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,solana,binancecoin&vs_currencies=usd";

        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        return [
            'BTC' => $data['bitcoin']['usd'],
            'ETH' => $data['ethereum']['usd'],
            'SOL' => $data['solana']['usd'],
            'BNB' => $data['binancecoin']['usd'],
        ];
    }

    public function getOpenInterestsData($symbol = 'BTCUSDT'): mixed
    {
        $url = "https://fapi.binance.com/fapi/v1/openInterest?symbol={$symbol}";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return data_get($data, 'openInterest');
        }

        return null;
    }
}
