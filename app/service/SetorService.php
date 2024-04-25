<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class SetorService
{
    /**
    * @author: Gabriella
    * @created: 08/03/2024
    * @summary: gera sequencial
    * @param $database: conexão com o banco de dados
    */
    public static function gerarSequencia($database)
    {
        $sequencia = GBSequencia::gerarSequencia('cd_setor', $database, 'Setor');
        return $sequencia;
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de setor, fazendo as validações necessárias
    * @param $setor: registro de setor a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($setor, $conexao)
    {  
        $query = "select cast(1 as bool) as fl_existe
                from patrimonio p
                where p.id_setor = :id_setor
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_setor', $setor->id_setor, PDO::PARAM_INT);
        $stmt->execute();
                
        $patrimonio = $stmt->fetchObject();
        
        if(isset($patrimonio) && $patrimonio == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Patrimonio");
        }

        $setor->delete();
    }
}