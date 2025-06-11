<?php

namespace App\DAO;
use PDO;

use App\Connection\DataConnection;
use App\Model\taxaJuros;

class JurosDAO {

    private $conn;

    public function __construct() {
        $this->conn = DataConnection::get_connection();        
    }

    public function salvarJuros(taxaJuros $juros) {
    $stmt = $this->conn->prepare(
        'INSERT INTO taxa_juros (id, dataInicio, dataFinal, taxa, updated_at) 
         VALUES (?, ?, ?, ?, NOW())'
    );

    $stmt->execute([
        $juros->getId(),
        $juros->getDataInicial(),
        $juros->getDataFinal(),
        $juros->getJuros()
    ]);
}

    public function buscarTaxaJurosAtual() {
    $stmt = $this->conn->prepare(
        'SELECT id, taxa 
         FROM taxa_juros 
         WHERE CURDATE() BETWEEN dataInicio AND dataFinal 
           AND deleted_at IS NULL 
         ORDER BY dataInicio DESC 
         LIMIT 1'
    );
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function mostrarJuros() {
        $stmt = $this->conn->prepare(
            'SELECT id, taxa, dataInicio, dataFinal FROM taxa_juros WHERE deleted_at IS NULL ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $juros = new taxaJuros();
            $juros->setId($row['id']);
            $juros->setJuros($row['taxa']);
            $juros->setDataInicial($row['dataInicio']);
            $juros->setDataFinal($row['dataFinal']);
            return $juros;
        }

        return null;
    }
}