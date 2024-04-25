<?php

class FichaCadastralService
{
    /**
    * @author: Gabriella
    * @created: 27/02/2024
    * @summary: verificação para não permitir o cadastro sem o campo CNPJ ou CPF
    * @param $tp_fichacadastral: tipo do cadastro (pf ou pj)
    * @param $nu_cpfcnpj: campo cpf/cnpj
    */
    public static function verificarCampoCNPJCPF($tp_fichacadastral, $nu_cpfcnpj)
    {
        if($tp_fichacadastral == 1 && empty($nu_cpfcnpj))
        {
            throw new Exception('É necessário preencher o campo CPF');
        }
        elseif($tp_fichacadastral == 2 && empty($nu_cpfcnpj))
        {
            throw new Exception('É necessário preencher o campo CNPJ');
        }
    }

    /**
    * @author: Gabriella
    * @created: 27/02/2024
    * @summary: verificação para não permitir o cadastro sem o campo Nome ou Razão Social
    * @param $tp_fichacadastral: tipo do cadastro (pf ou pj)
    * @param $nm_fichacadastral: campo nome/razão
    */
    public static function verificarCampoNomeRazao($tp_fichacadastral, $nm_fichacadastral)
    {
        if($tp_fichacadastral == 1 && empty($nm_fichacadastral)) //PF
        {
            throw new Exception('É necessário preencher o campo Nome Completo');
        }
        elseif($tp_fichacadastral == 2 && empty($nm_fichacadastral)) //PJ
        {
            throw new Exception('É necessário preencher o campo Razão Social');
        }
    }

    /**
    * @author: Gabriella
    * @created: 05/03/2024
    * @summary: Exclui o registro de ficha cadastral, fazendo as validações necessárias
    * @param $fichaCadastral: registro de ficha cadastral a ser excluído
    * @param $conexao: conexão com o banco de dados
    */
    public static function excluir($fichaCadastral, $conexao)
    {  
        $query = "select cast(1 as bool) as fl_existe
                from nota_fiscal nf
                where nf.id_fichacadastral = :id_fichacadastral
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_fichacadastral', $fichaCadastral->id_fichacadastral, PDO::PARAM_INT);
        $stmt->execute();
                
        $notaFiscal = $stmt->fetchObject();
        
        if(isset($notaFiscal) && $notaFiscal == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Nota Fiscal");
        }
        
        $query = "select cast(1 as bool) as fl_existe
                from setor s
                where s.id_fichacadastral = :id_fichacadastral
                limit 1;";
                
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':id_fichacadastral', $fichaCadastral->id_fichacadastral, PDO::PARAM_INT);
        $stmt->execute();
                
        $setor = $stmt->fetchObject();
        
        if(isset($setor) && $setor == true)
        {
            throw new Exception("Não é possível excluir. Existe vínculo com Setor");
        }

        $fichaCadastral->delete();
    }
}