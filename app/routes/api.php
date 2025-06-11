<?php

use App\Http\Controllers\CompraController;
use Slim\App;
use \App\Http\Controllers\ProdutoController;
use \App\Http\Controllers\EstatisticaController;
use App\Http\Controllers\ComprarController;
use App\Http\Controllers\JurosController;

/** @var App $app */

/* $app->group('/api', function ($group) use ($app) {
    $group->get('/compras', [Compra::class, 'index']);
    $group->post('/produtos', [ProdutoController::class, 'create']);
}); */

return function (App $app) {
    $app->group('/api', function ($group) {
        $group->get('/compras', [CompraController::class, 'index']);
        $group->post('/produtos', [ProdutoController::class, 'store']);
        $group->post('/comprar', [ComprarController::class, 'criar']);
        $group->get('/estatistica', [EstatisticaController::class, 'index']);
        $group->put('/juros', JurosController::class . ':atualizarJuros');
    });
};
