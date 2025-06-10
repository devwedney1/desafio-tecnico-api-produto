<?php

namespace App\Models\Dao;

require_once __DIR__ . '/../../../database/DataConnection.php';

use DataConnection;

class JurosDao
{
    public function salvar(float $taxa, string $inicio, string $final): void
    {
        $conn = DataConnection::get_connection();

        if ($conn === null) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno ao conectar com o banco de dados.']);
            return;
        }

        $sql = "INSERT INTO juros (taxa, dataInicio, dataFinal) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$taxa, $inicio, $final]);
    }
}
