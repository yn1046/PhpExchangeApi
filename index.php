<?php

namespace ExchangeApi;

require 'exchange_repository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $repository = new ExchangeRepository();
    echo json_encode($repository->getAll());
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repository = new ExchangeRepository();
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $exchange = new Exchange();
    $exchange->rates = $data['rates'];
    $exchange->setBalances($repository->percentages, $data['balance']);

    echo json_encode($repository->add($exchange));
}