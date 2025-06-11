<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

// Define a data final como ontem (pois hoje pode não ter valor disponível)
$dataFinal = (new DateTime('-1 day', new DateTimeZone('UTC')))->format('d/m/Y');

// Define a data inicial provisória: máximo 10 anos antes da data final
$dataInicialProvisoria = (new DateTime($dataFinal, new DateTimeZone('UTC')))
                        ->modify('-10 years')
                        ->format('d/m/Y');

// Define a data mínima permitida pela API do Banco Central
$dataInicialMinima = '01/01/2000';

// Ajusta a data inicial para não ser anterior à mínima permitida
$dataInicial = (DateTime::createFromFormat('d/m/Y', $dataInicialProvisoria) < DateTime::createFromFormat('d/m/Y', $dataInicialMinima)) 
    ? $dataInicialMinima 
    : $dataInicialProvisoria;

// Monta a URL da requisição com as datas no formato correto
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
