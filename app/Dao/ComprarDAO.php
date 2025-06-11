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
    public function inserirParcelas(array $parcelas)
    {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare(
                'INSERT INTO parcelas (id, idCompra, numeroParcela, valorParcela, dataVencimento) 
                 VALUES (:id, :idCompra, :numeroParcela, :valorParcela, :dataVencimento)'
            );
            
            foreach ($parcelas as $parcela) {
                if (!isset($parcela['id'], $parcela['idCompra'], $parcela['numeroParcela'], 
                          $parcela['valorParcela'], $parcela['dataVencimento'])) {
                    throw new PDOException("Dados da parcela incompletos");
                }

                $stmt->bindValue(':id', $parcela['id']);
                $stmt->bindValue(':idCompra', $parcela['idCompra']);
                $stmt->bindValue(':numeroParcela', $parcela['numeroParcela'], PDO::PARAM_INT);
                $stmt->bindValue(':valorParcela', number_format($parcela['valorParcela'], 2, '.', ''), PDO::PARAM_STR);
                $stmt->bindValue(':dataVencimento', $parcela['dataVencimento']);
                
                if (!$stmt->execute()) {
                    throw new PDOException("Erro ao inserir parcela: " . implode(", ", $stmt->errorInfo()));
                }
            }

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new PDOException("Erro ao inserir parcelas: " . $e->getMessage());
        }
    }



    public function buscarTaxaJurosAtual(): ?array
{
    $sql = "SELECT id, taxa FROM taxa_juros WHERE CURRENT_DATE BETWEEN dataInicio AND dataFinal LIMIT 1";
    $stmt = $this->conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}


    public function inserir(Compra $compra): void
{
    $sql = "INSERT INTO compras (id, idProduto, valorEntrada, qtdParcelas, idTaxaJuros)
            VALUES (:id, :idProduto, :valorEntrada, :qtdParcelas, :idTaxaJuros)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $compra->getId());
    $stmt->bindValue(':idProduto', $compra->getIdProduto());
    $stmt->bindValue(':valorEntrada', $compra->getValorEntrada());
    $stmt->bindValue(':qtdParcelas', $compra->getQtdParcelas());
    $stmt->bindValue(':idTaxaJuros', $compra->getIdTaxaJuros());

    $stmt->execute();
}



    public function buscarValorProduto(string $idProduto): float
    {
        $sql = "SELECT valorProduto FROM produtos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $idProduto, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['valorProduto'] : 0.0;
    }
}
