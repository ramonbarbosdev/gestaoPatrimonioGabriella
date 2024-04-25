<?php

class NotaFiscal extends TRecord
{
    const TABLENAME  = 'nota_fiscal';
    const PRIMARYKEY = 'id_notafiscal';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $fichacadastral;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nu_chavenf');
        parent::addAttribute('sg_uf');
        parent::addAttribute('dt_emissao');
        parent::addAttribute('nu_serie');
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

    /**
     * Method getPatrimonios
     */
    public function getPatrimonios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_notafiscal', '=', $this->id_notafiscal));
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
    
        $values = Patrimonio::where('id_notafiscal', '=', $this->id_notafiscal)->getIndexedArray('id_setor','{setor->id_setor}');
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
    
        $values = Patrimonio::where('id_notafiscal', '=', $this->id_notafiscal)->getIndexedArray('id_itembem','{itembem->id_itembem}');
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
    
        $values = Patrimonio::where('id_notafiscal', '=', $this->id_notafiscal)->getIndexedArray('id_notafiscal','{notafiscal->id_notafiscal}');
        return implode(', ', $values);
    }
    
}

