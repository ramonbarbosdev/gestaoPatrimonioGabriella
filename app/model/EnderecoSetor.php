<?php

class EnderecoSetor extends TRecord
{
    const TABLENAME  = 'endereco_setor';
    const PRIMARYKEY = 'id_endereco';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $setor;

    

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
        parent::addAttribute('sg_uf');
        parent::addAttribute('nm_cidade');
        parent::addAttribute('id_setor');
            
    }

    /**
     * Method set_setor
     * Sample of usage: $var->setor = $object;
     * @param $object Instance of Setor
     */
    public function set_setor(Setor $object)
    {
        $this->setor = $object;
        $this->id_setor = $object->id_setor;
    }

    /**
     * Method get_setor
     * Sample of usage: $var->setor->attribute;
     * @returns Setor instance
     */
    public function get_setor()
    {
    
        // loads the associated object
        if (empty($this->setor))
            $this->setor = new Setor($this->id_setor);
    
        // returns the associated object
        return $this->setor;
    }

    
}

