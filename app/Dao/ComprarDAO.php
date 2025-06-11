<?php

namespace App\DAO;

use App\Connection\DataConnection;
use App\Model\Compra;
use PDO;
use PDOException;

class ComprarDAO 
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = DataConnection::get_connection();
    }

    public function inserir(Compra $compra): void
    {
        $sql = "INSERT INTO compras (idProduto, valorEntrada, qtdParcelas, vlrParcela, jurosAplicados)
                VALUES (:idProduto, :valorEntrada, :qtdParcelas, :vlrParcela, :jurosAplicado)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idProduto', $compra->getIdProduto());
        $stmt->bindValue(':valorEntrada', $compra->getValorEntrada());
        $stmt->bindValue(':qtdParcelas', $compra->getQtdParcelas());
        $stmt->bindValue(':vlrParcela', $compra->getVlrParcela());
        $stmt->bindValue(':jurosAplicado', $compra->getJurosAplicado());

        $stmt->execute();
    }

    public function buscarValorProduto(int $idProduto): float
    {
        $sql = "SELECT valor FROM produtos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $idProduto);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['valor'] : 0.0;
    }
}
