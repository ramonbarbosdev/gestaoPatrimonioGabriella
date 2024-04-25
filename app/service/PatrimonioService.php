<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class PatrimonioService
{
    /**
    * @author: Gabriella
    * @created: 08/03/2024
    * @summary: gera sequencial
    * @param $database: conexão com o banco de dados
    */
    public static function gerarSequencia($database)
    {
        $sequencia = GBSequencia::gerarSequencia('nu_plaqueta', $database, 'Patrimonio');
        return $sequencia;
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de patrimonio, fazendo as validações necessárias
    * @param $patrimonio: registro de patrimonio a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($patrimonio, $conexao)
    {  
        $query = "select cast(1 as bool) as fl_existe
                from item_baixa ib
                where ib.id_patrimonio = :id_patrimonio
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_patrimonio', $patrimonio->id_patrimonio, PDO::PARAM_INT);
        $stmt->execute();
                
        $itemBaixa = $stmt->fetchObject();
        
        if(isset($itemBaixa) && $itemBaixa == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Item Baixa");
        }

        $query = "select cast(1 as bool) as fl_existe
                from item_transferencia it
                where it.id_patrimonio = :id_patrimonio
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_patrimonio', $patrimonio->id_patrimonio, PDO::PARAM_INT);
        $stmt->execute();
                
        $itemTransferencia = $stmt->fetchObject();
        
        if(isset($itemTransferencia) && $itemTransferencia == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Item Transferencia");
        }

        $patrimonio->delete();
    }
}