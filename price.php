<?php
namespace ExchangeApi;

require 'exchange_repository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # GET ExchangeApi/?id=5d20e7157bbe8
    if (isset($_GET['id']) && isset($_GET['currencies'])) {
        $repository = new ExchangeRepository();
        $upCurrencies = strtoupper($_GET['currencies']);
        echo json_encode(array(
            "currencies" => $upCurrencies,
            "rate" => $repository->getByApiKey($_GET['id'])->getPrice($upCurrencies)
        ));
    }
}