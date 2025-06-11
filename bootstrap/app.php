<?php

use Slim\Factory\AppFactory;

use Slim\Middleware\BodyParsingMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Middleware para retornar JSON (obrigatório para APIs)
$app->addBodyParsingMiddleware();

// Middleware para lidar com erros

$app->addErrorMiddleware(true, true, true);

// Inclusão de rotas separadas
(require __DIR__ . '/../app/routes/api.php')($app);

return $app;
