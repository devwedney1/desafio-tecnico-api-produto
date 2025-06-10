<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

Class Compra
{
    private Compra $compra;
    public function __construct (Compra $compra)
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