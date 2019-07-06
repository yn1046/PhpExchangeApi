<?php

namespace ExchangeApi;

require 'exchange_repository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # GET ExchangeApi/balance.php?id=5d20e7157bbe8
    if (isset($_GET['id'])) {
        $repository = new ExchangeRepository();
        echo json_encode($repository->getByApiKey($_GET['id'])->balances);
    }
}