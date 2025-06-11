<?php

namespace App\Utils\Uuid;

use Exception;

Class GeradorUuid
{
    /**
     * Metodo para criar automaticamente uuid para tables na coluna id (primary Key)
     *
     * @return string|array
     *
     * @author Lucas Wedney
     */
    public static function gerarUuidPrimaryKey(): string|array
   {
       try {
           $data = random_bytes(16);
           $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // versão 4
           $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante RFC 4122
           return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
       } catch (Exception $e) {
           error_log($e->getMessage());
           return ['error' => 'Problema no momento da criar um UUID'];
       }
   }
}