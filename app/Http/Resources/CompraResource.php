<?php

namespace App\Http\Resources;

class CompraResource {
    public static function make($data)
    {
        $compras = [];

        foreach ($data as $item) {
            $compras[] = [
                'idCompra'      => (string) ($item['idCompra'] ?? 0),
                'nomeProduto'   => (string) ($item['nomeProduto'] ?? 0),
                'tipoProduto'   => (string) ($item['tipoProduto'] ?? 0),
                'valorEntrada'  => $item['valorEntrada'] ?? null,
                'qtdParcelas'   => (int) ($item['qtdParcelas'] ?? 0),
                'valorParcelas' => (float) ($item['valorParcela'] ?? 0),
                'taxaJuros'     => (float) ($item['taxaJuros'] ?? 0),
            ];
        }

        return json_encode($compras);
    }
}
