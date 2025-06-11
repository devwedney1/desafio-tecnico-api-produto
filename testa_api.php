<?php

function makeRequest($method, $endpoint, $data = null) {
    $ch = curl_init();
    $url = "http://nginx" . $endpoint;
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "Status HTTP: " . $httpCode . "\n";
    echo "Resposta: " . $response . "\n\n";
    
    curl_close($ch);
    return $response;
}

// 1. Criar produto
echo "1. Criando produto...\n";
$produto = [
    'id' => '123e4567-e89b-12d3-a456-426614174000',
    'nome' => 'Produto Teste',
    'tipo' => 'Teste',
    'valor' => 100.00
];
makeRequest('POST', '/api/produtos', $produto);

// 2. Atualizar juros
echo "2. Atualizando juros...\n";
$juros = [
    'id' => '123e4567-e89b-12d3-a456-426614174001',
    'dataInicio' => '2024-01-01',
    'dataFinal' => '2024-12-31'
];
makeRequest('PUT', '/api/juros', $juros);

// 3. Criar compra
echo "3. Criando compra...\n";
$compra = [
    'id' => '123e4567-e89b-12d3-a456-426614174002',
    'idProduto' => '123e4567-e89b-12d3-a456-426614174000',
    'valorEntrada' => 10.00,
    'qtdParcelas' => 3,
    'parcelas' => [
        ['id' => '123e4567-e89b-12d3-a456-426614174003'],
        ['id' => '123e4567-e89b-12d3-a456-426614174004'],
        ['id' => '123e4567-e89b-12d3-a456-426614174005']
    ]
];
makeRequest('POST', '/api/comprar', $compra);

// 4. Listar compras
echo "4. Listando compras...\n";
makeRequest('GET', '/api/compras');

// 5. Consultar estatísticas
echo "5. Consultando estatísticas...\n";
makeRequest('GET', '/api/estatistica'); 