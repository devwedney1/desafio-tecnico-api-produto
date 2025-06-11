<?php
namespace App\Model;

class Compra
{
    private string $id;
    private string $idProduto;
    private float $valorEntrada;
    private int $qtdParcelas;
    private ?float $vlrParcela = null;
    private ?string $idTaxaJuros = null;
    private ?float $jurosAplicado = null;

    public function __construct(string $id, string $idProduto, float $valorEntrada, int $qtdParcelas, float $vlrParcela)
    {
        $this->id = $id;
        $this->idProduto = $idProduto;
        $this->valorEntrada = $valorEntrada;
        $this->qtdParcelas = $qtdParcelas;
        $this->vlrParcela = $vlrParcela;
    }

    public function getId(): string {
        return $this->id;
}
    public function setId(string $id): void { $this->id = $id; }

    public function getIdProduto(): string { return $this->idProduto; }
    public function setIdProduto(string $idProduto): void { $this->idProduto = $idProduto; }

    public function getValorEntrada(): float { return $this->valorEntrada; }
    public function setValorEntrada(float $valorEntrada): void { $this->valorEntrada = $valorEntrada; }

    public function getQtdParcelas(): int { return $this->qtdParcelas; }
    public function setQtdParcelas(int $qtdParcelas): void { $this->qtdParcelas = $qtdParcelas; }

    public function getVlrParcela(): float { return $this->vlrParcela; }
    public function setVlrParcela(float $vlrParcela): void { $this->vlrParcela = $vlrParcela; }

    public function getJurosAplicado(): float { return $this->jurosAplicado; }
    public function setJurosAplicado(float $jurosAplicado): void { $this->jurosAplicado = $jurosAplicado; }

    public function getIdTaxaJuros(): string { return $this->idTaxaJuros; }
    public function setIdTaxaJuros(string $idTaxaJuros): void { $this->idTaxaJuros = $idTaxaJuros; }
}
