<?php

namespace App\Model;

class Compra
{
    private int $id;
    private int $idProduto;
    private float $valorEntrada;
    private int $qtdParcelas;
    private float $vlrParcela;
    private float $jurosAplicado;

    public function __construct(int $idProduto, float $valorEntrada, int $qtdParcelas, float $vlrParcela)
    {
        $this->idProduto = $idProduto;
        $this->valorEntrada = $valorEntrada;
        $this->qtdParcelas = $qtdParcelas;
        $this->vlrParcela = $vlrParcela;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getIdProduto(): int { return $this->idProduto; }
    public function setIdProduto(int $idProduto): void { $this->idProduto = $idProduto; }

    public function getValorEntrada(): float { return $this->valorEntrada; }
    public function setValorEntrada(float $valorEntrada): void { $this->valorEntrada = $valorEntrada; }

    public function getQtdParcelas(): int { return $this->qtdParcelas; }
    public function setQtdParcelas(int $qtdParcelas): void { $this->qtdParcelas = $qtdParcelas; }

    public function getVlrParcela(): float { return $this->vlrParcela; }
    public function setVlrParcela(float $vlrParcela): void { $this->vlrParcela = $vlrParcela; }

    public function getJurosAplicado(): float { return $this->jurosAplicado; }
    public function setJurosAplicado(float $jurosAplicado): void { $this->jurosAplicado = $jurosAplicado; }
}
