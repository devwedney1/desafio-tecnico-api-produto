<?php

require_once 'taxaJuros.php';
require_once '../connection/produto.php';

class JurosDAO {

    private $conn;

    public function __construct() {
        $this->conn = DataConnection::get_connection();        
    }

    public function salvarJuros(Juros $juros) {
        $stmt = $this->conn->prepare(
            'UPDATE taxa_juros 
             SET dataInicio = ?, dataFinal = ?, taxa = ?, updated_at = NOW() 
             WHERE id = ?'
        );

        $stmt->execute([
            $juros->getDataInicial(),
            $juros->getDataFinal(),
            $juros->getJuros(),
            $juros->getId()
        ]);
    }

    public function mostrarJuros() {
        $stmt = $this->conn->prepare(
            'SELECT id, taxa, dataInicio, dataFinal FROM taxa_juros WHERE deleted_at IS NULL ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $juros = new Juros();
            $juros->setId($row['id']);
            $juros->setJuros($row['taxa']);
            $juros->setDataInicial($row['dataInicio']);
            $juros->setDataFinal($row['dataFinal']);
            return $juros;
        }

        return null;
    }
}