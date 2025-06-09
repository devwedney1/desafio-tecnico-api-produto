<?php
require_once __DIR__ . '/../../Models/Produto.php';

class CompraRequest {
    public function validar($data) {
        $erros = [];

        if (!isset($data['id'], $data['valorEntrada'], $data['qtdParcelas'], $data['idProduto'])) {
            return ['Campos obrigatórios ausentes:'];
        }

        if (!is_numeric($data['valorEntrada']) || !is_numeric($data['qtdParcelas'])) {
            $erros[] = "Campos numéricos inválidos";
        }

        if ($data['qtdParcelas'] < 0) {
            $erros[] = "Quantidade de parcelas inválida";
        }

        $produto = new Produto();
        $infoProduto = $produto->buscaPorId($data['idProduto']); 

        if (!$infoProduto) {
            $erros[] = "Produto não encontrado.";
        } else if ($data['valorEntrada'] > $infoProduto['valor']) {
            $erros[] = "Entrada maior que valor do produto";
        }
        
        // TODO: verificar unicidade do id em 'compras'

        return $erros;
    }
}