<?php

class ItemTransferencia extends TRecord
{
    const TABLENAME  = 'item_transferencia';
    const PRIMARYKEY = 'id_itemtransferencia';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $transferenciapatrimonio;
    private $patrimonio;
    private $setordestino;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('vl_itemtransferencia');
        parent::addAttribute('id_transferenciapatrimonio');
        parent::addAttribute('id_patrimonio');
        parent::addAttribute('id_setordestino');
            
    }

    /**
     * Method set_transferencia_patrimonio
     * Sample of usage: $var->transferencia_patrimonio = $object;
     * @param $object Instance of TransferenciaPatrimonio
     */
    public function set_transferenciapatrimonio(TransferenciaPatrimonio $object)
    {
        $this->transferenciapatrimonio = $object;
        $this->id_transferenciapatrimonio = $object->id_transferenciapatrimonio;
    }

    /**
     * Method get_transferenciapatrimonio
     * Sample of usage: $var->transferenciapatrimonio->attribute;
     * @returns TransferenciaPatrimonio instance
     */
    public function get_transferenciapatrimonio()
    {
    
        // loads the associated object
        if (empty($this->transferenciapatrimonio))
            $this->transferenciapatrimonio = new TransferenciaPatrimonio($this->id_transferenciapatrimonio);
    
        // returns the associated object
        return $this->transferenciapatrimonio;
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
    /**
     * Method set_setor
     * Sample of usage: $var->setor = $object;
     * @param $object Instance of Setor
     */
    public function set_setordestino(Setor $object)
    {
        $this->setordestino = $object;
        $this->id_setordestino = $object->id_setor;
    }

    /**
     * Method get_setordestino
     * Sample of usage: $var->setordestino->attribute;
     * @returns Setor instance
     */
    public function get_setordestino()
    {
    
        // loads the associated object
        if (empty($this->setordestino))
            $this->setordestino = new Setor($this->id_setordestino);
    
        // returns the associated object
        return $this->setordestino;
    }

    
}

