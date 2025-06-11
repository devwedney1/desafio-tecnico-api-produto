<?php

namespace App\Model;

class Produto
{
    private string $id;
    private string $nome;
    private string $tipo;
    private float $valor;
    
    public function __construct($id, $nome, $tipo, $valor)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->valor = $valor;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function setId($id): static
    {
        $this->id = $id;
        return $this;
    }
    
    public function getNome(): string
    {
        return $this->nome;
    }
    
    public function setNome($nome): static
    {
        $this->nome = $nome;
        return $this;
    }
    
    public function getTipo(): string
    {
        return $this->tipo;
    }
    
    public function setTipo($tipo): static
    {
        $this->tipo = $tipo;
        return $this;
    }
    
    public function getValor(): float
    {
        return $this->valor;
    }
    
    public function setValor($valor): static
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
    
    public static function fromArray(array $data)
    {
        return new self(
            $data['id'] ?? null,
            $data['nome'] ?? null,
            $data['tipo'] ?? null,
            $data['valor'] ?? null
        );
    }
}