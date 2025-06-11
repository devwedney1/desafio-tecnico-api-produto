<?php

namespace App\Http\Resources;
Class CompraResource
{
    public static function make($data): false|string
    {
        // Se for uma lista de compras
        if (is_array($data) && isset($data[0])) {
            $compras = array_map(function($compra) {
                return [
                    'idCompra'      => (string) ($compra['id']              ?? ''),
                    'nomeProduto'   => (string) ($compra['nomeProduto']     ?? ''),
                    'tipoProduto'   => (int)    ($compra['tipoProduto']     ?? 0),
                    'valorEntrada'  => (float)  ($compra['valorEntrada']    ?? 0),
                    'qtdParcelas'   => (int)    ($compra['qtdParcelas']     ?? 0),
                    'valorParcelas' => (float)  ($compra['valor_parcela']   ?? 0),
                    'taxaJuros'     => (float)  ($compra['taxaJuros']       ?? 0),
                ];
            }, $data);
            return json_encode($compras);
        }

        // Se for uma compra só
        $compra = [
            'idCompra'      => (string) ($data['id']              ?? ''),
            'nomeProduto'   => (string) ($data['nomeProduto']     ?? ''),
            'tipoProduto'   => (int)    ($data['tipoProduto']     ?? 0),
            'valorEntrada'  => (float)  ($data['valorEntrada']    ?? 0),
            'qtdParcelas'   => (int)    ($data['qtdParcelas']     ?? 0),
            'valorParcelas' => (float)  ($data['valor_parcela']   ?? 0),
            'taxaJuros'     => (float)  ($data['taxaJuros']       ?? 0),
        ];
        return json_encode($compra);
    }
}