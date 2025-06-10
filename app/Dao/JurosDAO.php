<?php

require_once 'JurosModel.php';
require_once '../connection/produto.php';

class JurosDAO{

    private $conn;

    public function __construct() {
        $this->conn = DataConnection::get_connection();        
    }

    public function salvarJuros(Juros $juros){

        $stmt = $this->conn->prepare('UPDATE juros SET dataInicio = ?, dataFInal = ?, juros = ? WHERE id = 1');
        $stmt->execute([
            $juros->getDataInicial(),
            $juros->getDataFinal(),
            $juros->getJuros()
        ]);
    }
    public function mostrarJuros(){

    $stmt = $this->conn->prepare('SELECT juros from juros');
    $stmt->execute();
    $juros = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $juros['juros'];
    }
}
