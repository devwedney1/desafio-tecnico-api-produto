<?php
namespace App\Models\Dao;

use PDO;
use App\Models\Estatistica;

class EstatisticaDAO
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function calcularEstatisticas(): Estatistica
    {
        $sql = "
            SELECT 
                COUNT(*) AS count,
                COALESCE(SUM(valorTotal), 0) AS sum,
                COALESCE(AVG(valorTotal), 0) AS avg,
                COALESCE(SUM(valorJuros), 0) AS sumTx,
                COALESCE(AVG(valorJuros), 0) AS avgTx
            FROM compras
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return new Estatistica(
                (int)$row['count'],
                (float)$row['sum'],
                (float)$row['avg'],
                (float)$row['sumTx'],
                (float)$row['avgTx']
            );
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao calcular estatísticas: " . $e->getMessage());
        }
    }
}