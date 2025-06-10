<?php

use Slim\App;
use \App\Http\Controllers\Compra;
use \App\Http\Controllers\ProdutoController;

/** @var App $app */

$app->group('/api', function ($group) use ($app) {
    $group->get('/compras', [Compra::class, 'index']);
    $group->post('/produtos', [ProdutoController::class, 'create']);
});