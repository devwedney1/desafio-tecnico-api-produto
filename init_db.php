<?php

try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Lê o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/app/database/produto.sql');
    
    // Executa o SQL
    $pdo->exec($sql);
    
    echo "Banco de dados inicializado com sucesso!\n";
} catch (PDOException $e) {
    echo "Erro ao inicializar banco de dados: " . $e->getMessage() . "\n";
    echo "Verifique se o MySQL está rodando e se as credenciais estão corretas.\n";
    echo "Certifique-se de que o arquivo php.ini tem a extensão pdo_mysql habilitada.\n";
} 