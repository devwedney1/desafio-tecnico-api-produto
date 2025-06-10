<?php

use Slim\App;
use \App\Http\Controllers\Compra;
use App\Http\Controllers\EstatisticaController;
use App\Http\Controllers\JurosController;

/** @var App $app */

$app->group('/api/', function ($group) use ($app) {
    $group->get('compras', [Compra::class, 'index']);
    $group->get('estatistica', [EstatisticaController::class, 'index']);
    $group->put('juros', [JurosController::class, 'index']);
});
