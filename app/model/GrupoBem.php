<?php

class GrupoBem extends TRecord
{
    const TABLENAME  = 'grupo_bem';
    const PRIMARYKEY = 'id_grupobem';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cd_grupobem');
        parent::addAttribute('nm_grupobem');
        parent::addAttribute('ds_grupobem');
            
    }

    /**
     * Method getItemBems
     */
    public function getItemBems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_grupobem', '=', $this->id_grupobem));
        return ItemBem::getObjects( $criteria );
    }

    public function set_item_bem_grupobem_to_string($item_bem_grupobem_to_string)
    {
        if(is_array($item_bem_grupobem_to_string))
        {
            $values = GrupoBem::where('id_grupobem', 'in', $item_bem_grupobem_to_string)->getIndexedArray('id_grupobem', 'id_grupobem');
            $this->item_bem_grupobem_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_bem_grupobem_to_string = $item_bem_grupobem_to_string;
        }

        $this->vdata['item_bem_grupobem_to_string'] = $this->item_bem_grupobem_to_string;
    }

    public function get_item_bem_grupobem_to_string()
    {
        if(!empty($this->item_bem_grupobem_to_string))
        {
            return $this->item_bem_grupobem_to_string;
        }
    
        $values = ItemBem::where('id_grupobem', '=', $this->id_grupobem)->getIndexedArray('id_grupobem','{grupobem->id_grupobem}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
    }
}

