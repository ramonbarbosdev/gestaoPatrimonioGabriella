<?php

class ConsultaPatrimonioGrupoService
{
    /*
    @author: Gabriella
    @created: 07/03/2024
    @summary: consulta patrimonio por grupo
    @param $conn: conexão com o banco de dados
    */
    public static function consultaPatrimonioGrupo($conn)
    {

        $consulta = $conn->query("
                                select gb.nm_grupobem, ib.nm_itembem, s.nm_setor, p.tp_situacao, p.vl_bem, p.dt_aquisicao 
                                from patrimonio p 
                                inner join item_bem ib on ib.id_itembem = p.id_itembem 
                                inner join grupo_bem gb on gb.id_grupobem = ib.id_grupobem 
                                inner join setor s on s.id_setor = p.id_setor 
                                ");

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
}