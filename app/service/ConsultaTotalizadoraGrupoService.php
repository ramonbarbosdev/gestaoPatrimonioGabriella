<?php

class ConsultaTotalizadoraGrupoService
{
    /*
    @author: Gabriella
    @created: 08/03/2024
    @summary: soma o total de grupo bens
    @param $conn: conexão com o banco de dados
    */
    public static function consultaTotalGrupo($conn)
    {

        $consulta = $conn->query("
                                select gb.nm_grupobem, sum(p.vl_bem) as vl_total from grupo_bem gb 
                                inner join item_bem ib on ib.id_grupobem = gb.id_grupobem 
                                inner join patrimonio p on p.id_itembem = ib.id_itembem 
                                group by gb.nm_grupobem 
                                ");

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
}