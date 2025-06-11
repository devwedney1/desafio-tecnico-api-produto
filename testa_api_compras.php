<?php
// Script para testar a rota /api/compras

$url = 'http://localhost:8989/api/compras';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: text/plain; charset=utf-8');
echo "Status HTTP: $httpcode\n\n";

if ($httpcode === 200) {
    $json = json_decode($response, true);
    print_r($json);
} else {
    echo $response;
} 