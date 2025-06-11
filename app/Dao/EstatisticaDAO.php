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
    
    public function __construct()
    {
        $this->connection = DataConnection::get_connection();
        
        //if ($this->connection === null) {
        //    throw new Exception("Não foi possível estabelecer conexão com o banco de dados.");
        //}
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
            $stmt = $this->connection->prepare($sql);
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