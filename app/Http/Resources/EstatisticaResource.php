<?php

class EstatisticaResource {
    public static function toArray($data) {
        return [
            'count' => $data['count'],
            'sum' => round($data['sum'], 2),
            'avg' => round($data['avg'], 2),
            'sumTx' => round($data['sumTx'], 4),
            'avgTx' => round($data['avgTx'], 4),
        ];
    }
}