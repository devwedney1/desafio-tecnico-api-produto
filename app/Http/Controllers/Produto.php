<?php

namespace App\Http\Controllers;

use App\Dao\ProdutoDao;
use App\Model\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProdutoController
{
    private $produtoDao;
    
    public function __construct()
    {
        $this->produtoDao = new ProdutoDao();
    }
    
    public function create(Request $request, Response $response)
    {
        try {
            // Pega o corpo da requisição
            $body = $request->getBody()->getContents();
            
            // Verifica se o JSON é válido
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $response->withStatus(400); // Bad Request - JSON inválido
            }
            
            // Validações obrigatórias
            if (!$this->validateRequiredFields($data)) {
                return $response->withStatus(422); // Unprocessable Entity
            }
            
            // Validação de valor negativo
            if ($data['valor'] < 0) {
                return $response->withStatus(422); // Unprocessable Entity
            }
            
            // Verifica se o ID já existe
            if ($this->produtoDao->existsById($data['id'])) {
                return $response->withStatus(422); // Unprocessable Entity - ID já existe
            }
            
            // Cria o produto
            $produto = new Produto(
                $data['id'],
                $data['nome'],
                $data['tipo'] ?? null,
                $data['valor']
            );
            
            // Salva no banco
            $this->produtoDao->save($produto);
            
            return $response->withStatus(201); // Created
            
        } catch (Exception $e) {
            // Log do erro seria interessante aqui
            return $response->withStatus(500); // Internal Server Error
        }
    }
    
    private function validateRequiredFields($data)
    {
        // Verifica se os campos obrigatórios estão presentes e não estão vazios
        if (!isset($data['id']) || empty(trim($data['id']))) {
            return false;
        }
        
        if (!isset($data['nome']) || empty(trim($data['nome']))) {
            return false;
        }
        
        if (!isset($data['valor']) || !is_numeric($data['valor'])) {
            return false;
        }
        
        return true;
    }
}