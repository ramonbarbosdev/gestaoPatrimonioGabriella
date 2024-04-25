<?php

class ItemBem extends TRecord
{
    const TABLENAME  = 'item_bem';
    const PRIMARYKEY = 'id_itembem';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $grupobem;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cd_itembem');
        parent::addAttribute('nm_itembem');
        parent::addAttribute('nm_unidademedida');
        parent::addAttribute('id_grupobem');
            
    }

    /**
     * Method set_grupo_bem
     * Sample of usage: $var->grupo_bem = $object;
     * @param $object Instance of GrupoBem
     */
    public function set_grupobem(GrupoBem $object)
    {
        $this->grupobem = $object;
        $this->id_grupobem = $object->id_grupobem;
    }

    /**
     * Method get_grupobem
     * Sample of usage: $var->grupobem->attribute;
     * @returns GrupoBem instance
     */
    public function get_grupobem()
    {
    
        // loads the associated object
        if (empty($this->grupobem))
            $this->grupobem = new GrupoBem($this->id_grupobem);
    
        // returns the associated object
        return $this->grupobem;
    }

    /**
     * Method getPatrimonios
     */
    public function getPatrimonios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_itembem', '=', $this->id_itembem));
        return Patrimonio::getObjects( $criteria );
    }

    public function set_patrimonio_setor_to_string($patrimonio_setor_to_string)
    {
        if(is_array($patrimonio_setor_to_string))
        {
            $values = Setor::where('id_setor', 'in', $patrimonio_setor_to_string)->getIndexedArray('id_setor', 'id_setor');
            $this->patrimonio_setor_to_string = implode(', ', $values);
        }
        else
        {
            $this->patrimonio_setor_to_string = $patrimonio_setor_to_string;
        }

        $this->vdata['patrimonio_setor_to_string'] = $this->patrimonio_setor_to_string;
    }

    public function get_patrimonio_setor_to_string()
    {
        if(!empty($this->patrimonio_setor_to_string))
        {
            return $this->patrimonio_setor_to_string;
        }
    
        $values = Patrimonio::where('id_itembem', '=', $this->id_itembem)->getIndexedArray('id_setor','{setor->id_setor}');
        return implode(', ', $values);
    }

    public function set_patrimonio_itembem_to_string($patrimonio_itembem_to_string)
    {
        if(is_array($patrimonio_itembem_to_string))
        {
            $values = ItemBem::where('id_itembem', 'in', $patrimonio_itembem_to_string)->getIndexedArray('id_itembem', 'id_itembem');
            $this->patrimonio_itembem_to_string = implode(', ', $values);
        }
        else
        {
            $this->patrimonio_itembem_to_string = $patrimonio_itembem_to_string;
        }

        $this->vdata['patrimonio_itembem_to_string'] = $this->patrimonio_itembem_to_string;
    }

    public function get_patrimonio_itembem_to_string()
    {
        if(!empty($this->patrimonio_itembem_to_string))
        {
            return $this->patrimonio_itembem_to_string;
        }
    
        $values = Patrimonio::where('id_itembem', '=', $this->id_itembem)->getIndexedArray('id_itembem','{itembem->id_itembem}');
        return implode(', ', $values);
    }

    public function set_patrimonio_notafiscal_to_string($patrimonio_notafiscal_to_string)
    {
        if(is_array($patrimonio_notafiscal_to_string))
        {
            $values = NotaFiscal::where('id_notafiscal', 'in', $patrimonio_notafiscal_to_string)->getIndexedArray('id_notafiscal', 'id_notafiscal');
            $this->patrimonio_notafiscal_to_string = implode(', ', $values);
        }
        else
        {
            $this->patrimonio_notafiscal_to_string = $patrimonio_notafiscal_to_string;
        }

        $this->vdata['patrimonio_notafiscal_to_string'] = $this->patrimonio_notafiscal_to_string;
    }

    public function get_patrimonio_notafiscal_to_string()
    {
        if(!empty($this->patrimonio_notafiscal_to_string))
        {
            return $this->patrimonio_notafiscal_to_string;
        }
    
        $values = Patrimonio::where('id_itembem', '=', $this->id_itembem)->getIndexedArray('id_notafiscal','{notafiscal->id_notafiscal}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        
    }

    
}

