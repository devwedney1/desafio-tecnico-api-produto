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

        if (!isset($dados['dataInicio']) || !isset($dados['dataFinal'])) {
            $response->getBody()->write(json_encode(["erro" => "Parâmetros obrigatórios ausentes."]));
            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        }

        try {
            $dataInicio = new DateTime($dados['dataInicio']);
            $dataFinal = new DateTime($dados['dataFinal']);
            $hoje = new DateTime();
            $limiteInicio = new DateTime('2010-01-01');

            if ($dataFinal < $dataInicio || $dataFinal > $hoje || $dataInicio < $limiteInicio) {
                $response->getBody()->write(json_encode(["erro" => "Datas inválidas."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $dataInicioFormat = $dataInicio->format('d/m/Y');
            $dataFinalFormat = $dataFinal->format('d/m/Y');
            $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial={$dataInicioFormat}&dataFinal={$dataFinalFormat}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $resposta = curl_exec($ch);
            curl_close($ch);

            if (!$resposta) {
                $response->getBody()->write(json_encode(["erro" => "Erro na requisição à API do Banco Central."]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

            $valoresApi = json_decode($resposta, true);

            if (!is_array($valoresApi) || empty($valoresApi)) {
                $response->getBody()->write(json_encode(["erro" => "Nenhuma taxa de juros retornada pela API."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $valorTotal = 0.0;

            foreach ($valoresApi as $valor) {
                if (isset($valor['valor'])) {
                    $valorTotal += floatval(str_replace(',', '.', $valor['valor']));
                }
            }

// Arredonda para 2 casas decimais
            $valorTotal = round($valorTotal, 2);

            // Salva a taxa acumulada no banco
            $jurosModel = new taxaJuros(
                $dados['dataInicio'],
                $dados['dataFinal'],
                $valorTotal,
                uniqid()
            );
            $jurosDao = new JurosDAO();
            $jurosDao->salvarJuros($jurosModel);

            $response->getBody()->write(json_encode([
                "sucesso" => "A taxa de juros foi atualizada com sucesso.",
                "taxaAcumulada" => $valorTotal
            ]));


            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["erro" => "Erro interno: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
