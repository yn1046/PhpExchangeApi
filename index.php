<?php

namespace ExchangeApi;

require 'exchange_repository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $repository = new ExchangeRepository();
    # GET ExchangeApi/?id=5d20e7157bbe8
    if (isset($_GET['id'])) {
        echo json_encode($repository->getByApiKey($_GET['id']));
    }
    # GET ExchangeApi/
    else echo json_encode($repository->getAll());
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repository = new ExchangeRepository();
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($_GET['id'])) {
        // POST ExchangeApi/?id=5d20e7157bbe8
        /*
          {
                "currencies": "RUBUSD",
                "amount": 1000
          }
        */
        $exchange = $repository->getByApiKey($_GET['id']);
        $upCurrencies = strtoupper($data['currencies']);
        $from = substr($upCurrencies, 0, 3);
        $to = substr($upCurrencies, 3);
        $amount = $data['amount'];

        if (!$exchange->canExchange($upCurrencies, $amount)) {
            echo "Couldn't perform the exchange.\n"
                . "Required amount: $amount\n"
                . "Actual balance: " . $exchange->balances[$from];
        } else {
            $amountUsd = $amount * $exchange->getPrice($from . "USD");
            $percent = $amountUsd / $exchange->getFullBalanceInUsd() * 100;
            $repository->percentages[$from] -= $percent;
            $repository->percentages[$to] += $percent;
            $repository->applyPercentageChanges();
            echo json_encode($repository->getAll());
        }

    } else {
        // POST ExchangeApi/
        /*
          {
            "balance": 27,
            "rates": {
                "RUB": 61,
                "EUR": 1.23,
                "USD": 1
            }
        }
        */
        $exchange = new Exchange();
        $exchange->rates = $data['rates'];
        $exchange->setBalances($repository->percentages, $data['balance']);

        echo json_encode($repository->add($exchange));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        $repository = new ExchangeRepository();
        echo json_encode(array(
            'deleted' => $repository->remove($_GET['id'])
        ));
    }
}