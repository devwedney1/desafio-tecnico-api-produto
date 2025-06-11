<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

// Cria instância do Guzzle
$client = new Client();

// Define a data final como a data atual (ou ontem, se preferir)
$dataFinal = (new DateTime('now', new DateTimeZone('UTC')))->format('d/m/Y');

// Define a data inicial como 5 anos antes da final
$dataInicial = (new DateTime($dataFinal, new DateTimeZone('UTC')))
                ->modify('-5 years')
                ->format('d/m/Y');

// Monta a URL da requisição com as datas no formato dd/mm/yyyy
$url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial={$dataInicial}&dataFinal={$dataFinal}";

try {
    $response = $client->request('GET', $url);

    if ($response->getStatusCode() === 200) {
        $body = $response->getBody();
        $dados = json_decode($body, true);

        if (!empty($dados)) {
            echo "Dados entre $dataInicial e $dataFinal:\n\n";
            foreach ($dados as $dia) {
                echo "Data: {$dia['data']} - Valor: {$dia['valor']}\n";
            }
        } else {
            echo "Nenhum dado retornado pela API.\n";
        }
    } else {
        echo "Erro na requisição: Código HTTP {$response->getStatusCode()}.\n";
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "Erro na requisição: " . $e->getMessage() . "\n";
}

