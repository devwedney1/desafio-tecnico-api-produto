<?php

class Juros {
    private string $id;
    private string $dataInicial;
    private string $dataFinal;
    private float $juros;

    public function __construct(string $dataInicial = '', string $dataFinal = '', float $juros = 0.0, string $id = '') {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
        $this->juros = $juros;
        $this->id = $id;
    }

    // Getters
    public function getId(): string {
        return $this->id;
    }

    public function getDataInicial(): string {
        return $this->dataInicial;
    }

    public function getDataFinal(): string {
        return $this->dataFinal;
    }

    public function getJuros(): float {
        return $this->juros;
    }

    // Setters
    public function setId(string $id): void {
        $this->id = $id;
    }

    public function setDataInicial(string $dataInicial): void {
        $this->dataInicial = $dataInicial;
    }

    public function setDataFinal(string $dataFinal): void {
        $this->dataFinal = $dataFinal;
    }

    public function setJuros(float $juros): void {
        $this->juros = $juros;
    }
}
