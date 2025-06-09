<?php
// Código da Cmpra
require_once __DIR__ . '/../app/Http/Requests/CompraRequest.php';
require_once __DIR__ . '/../app/Models/Compra.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/compras') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            exit;
        }

        $validador = new CompraRequest();
        $erros = $validador->validar($input);

        if (!empty($erros)) {
            http_response_code(422);
            exit;
        }

        $compra = new Compra();
        $sucesso = $compra->registrar($input);

        http_response_code($sucesso ? 201 : 422);
    } catch (Exception $e) {
        http_response_code(400);
    }
}