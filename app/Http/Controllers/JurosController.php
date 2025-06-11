<?php

namespace App\Http\Controllers;

use App\DAO\JurosDAO;
use App\Model\taxaJuros;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JurosController
{
    public function atualizarJuros(Request $request, Response $response): Response
    {
        $dados = $request->getParsedBody();

        // JSON inválido ou parâmetros ausentes → 400 sem corpo
        if (!isset($dados['dataInicio']) || !isset($dados['dataFinal'])) {
            return $response->withStatus(400);
        }

        try {
            $dataInicio = new DateTime($dados['dataInicio']);
            $dataFinal = new DateTime($dados['dataFinal']);
            $hoje = new DateTime();
            $limiteInicio = new DateTime('2010-01-01');

            if ($dataFinal < $dataInicio || $dataFinal > $hoje || $dataInicio < $limiteInicio) {
                return $response->withStatus(400);
            }

            $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial="
                 . $dataInicio->format('d/m/Y') . "&dataFinal=" . $dataFinal->format('d/m/Y');

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $resposta = curl_exec($ch);
            curl_close($ch);

            if (!$resposta) {
                return $response->withStatus(400);
            }

            $valoresApi = json_decode($resposta, true);

            if (!is_array($valoresApi) || empty($valoresApi)) {
                return $response->withStatus(400);
            }

            $valorTotal = 0.0;

            foreach ($valoresApi as $valor) {
                if (isset($valor['valor'])) {
                    $valorTotal += floatval(str_replace(',', '.', $valor['valor']));
                }
            }

            $valorTotal = round($valorTotal, 2) / 100;

            $jurosModel = new taxaJuros(
                $dados['dataInicio'],
                $dados['dataFinal'],
                $valorTotal,
                uniqid()
            );

            $jurosDao = new JurosDAO();
            $jurosDao->salvarJuros($jurosModel);

            // Retorna apenas a nova taxa no corpo
            $response->getBody()->write(json_encode(["taxa" => $valorTotal]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            return $response->withStatus(400); // erro inesperado → ainda responde com 400 sem corpo
        }
    }
}
