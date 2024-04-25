<?php

class BaixaPatrimonio extends TRecord
{
    const TABLENAME  = 'baixa_patrimonio';
    const PRIMARYKEY = 'id_baixapatrimonio';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cd_baixapatrimonio');
        parent::addAttribute('dt_baixapatrimonio');
        parent::addAttribute('vl_total');
        parent::addAttribute('ds_observacao');
            
    }

    /**
     * Method getItemBaixas
     */
    public function getItemBaixas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_baixapatrimonio', '=', $this->id_baixapatrimonio));
        return ItemBaixa::getObjects( $criteria );
    }

    public function set_item_baixa_baixapatrimonio_to_string($item_baixa_baixapatrimonio_to_string)
    {
        if(is_array($item_baixa_baixapatrimonio_to_string))
        {
            $values = BaixaPatrimonio::where('id_baixapatrimonio', 'in', $item_baixa_baixapatrimonio_to_string)->getIndexedArray('id_baixapatrimonio', 'id_baixapatrimonio');
            $this->item_baixa_baixapatrimonio_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_baixa_baixapatrimonio_to_string = $item_baixa_baixapatrimonio_to_string;
        }

        $this->vdata['item_baixa_baixapatrimonio_to_string'] = $this->item_baixa_baixapatrimonio_to_string;
    }

    public function get_item_baixa_baixapatrimonio_to_string()
    {
        if(!empty($this->item_baixa_baixapatrimonio_to_string))
        {
            return $this->item_baixa_baixapatrimonio_to_string;
        }
    
        $values = ItemBaixa::where('id_baixapatrimonio', '=', $this->id_baixapatrimonio)->getIndexedArray('id_baixapatrimonio','{baixapatrimonio->id_baixapatrimonio}');
        return implode(', ', $values);
    }

    public function set_item_baixa_patrimonio_to_string($item_baixa_patrimonio_to_string)
    {
        if(is_array($item_baixa_patrimonio_to_string))
        {
            $values = Patrimonio::where('id_patrimonio', 'in', $item_baixa_patrimonio_to_string)->getIndexedArray('id_patrimonio', 'id_patrimonio');
            $this->item_baixa_patrimonio_to_string = implode(', ', $values);
        }
        else
        {
            $this->item_baixa_patrimonio_to_string = $item_baixa_patrimonio_to_string;
        }

        $this->vdata['item_baixa_patrimonio_to_string'] = $this->item_baixa_patrimonio_to_string;
    }

    public function get_item_baixa_patrimonio_to_string()
    {
        if(!empty($this->item_baixa_patrimonio_to_string))
        {
            return $this->item_baixa_patrimonio_to_string;
        }
    
        $values = ItemBaixa::where('id_baixapatrimonio', '=', $this->id_baixapatrimonio)->getIndexedArray('id_patrimonio','{patrimonio->id_patrimonio}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        ItemBaixa::where('id_baixapatrimonio', '=', $this->id_baixapatrimonio)
            ->delete();
    }

    
}

