<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Dao\EstatisticaDAO;

class EstatisticaController
{

    private EstatisticaDAO $estatisticaDAO;

    public function __construct ()
    {
        $this->estatisticaDAO = new EstatisticaDAO;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index (Request $request, Response $response): Response
    {
        try {
            $estatistica = $this->estatisticaDAO->calcularEstatisticas();

            $data = [
                'count' => $estatistica->getCount(),
                'sum' => $estatistica->getSum(),
                'avg' => $estatistica->getAvg(),
                'sumTx' => $estatistica->getSumTx(),
                'avgTx' => $estatistica->getAvgTx(),
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['erro' => 'Erro ao buscar estatísticas', 'detalhe' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}