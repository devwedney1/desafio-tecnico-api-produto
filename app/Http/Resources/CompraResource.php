<?php

namespace App\Http\Resources;

class CompraResource {
    public static function make($data): false|string
    {

        $compras =[
            'idCompra' => (string) $data['id'],
            'nomeProduto' => (string) $data['valorEntrada'],
            'tipoProduto' => (int) $data['qtdParcelas'],
            'valorEntrada' => $data['idProduto'],
            'qtdParcelas' => (float) $data['juros_aplicado'],
            'valorParcelas' => (float) $data['valor_parcela'],
            'taxaJuros' => (float) $data['taxaJuros'],
        ];

        return json_encode($compras);
    }
}