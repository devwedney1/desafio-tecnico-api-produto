<?php

namespace App\Http\Controllers;

use App\Dao\CompraDAO;
use App\Http\Resources\CompraResource;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


Class CompraController
{
    private CompraDAO $compraDAO;
    private CompraResource $compraResource;

    public function __construct ()
    {
        $this->compraDAO = new CompraDAO();
        $this->compraResource = new CompraResource();
    }
    public function index (Request $request, Response $response): Response
    {
        try {
            $dataCompraGet = $this->compraDAO->todasComprasGet();


            if(!$dataCompraGet){
                $response->getBody()->write(json_encode([
                    'error' => 'A API não encontrou compras'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $dataCompraGetTratadoSucess = $this->compraResource->make($dataCompraGet);

            $response->getBody()->write(($dataCompraGetTratadoSucess));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode([
                'error' => 'A API não encontrou compras, problema não esperado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404); // Bad Request - JSON inválido
        }
    }
}