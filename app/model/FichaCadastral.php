<?php

class FichaCadastral extends TRecord
{
    const TABLENAME  = 'ficha_cadastral';
    const PRIMARYKEY = 'id_fichacadastral';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tp_fichacadastral');
        parent::addAttribute('nm_fichacadastral');
        parent::addAttribute('nu_cpfcnpj');
        parent::addAttribute('ds_email');
        parent::addAttribute('nu_telefone');
        parent::addAttribute('nm_fantasia');
            
    }

    /**
     * Method getNotaFiscals
     */
    public function getNotaFiscals()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_fichacadastral', '=', $this->id_fichacadastral));
        return NotaFiscal::getObjects( $criteria );
    }
    /**
     * Method getSetors
     */
    public function getSetors()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_fichacadastral', '=', $this->id_fichacadastral));
        return Setor::getObjects( $criteria );
    }
    /**
     * Method getEnderecos
     */
    public function getEnderecos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id_fichacadastral', '=', $this->id_fichacadastral));
        return Endereco::getObjects( $criteria );
    }

    public function set_nota_fiscal_fichacadastral_to_string($nota_fiscal_fichacadastral_to_string)
    {
        if(is_array($nota_fiscal_fichacadastral_to_string))
        {
            $values = FichaCadastral::where('id_fichacadastral', 'in', $nota_fiscal_fichacadastral_to_string)->getIndexedArray('id_fichacadastral', 'id_fichacadastral');
            $this->nota_fiscal_fichacadastral_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_fiscal_fichacadastral_to_string = $nota_fiscal_fichacadastral_to_string;
        }

        $this->vdata['nota_fiscal_fichacadastral_to_string'] = $this->nota_fiscal_fichacadastral_to_string;
    }

    public function get_nota_fiscal_fichacadastral_to_string()
    {
        if(!empty($this->nota_fiscal_fichacadastral_to_string))
        {
            return $this->nota_fiscal_fichacadastral_to_string;
        }
    
        $values = NotaFiscal::where('id_fichacadastral', '=', $this->id_fichacadastral)->getIndexedArray('id_fichacadastral','{fichacadastral->id_fichacadastral}');
        return implode(', ', $values);
    }

    public function set_setor_fichacadastral_to_string($setor_fichacadastral_to_string)
    {
        if(is_array($setor_fichacadastral_to_string))
        {
            $values = FichaCadastral::where('id_fichacadastral', 'in', $setor_fichacadastral_to_string)->getIndexedArray('id_fichacadastral', 'id_fichacadastral');
            $this->setor_fichacadastral_to_string = implode(', ', $values);
        }
        else
        {
            $this->setor_fichacadastral_to_string = $setor_fichacadastral_to_string;
        }

        $this->vdata['setor_fichacadastral_to_string'] = $this->setor_fichacadastral_to_string;
    }

    public function get_setor_fichacadastral_to_string()
    {
        if(!empty($this->setor_fichacadastral_to_string))
        {
            return $this->setor_fichacadastral_to_string;
        }
    
        $values = Setor::where('id_fichacadastral', '=', $this->id_fichacadastral)->getIndexedArray('id_fichacadastral','{fichacadastral->id_fichacadastral}');
        return implode(', ', $values);
    }

    public function set_endereco_fichacadastral_to_string($endereco_fichacadastral_to_string)
    {
        if(is_array($endereco_fichacadastral_to_string))
        {
            $values = FichaCadastral::where('id_fichacadastral', 'in', $endereco_fichacadastral_to_string)->getIndexedArray('id_fichacadastral', 'id_fichacadastral');
            $this->endereco_fichacadastral_to_string = implode(', ', $values);
        }
        else
        {
            $this->endereco_fichacadastral_to_string = $endereco_fichacadastral_to_string;
        }

        $this->vdata['endereco_fichacadastral_to_string'] = $this->endereco_fichacadastral_to_string;
    }

    public function get_endereco_fichacadastral_to_string()
    {
        if(!empty($this->endereco_fichacadastral_to_string))
        {
            return $this->endereco_fichacadastral_to_string;
        }
    
        $values = Endereco::where('id_fichacadastral', '=', $this->id_fichacadastral)->getIndexedArray('id_fichacadastral','{fichacadastral->id_fichacadastral}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
        Endereco::where('id_fichacadastral', '=', $this->id_fichacadastral)
            ->delete();
    }
}

