<?php

require 'vendor/autoload.php'; 

use GuzzleHttp\Client;

class Selic
{
    public function obterDados()
    {
        $client = new Client();

        $dataFinal = (new DateTime('now', new DateTimeZone('UTC')))->format('d/m/Y');
        $dataInicial = (new DateTime($dataFinal, new DateTimeZone('UTC')))
                        ->modify('-5 years')
                        ->format('d/m/Y');

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
    }
}

// Chama a função
$selic = new Selic();
$selic->obterDados();






