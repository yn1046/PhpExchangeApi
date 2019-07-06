<?php


namespace ExchangeApi;


final class Exchange
{
    public $apiKey;
    public $balances;
    public $rates;

    public function __construct()
    {
        $this->apiKey = uniqid();
    }

    public function getPrice($currencies)
    {
        $from = substr($currencies, 0, 3);
        $to = substr($currencies, 3);
        return $this->rates[$to] / $this->rates[$from];
    }

    public function canExchange($currencies, $amount)
    {
        $from = substr($currencies, 0, 3);
        return $this->balances[$from] >= $amount;
    }

    public function setBalances($percentages, $usdAmount)
    {
        $result = [];

        foreach ($percentages as $currency => $percent) {
            $result[$currency] = ($percent / 100) * $usdAmount * $this->rates[$currency];
        }

        $this->balances = $result;

        return $result;
    }

    public function updatePercentages($percentages)
    {
        $result = [];

        foreach ($percentages as $currency => $percent) {
            $result[$currency] = ($percent / 100) * $this->getFullBalanceInUsd() * $this->rates[$currency];
        }

        $this->balances = $result;

        return $result;
    }

    public function getFullBalanceInUsd()
    {
        $sum = 0;
        foreach ($this->balances as $currency => $balance) {
            $sum += $balance / $this->rates[$currency];
        }

        return $sum;
    }


}