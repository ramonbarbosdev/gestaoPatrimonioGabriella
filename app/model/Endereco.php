<?php

class Endereco extends TRecord
{
    const TABLENAME  = 'endereco';
    const PRIMARYKEY = 'id_endereco';
    const IDPOLICY   = 'serial'; // {max, serial}

    private $fichacadastral;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nu_cep');
        parent::addAttribute('ds_logradouro');
        parent::addAttribute('nm_bairro');
        parent::addAttribute('nu_endereco');
        parent::addAttribute('ds_complemento');
        parent::addAttribute('nm_cidade');
        parent::addAttribute('sg_uf');
        parent::addAttribute('id_fichacadastral');
            
    }

    /**
     * Method set_ficha_cadastral
     * Sample of usage: $var->ficha_cadastral = $object;
     * @param $object Instance of FichaCadastral
     */
    public function set_fichacadastral(FichaCadastral $object)
    {
        $this->fichacadastral = $object;
        $this->id_fichacadastral = $object->id_fichacadastral;
    }

    /**
     * Method get_fichacadastral
     * Sample of usage: $var->fichacadastral->attribute;
     * @returns FichaCadastral instance
     */
    public function get_fichacadastral()
    {
    
        // loads the associated object
        if (empty($this->fichacadastral))
            $this->fichacadastral = new FichaCadastral($this->id_fichacadastral);
    
        // returns the associated object
        return $this->fichacadastral;
    }
}

