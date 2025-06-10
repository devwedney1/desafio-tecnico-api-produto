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
    
    public function __construct()
    {
        $this->connection = DataConnection::get_connection();
        
        if ($this->connection === null) {
            throw new Exception("Não foi possível estabelecer conexão com o banco de dados.");
        }
    }
    
    public function save(Produto $produto)
    {
        try {
            $sql = "INSERT INTO produtos (id, nome, tipo, valorProduto) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            
            return $stmt->execute([
                $produto->getId(),
                $produto->getNome(),
                $produto->getTipo(),
                $produto->getValor()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao salvar produto: " . $e->getMessage());
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