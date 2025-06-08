<?php

class CompraResource {
    public static function toArray($compra, $parcelas) {
        return [
            'id' => $compra['id'],
            'valorEntrada' => (float) $compra['valorEntrada'],
            'qtdParcelas' => (int) $compra['qtdParcelas'],
            'idProduto' => $compra['idProduto'],
            'jurosAplicado' => (float) $compra['juros_aplicado'],
            'parcelas' => array_map(function ($p) {
                return [
                    'numero' => (int) $p['numero'],
                    'valor' => (float) $p['valor']
                ];
            }, $parcelas)
        ];
    }
}