<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class BaixaPatrimonioService
{
    /**
    * @author: Gabriella
    * @created: 08/03/2024
    * @summary: gera sequencial
    * @param $database: conexão com o banco de dados
    */
    public static function gerarSequencia($database)
    {
        $sequencia = GBSequencia::gerarSequencia('cd_baixapatrimonio', $database, 'BaixaPatrimonio');
        return $sequencia;
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de baixa, fazendo as validações necessárias
    * @param $baixa: registro de baixa a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($baixa, $conexao)
    {  
        $baixa->delete();
    }
}