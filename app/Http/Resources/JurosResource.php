<?php

namespace App\Http\Resources;

class JurosResource
{
    public static function toArray(float $taxa): array
    {
        return [
            'taxa' => round($taxa, 5)
        ];
    }
}
