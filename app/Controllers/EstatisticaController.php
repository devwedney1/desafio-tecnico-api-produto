<?php

require_once __DIR__ . '/../Models/Compra.php';
require_once __DIR__ . '/../Http/Resources/EstatisticaResource.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EstatisticaController
{
    public function index(Request $request, Response $response): Response
    {
        $compra = new Compra();
        $dados = $compra->getEstatisticas();

        $payload = json_encode(EstatisticaResource::toArray($dados));

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}