<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class TransferenciaPatrimonioService
{
    public static function gerarSequencia($database)
    {
        $sequencia = GBSequencia::gerarSequencia('cd_transferenciapatrimonio', $database, 'TransferenciaPatrimonio');
        return $sequencia;
    }
}