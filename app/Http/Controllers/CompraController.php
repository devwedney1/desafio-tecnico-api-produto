<?php

namespace App\Http\Controllers;

use App\Model\Parcela;
use App\Model\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

Class CompraController
{
    private Compra $compra;
    public function __construct (Produto $produto, Compra $compra, Parcela $parcela, )
    {
        $this->compra = $compra;
    }
    public function index (Response $response)
    {
        try {

        } catch (\Throwable $th) {

        }
        $response->getBody()->write(json_encode());
        return $response->withHeader('Content-Type', 'application/json');
    }
}