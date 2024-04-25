<?php

require_once (PATH . '/app/utils/GBSequencia.php');

class GrupoBemService
{
    /**
    * @author: Gabriella
    * @created: 08/03/2024
    * @summary: verifica se a sequencia digitada ja foi cadastrada
    * @param $cd_grupobem: sequencia digitada
    * @param $primaryKey: chave primaria
    * @param $activeRecord: classe
    * @param $database: conexao com o banco de dados
    * @param $class: metodo magico
    */
    public static function validarSequenciaExistente($cd_grupobem, $primaryKey,  $activeRecord, $database, $class) 
    {
        if(isset($cd_grupobem) && !empty($cd_grupobem))
        {
            $filtro[] = new TFilter('cd_grupobem', '=', $cd_grupobem);
            $resultado = GBSequencia::validarSequenciaExistente($activeRecord, $database, $primaryKey, $filtro, $class);

            if($resultado == true)
            {
                throw new Exception('Grupo bem já existe!'); 
            }
        }
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de grupo bem, fazendo as validações necessárias
    * @param $grupoBem: registro de grupo bem a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($grupoBem, $conexao)
    {         
        $query = "select cast(1 as bool) as fl_existe
                from item_bem ib
                where ib.id_grupobem = :id_grupobem
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_grupobem', $grupoBem->id_grupobem, PDO::PARAM_INT);
        $stmt->execute();
                
        $itemBem = $stmt->fetchObject();
        
        if(isset($itemBem) && $itemBem == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Item/Bem");
        }

        $grupoBem->delete();
    }
}