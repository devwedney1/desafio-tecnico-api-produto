<?php

namespace App\Model;

class Produto
{
    private $id;
    private $nome;
    private $tipo;
    private $valor;
    
    public function __construct($id = null, $nome = null, $tipo = null, $valor = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->valor = $valor;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getNome()
    {
        return $this->nome;
    }
    
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }
    
    public function getTipo()
    {
        return $this->tipo;
    }
    
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }
    
    public function getValor()
    {
        return $this->valor;
    }
    
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }
    
    public function toArray()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'tipo' => $this->tipo,
            'valor' => $this->valor
        ];
    }
    
    public static function fromArray($data)
    {
        return new self(
            $data['id'] ?? null,
            $data['nome'] ?? null,
            $data['tipo'] ?? null,
            $data['valor'] ?? null
        );
    }
}