<?php

namespace App\Dao;

use App\Model\Produto;
use App\Connection\DataConnection;
use PDO;
use PDOException;
use Exception;

class ProdutoDao
{
    private $connection;
    private const tableName = 'produtos';

    public function __construct()
    {
        $this->connection = DataConnection::get_connection();
        
//        if ($this->connection === null) {
//            throw new Exception("Não foi possível estabelecer conexão com o banco de dados.");
//        }
    }

    /**
     * @param Produto $produto
     *
     * @return bool
     */
    public function create(Produto $produto): bool
    {
        try {
            $this->connection->beginTransaction();
            $sql = "INSERT INTO " . self::tableName . " (id, nome, tipo, valorProduto) 
                    VALUES (:id, :nome, :tipo, :valor)";
            $stmt = $this->connection->prepare($sql);

            $stmt->bindValue(':id', $produto->getId());
            $stmt->bindValue(':nome', $produto->getNome());
            $stmt->bindValue(':tipo', $produto->getTipo());
            $stmt->bindValue(':valor', $produto->getValor());
            
            $stmt->execute();

            $this->connection->commit();

            return true;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw new PDOException("ERRO ao cadastrar um filme: " . $e->getMessage());
            return false;
        }
    }
    
    public function findById($id)
    {
        try {
            $sql = "SELECT id, nome, tipo, valorProduto as valor FROM produtos WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                return Produto::fromArray($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produto: " . $e->getMessage());
        }
    }
    
    public function existsById($id)
    {
        try {
            $sql = "SELECT COUNT(*) FROM produtos WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar existência do produto: " . $e->getMessage());
        }
    }
    
    public function findAll()
    {
        try {
            $sql = "SELECT id, nome, tipo, valorProduto as valor FROM produtos";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            
            $produtos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produtos[] = Produto::fromArray($row);
            }
            
            return $produtos;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }
}