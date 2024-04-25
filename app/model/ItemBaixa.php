<?php

class ItemBaixa extends TRecord
{
    const TABLENAME  = 'item_baixa';
    const PRIMARYKEY = 'id_itembaixa';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $baixapatrimonio;
    private $patrimonio;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_baixapatrimonio');
        parent::addAttribute('id_patrimonio');
        parent::addAttribute('vl_itembaixa');
            
    }

    /**
     * Method set_baixa_patrimonio
     * Sample of usage: $var->baixa_patrimonio = $object;
     * @param $object Instance of BaixaPatrimonio
     */
    public function set_baixapatrimonio(BaixaPatrimonio $object)
    {
        $this->baixapatrimonio = $object;
        $this->id_baixapatrimonio = $object->id_baixapatrimonio;
    }

    /**
     * Method get_baixapatrimonio
     * Sample of usage: $var->baixapatrimonio->attribute;
     * @returns BaixaPatrimonio instance
     */
    public function get_baixapatrimonio()
    {
    
        // loads the associated object
        if (empty($this->baixapatrimonio))
            $this->baixapatrimonio = new BaixaPatrimonio($this->id_baixapatrimonio);
    
        // returns the associated object
        return $this->baixapatrimonio;
    }
    /**
     * Method set_patrimonio
     * Sample of usage: $var->patrimonio = $object;
     * @param $object Instance of Patrimonio
     */
    public function set_patrimonio(Patrimonio $object)
    {
        $this->patrimonio = $object;
        $this->id_patrimonio = $object->id_patrimonio;
    }

    /**
     * Method get_patrimonio
     * Sample of usage: $var->patrimonio->attribute;
     * @returns Patrimonio instance
     */
    public function get_patrimonio()
    {
    
        // loads the associated object
        if (empty($this->patrimonio))
            $this->patrimonio = new Patrimonio($this->id_patrimonio);
    
        // returns the associated object
        return $this->patrimonio;
    }

    
}

