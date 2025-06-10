<?php

use Slim\App;
use \App\Http\Controllers\Compra;
use App\Http\Controllers\EstatisticaController;

/** @var App $app */

$app->group('/api/', function ($group) use ($app) {
    $group->get('compras', [Compra::class, 'index']);
    $group->get('estatistica', [EstatisticaController::class, 'index']);
});
