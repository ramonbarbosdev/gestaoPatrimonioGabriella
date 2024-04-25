<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class ItemBemService
{
    /**
    * @author: Gabriella
    * @created: 08/03/2024
    * @summary: gera sequencial
    * @param $database: conexão com o banco de dados
    */
    public static function gerarSequencia($database)
    {
        $sequencia = GBSequencia::gerarSequencia('cd_itembem', $database, 'ItemBem');
        return $sequencia;
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de item/bem, fazendo as validações necessárias
    * @param $itemBem: registro de item/bem a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($itemBem, $conexao)
    {  
        $query = "select cast(1 as bool) as fl_existe
                from patrimonio p
                where p.id_itembem = :id_itembem
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_itembem', $itemBem->id_itembem, PDO::PARAM_INT);
        $stmt->execute();
                
        $patrimonio = $stmt->fetchObject();
        
        if(isset($patrimonio) && $patrimonio == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Patrimônio");
        }

        $itemBem->delete();
    }
}