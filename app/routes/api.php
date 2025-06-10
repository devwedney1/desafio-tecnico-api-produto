<?php

use Slim\App;
use \App\Http\Controllers\Compra;

/** @var App $app */

$app->group('/api/', function ($group) use ($app) {
    $group->get('compras', [Compra::class, 'index']);
});
