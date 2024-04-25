<?php

class Patrimonio extends TRecord
{
    const TABLENAME  = 'patrimonio';
    const PRIMARYKEY = 'id_patrimonio';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $setor;
    private $itembem;
    private $notafiscal;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nu_plaqueta');
        parent::addAttribute('dt_aquisicao');
        parent::addAttribute('tp_entrada');
        parent::addAttribute('vl_bem');
        parent::addAttribute('tp_situacao');
        parent::addAttribute('ds_observacao');
        parent::addAttribute('id_setor');
        parent::addAttribute('id_itembem');
        parent::addAttribute('id_notafiscal');
            
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
    /**
     * Method set_item_bem
     * Sample of usage: $var->item_bem = $object;
     * @param $object Instance of ItemBem
     */
    public function set_itembem(ItemBem $object)
    {
        $this->itembem = $object;
        $this->id_itembem = $object->id_itembem;
    }

    /**
     * Method get_itembem
     * Sample of usage: $var->itembem->attribute;
     * @returns ItemBem instance
     */
    public function get_itembem()
    {
    
        // loads the associated object
        if (empty($this->itembem))
            $this->itembem = new ItemBem($this->id_itembem);
    
        // returns the associated object
        return $this->itembem;
    }
    /**
     * Method set_nota_fiscal
     * Sample of usage: $var->nota_fiscal = $object;
     * @param $object Instance of NotaFiscal
     */
    public function set_notafiscal(NotaFiscal $object)
    {
        $this->notafiscal = $object;
        $this->id_notafiscal = $object->id_notafiscal;
    }

    /**
     * Method get_notafiscal
     * Sample of usage: $var->notafiscal->attribute;
     * @returns NotaFiscal instance
     */
    public function get_notafiscal()
    {
    
        // loads the associated object
        if (empty($this->notafiscal))
            $this->notafiscal = new NotaFiscal($this->id_notafiscal);
    
        // returns the associated object
        return $this->notafiscal;
    }

    public function onBeforeDelete()
    {
        NotaFiscal::where('id_notafiscal', '=', $this->id_notafiscal)
            ->delete();
    }
}

