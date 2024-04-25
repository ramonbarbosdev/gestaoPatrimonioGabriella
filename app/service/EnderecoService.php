<?php

class EnderecoService
{
    /**
    * @author: Gabriella
    * @created: 27/02/2024
    * @summary: conexão com api de cep
    * @param $fieldValue: número do cep
    */
    public static function cepApi($fieldValue)
    {
        $cep = preg_replace('/[^0-9]/', '', $fieldValue);
        $url = 'https://viacep.com.br/ws/'.$cep.'/json/';
        
        $content = @file_get_contents($url);
        
        if ($content !== false)
        {
            $cep_data = json_decode($content);
            
            return $cep_data;
        }
    }

    /**
    * @author: Gabriella
    * @created: 27/02/2024
    * @summary: Função para salvar endereço e obter seu id, em seguida, salvar a ficha cadastral
    * @param $objeto: objeto de ficha cadastral para receber o id de endereço
    */
    public static function onSaveEndereco($objeto, $param)
    {
        $endereco = new Endereco();
        $endereco->id_endereco    = $param['id_endereco'];
        $endereco->nu_cep         = $param['nu_cep'];
        $endereco->ds_logradouro  = $param['ds_logradouro'];
        $endereco->nm_bairro      = $param['nm_bairro'];
        $endereco->nu_endereco    = $param['nu_endereco'];
        $endereco->ds_complemento = $param['ds_complemento'];
        $endereco->sg_uf          = $param['sg_uf'];
        $endereco->nm_cidade      = $param['nm_cidade'];  
         
        $endereco->id_fichacadastral = $objeto->id_fichacadastral;

        $endereco->store();
    }
}