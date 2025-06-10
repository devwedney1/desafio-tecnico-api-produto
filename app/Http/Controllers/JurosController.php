<?php

namespace App\Http\Controllers;

require_once './app/Dao/CompraDAO.php';
require_once './app/Dao/JurosDAO.php';
require_once './app/Dao/juros.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JurosController
{
    public function atualizarJuros(Request $request, Response $response): Response
    {
        $dados = $request->getParsedBody();

        if (!isset($dados['dataInicio'], $dados['dataFinal'])) {
            $response->getBody()->write(json_encode(["erro" => "Parâmetros obrigatórios ausentes."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $dataInicio = new \DateTime($dados['dataInicio']);
            $dataFinal = new \DateTime($dados['dataFinal']);
            $hoje = new \DateTime();
            $limiteInferior = new \DateTime('2010-01-01');

            if ($dataFinal < $dataInicio || $dataFinal > $hoje || $dataInicio < $limiteInferior) {
                $response->getBody()->write(json_encode(["erro" => "Datas inválidas."]));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $dataInicioFormat = $dataInicio->format('d/m/Y');
            $dataFinalFormat = $dataFinal->format('d/m/Y');

            $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial={$dataInicioFormat}&dataFinal={$dataFinalFormat}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $responseApi = curl_exec($ch);
            curl_close($ch);

            if (!$responseApi) {
                $response->getBody()->write(json_encode(["erro" => "Falha na requisição à API do Banco Central."]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

            $valoresApi = json_decode($responseApi, true);

            if (!is_array($valoresApi) || empty($valoresApi)) {
                $response->getBody()->write(json_encode(["erro" => "Resposta inválida da API do Banco Central."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $valorTotal = 0.0;
            $quantidade = 0;

            foreach ($valoresApi as $valor) {
                if (isset($valor['valor'])) {
                    $valorTotal += floatval(str_replace(',', '.', $valor['valor']));
                    $quantidade++;
                }
            }

            if ($quantidade === 0) {
                $response->getBody()->write(json_encode(["erro" => "Nenhum valor de taxa Selic encontrado."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $taxaMedia = round($valorTotal / $quantidade, 4);

            $compraDAO = new ComprasDAO();
            $compraDAO->atualizarBDCompras($taxaMedia);

            $jurosModel = new \Juros($dados['dataInicio'], $dados['dataFinal'], $taxaMedia, '1');
            $jurosDAO = new \JurosDAO();
            $jurosDAO->salvarJuros($jurosModel);

            $response->getBody()->write(json_encode(["sucesso" => "A taxa de juros foi atualizada com sucesso."]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(["erro" => "Erro interno: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}