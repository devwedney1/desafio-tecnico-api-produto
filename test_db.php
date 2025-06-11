<?php

require_once __DIR__ . '/app/config/database.php';

$config = require __DIR__ . '/app/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port=3306;dbname={$config['dbname']};charset=utf8";
    $pdo = new PDO($dsn, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão com o banco de dados estabelecida com sucesso!\n";
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage() . "\n";
} 