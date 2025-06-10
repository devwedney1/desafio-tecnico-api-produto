<?php


class Juros{
private $dataInicial;
private $dataFinal;
private$juros;

public function __construct($dataInicial,$dataFinal,$juros){
    $this->dataInicial = $dataInicial;
    $this->dataFinal = $dataFinal;
    $this->juros = $juros;
}

public function getDataInicial(){return $this->dataInicial;}
public function getDataFinal(){return $this->dataFinal;}
public function getJuros(){return $this->juros;}
public function setDataInicial($dataInicial){$this->dataInicial = $dataInicial;}
public function setDataFinal($dataFinal){$this->dataFinal = $dataFinal;}
public function setJuros($juros){$this->juros = $juros;}
}
