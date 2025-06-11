<?php

namespace App\Dao;

use PDO;
use App\Model\Estatistica;
use App\Connection\DataConnection;
use PDOException;
use Exception;

class EstatisticaDAO
{
    private $connection;
    public function __construct ()
    {
        $this->connection = DataConnection::get_connection();
    }

    /**
     * @return Estatistica
     * @throws Exception
     */
    public function calcularEstatisticas (): Estatistica
    {
        try {

            $sql = "
                SELECT
                    COUNT(DISTINCT c.id) AS count,
                    COALESCE(SUM(c.valorEntrada + IFNULL(p.totalParcelas, 0)), 0) AS sum,
                    COALESCE(AVG(c.valorEntrada + IFNULL(p.totalParcelas, 0)), 0) AS avg,
                    COALESCE(SUM(IFNULL(p.totalParcelas, 0)), 0) AS sumTx,
                    COALESCE(AVG(IFNULL(p.totalParcelas, 0)), 0) AS avgTx
                FROM compras c
                LEFT JOIN (
                    SELECT idCompra, SUM(valorParcela) AS totalParcelas
                    FROM parcelas
                    GROUP BY idCompra
                ) p ON c.id = p.idCompra";


            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return new Estatistica((int)$row['count'], (float)$row['sum'], (float)$row['avg'], (float)$row['sumTx'], (float)$row['avgTx']);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao calcular estatísticas: " . $e->getMessage());
        }
    }
}