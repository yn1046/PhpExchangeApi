<?php


namespace ExchangeApi;

use mysqli;

require 'exchange.php';

final class ExchangeRepository
{
    public $percentages;
    private $conn;

    public function __construct()
    {
        $this->conn = mysqli_connect('localhost', 'root', 'root', 'exchange_api');
        $this->percentages = array(
            'USD' => 40,
            'RUB' => 20,
            'EUR' => 40
        );
    }

    public function getByApiKey($apiKey)
    {
        $result = $this->conn->query("SELECT * FROM exchange WHERE api_key = '$apiKey'");
        return $this->mapRow($result->fetch_assoc());
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM exchange");
        if (!$result) return null;

        $all = [];

        while ($row = $result->fetch_assoc()) {
            array_push($all, $this->mapRow($row));
        }

        return $all;
    }

    public function add($exchange)
    {
        $balances = $this->conn->escape_string(json_encode($exchange->balances));
        $rates = $this->conn->escape_string(json_encode($exchange->rates));
        $apiKey = $exchange->apiKey;

        $result = $this->conn->query(
            "INSERT INTO exchange VALUES ('$apiKey', '$balances', '$rates')");

        return $exchange->apiKey;
    }

    public function remove($apiKey)
    {
        $this->conn->query("DELETE FROM exchange WHERE api_key = '$apiKey'");
        return $this->conn->affected_rows;
    }

    public function applyPercentageChanges()
    {
        foreach ($this->getAll() as $exchange) {
            $newBalance = $exchange->updatePercentages($this->percentages);
            $newBalanceJson = $this->conn->escape_string(json_encode($newBalance));
            $apiKey = $exchange->apiKey;
            $this->conn->query("UPDATE exchange SET balances = '$newBalanceJson'"
                . " WHERE api_key = '$apiKey'");
        }
    }

    private function mapRow($row)
    {
        $exc = new Exchange();
        $exc->apiKey = $row['api_key'];
        $exc->balances = json_decode($row['balances'], true);
        $exc->rates = json_decode($row['rates'], true);

        return $exc;
    }


}