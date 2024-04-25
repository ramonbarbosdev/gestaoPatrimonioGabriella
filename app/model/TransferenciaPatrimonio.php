<?php

class TransferenciaPatrimonio extends TRecord
{
    const TABLENAME  = 'transferencia_patrimonio';
    const PRIMARYKEY = 'id_transferenciapatrimonio';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $setororigem;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cd_transferenciapatrimonio');
        parent::addAttribute('dt_transferenciapatrimonio');
        parent::addAttribute('vl_total');
        parent::addAttribute('id_setororigem');
            
    }

    /**
     * Method set_setor
     * Sample of usage: $var->setor = $object;
     * @param $object Instance of Setor
     */
    public function set_setororigem(Setor $object)
    {
        $this->setororigem = $object;
        $this->id_setororigem = $object->id_setor;
    }

    /**
     * Method get_setororigem
     * Sample of usage: $var->setororigem->attribute;
     * @returns Setor instance
     */
    public function get_setororigem()
    {
    
        // loads the associated object
        if (empty($this->setororigem))
            $this->setororigem = new Setor($this->id_setororigem);
    
        // returns the associated object
        return $this->setororigem;
    }

    /**
     * Method getItemTransferencias
     */
    public function getItemTransferencias()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_transferenciapatrimonio', '=', $this->id_transferenciapatrimonio));
        return ItemTransferencia::getObjects( $criteria );
    }

    public function set_item_transferencia_transferenciapatrimonio_to_string($item_transferencia_transferenciapatrimonio_to_string)
    {
        if(is_array($item_transferencia_transferenciapatrimonio_to_string))
        {
            $values = TransferenciaPatrimonio::where('id_transferenciapatrimonio', 'in', $item_transferencia_transferenciapatrimonio_to_string)->getIndexedArray('id_transferenciapatrimonio', 'id_transferenciapatrimonio');
            $this->item_transferencia_transferenciapatrimonio_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_transferencia_transferenciapatrimonio_to_string = $item_transferencia_transferenciapatrimonio_to_string;
        }

        $this->vdata['item_transferencia_transferenciapatrimonio_to_string'] = $this->item_transferencia_transferenciapatrimonio_to_string;
    }

    public function get_item_transferencia_transferenciapatrimonio_to_string()
    {
        if(!empty($this->item_transferencia_transferenciapatrimonio_to_string))
        {
            return $this->item_transferencia_transferenciapatrimonio_to_string;
        }
    
        $values = ItemTransferencia::where('id_transferenciapatrimonio', '=', $this->id_transferenciapatrimonio)->getIndexedArray('id_transferenciapatrimonio','{transferenciapatrimonio->id_transferenciapatrimonio}');
        return implode(', ', $values);
    }

    public function set_item_transferencia_patrimonio_to_string($item_transferencia_patrimonio_to_string)
    {
        if(is_array($item_transferencia_patrimonio_to_string))
        {
            $values = Patrimonio::where('id_patrimonio', 'in', $item_transferencia_patrimonio_to_string)->getIndexedArray('id_patrimonio', 'id_patrimonio');
            $this->item_transferencia_patrimonio_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_transferencia_patrimonio_to_string = $item_transferencia_patrimonio_to_string;
        }

        $this->vdata['item_transferencia_patrimonio_to_string'] = $this->item_transferencia_patrimonio_to_string;
    }

    public function get_item_transferencia_patrimonio_to_string()
    {
        if(!empty($this->item_transferencia_patrimonio_to_string))
        {
            return $this->item_transferencia_patrimonio_to_string;
        }
    
        $values = ItemTransferencia::where('id_transferenciapatrimonio', '=', $this->id_transferenciapatrimonio)->getIndexedArray('id_patrimonio','{patrimonio->id_patrimonio}');
        return implode(', ', $values);
    }

    public function set_item_transferencia_setordestino_to_string($item_transferencia_setordestino_to_string)
    {
        if(is_array($item_transferencia_setordestino_to_string))
        {
            $values = Setor::where('id_setor', 'in', $item_transferencia_setordestino_to_string)->getIndexedArray('id_setor', 'id_setor');
            $this->item_transferencia_setordestino_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_transferencia_setordestino_to_string = $item_transferencia_setordestino_to_string;
        }

        $this->vdata['item_transferencia_setordestino_to_string'] = $this->item_transferencia_setordestino_to_string;
    }

    public function get_item_transferencia_setordestino_to_string()
    {
        if(!empty($this->item_transferencia_setordestino_to_string))
        {
            return $this->item_transferencia_setordestino_to_string;
        }
    
        $values = ItemTransferencia::where('id_transferenciapatrimonio', '=', $this->id_transferenciapatrimonio)->getIndexedArray('id_setordestino','{setordestino->id_setor}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        ItemTransferencia::where('id_transferenciapatrimonio', '=', $this->id_transferenciapatrimonio)
            ->delete();
    }

    
}

