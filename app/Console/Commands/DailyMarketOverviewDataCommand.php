<?php

namespace App\Console\Commands;

use App\Domains\Facades\CoinMarketCap;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

class DailyMarketOverviewDataCommand extends Command
{
    protected $signature = 'run:daily-market-overview';

    protected $description = 'This command will run daily to get the market overview data.';

    public function handle()
    {
        /** @var Api $telegram */
        $telegram = new Api(config('telegram.bots.MarketOverviewDataBot.token'));

        /** @var Chat $chat */
        $chat = $telegram->getChat(['chat_id' => config('api.chat_id')]);

        $btcInterests = $this->getOpenInterestsData();
        $fearGreed = $this->getFearAndGreed();
        $latestQuotes = $this->getLatestQuotes();
        $prices = $this->getLatestPrice();

        $btcPrice = number_format(data_get($prices, 'BTC'), 2);
        $ethPrice = number_format(data_get($prices, 'ETH'), 2);
        $solPrice = number_format(data_get($prices, 'SOL'), 2);
        $bnbPrice = number_format(data_get($prices, 'BNB'), 2);

        $btcDominance = number_format($latestQuotes['btc_dominance'], 2);
        $ethDominance = number_format($latestQuotes['eth_dominance'], 2);
        $btcDominanceChange = number_format($latestQuotes['btc_dominance_24h_percentage_change'], 2);
        $ethDominanceChange = number_format($latestQuotes['eth_dominance_24h_percentage_change'], 2);

        $totalMarketCap = number_format($latestQuotes['quote']['USD']['total_market_cap'] / 1e12, 2) . 'T';
        $defiMarketCap = number_format($latestQuotes['quote']['USD']['defi_market_cap'] / 1e9, 2) . 'B';
        $totalVolume24h = number_format($latestQuotes['quote']['USD']['total_volume_24h'] / 1e9, 2) . 'B';

        $stablecoinMarketCap = number_format($latestQuotes['quote']['USD']['stablecoin_market_cap'] / 1e9, 2) . 'B';
        $stablecoinVolume24h = number_format($latestQuotes['quote']['USD']['stablecoin_volume_24h'] / 1e9, 2) . 'B';
        $derivativesVolume24h = number_format($latestQuotes['quote']['USD']['derivatives_volume_24h'] / 1e12, 2) . 'T';

        $defiChange = number_format($latestQuotes['quote']['USD']['defi_24h_percentage_change'], 2);
        $stablecoinChange = number_format($latestQuotes['quote']['USD']['stablecoin_24h_percentage_change'], 2);
        $derivativesChange = number_format($latestQuotes['quote']['USD']['derivatives_24h_percentage_change'], 2);
        $latestUpdate = Carbon::parse($latestQuotes['last_updated'])->format('Y-m-d H:i:s');

        $activeCryptos = $latestQuotes['active_cryptocurrencies'];
        $totalCryptos = $latestQuotes['total_cryptocurrencies'];
        $activePairs = $latestQuotes['active_market_pairs'];
        $activeExchanges = $latestQuotes['active_exchanges'];
        $totalExchanges = $latestQuotes['total_exchanges'];

        $message = <<<EOD
📊 *Market Overview* 📊
━━━━━━━━━━━━━━━━━━━
🚀 *Top Cryptos:*
BTC : \${$btcPrice}
ETH : \${$ethPrice}
SOL : \${$solPrice}
BNB : \${$bnbPrice}

📈 *Market Cap* 💰
━━━━━━━━━━━━━━━━━━━
🌐 Total : {$totalMarketCap}
🔗 DeFi : {$defiMarketCap} ({$defiChange}%)
💵 Stablecoins : {$stablecoinMarketCap} ({$stablecoinChange}%)
📊 24hr Volume : {$totalVolume24h}
💵 Stablecoins Volume24h  : $stablecoinVolume24h
📉 Derivatives : {$derivativesVolume24h} ({$derivativesChange}%)

📊 *CMC Market Stats* 📊
━━━━━━━━━━━━━━━━━━━
🪙 Crypto: Active/Total : {$activeCryptos} / {$totalCryptos}
🔄 Market Pairs : {$activePairs}
🏦 Exchanges: Active/Total : {$activeExchanges} / {$totalExchanges}

⚡️ *Sentiment & Activity* 💡
━━━━━━━━━━━━━━━━━━━
🧭 Fear & Greed Index : {$fearGreed}
📈 Open Interest : {$btcInterests}

🔍 *Dominance* 📊
━━━━━━━━━━━━━━━━━━━
🟧 BTC : {$btcDominance}% (24h: {$btcDominanceChange}%)
🟦 ETH : {$ethDominance}% (24h: {$ethDominanceChange}%)

📅 *Last Update:* {$latestUpdate}
━━━━━━━━━━━━━━━━━━━

@WCryptonian
EOD;

        $telegram->sendMessage([
            'chat_id' => $chat->id,
            'text' => $message
        ]);
    }

    public function getFearAndGreed(): string
    {
        $data = data_get(CoinMarketCap::getFearAndGreed(), 'data', []);

        return data_get($data, 'value_classification', []) . ' (' . data_get($data, 'value', 0) . ')';
    }

    public function getLatestQuotes(): array
    {
        return data_get(CoinMarketCap::getLatestQuotes(), 'data');
    }

    public function getLatestPrice(): array
    {
        return CoinMarketCap::getLatestPrice();
    }

    public function getOpenInterestsData(): mixed
    {
        return CoinMarketCap::getOpenInterestsData();
    }
}
