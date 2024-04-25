<?php

class PatrimonioSeekWindow extends TWindow
{
    protected $form;    
    protected $datagrid; 
    protected $pageNavigation;
    private static $formName  = 'PatrimonioSeekWindow';
    private static $data_base = 'sample';
    
    // trait onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\Base\AdiantiStandardListTrait;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param)
    {
        parent::__construct();
        
        $this->setDatabase('sample');                
        $this->setActiveRecord('patrimonio');          
        $this->setDefaultOrder('id_patrimonio', 'asc');         
        $this->addFilterField('nu_plaqueta', '=');       
        
        $this->form = new BootstrapFormBuilder('form_search_Patrimonio');
        $this->form->setFieldSizes('100%');
        $this->form->setFormTitle('Buscar Patrimonio');

        $nu_plaqueta = new TEntry('nu_plaqueta');
        $vl_bem      = new TEntry('vl_bem');
        $tp_situacao = new TEntry('tp_situacao');
        
        $row1 = $this->form->addFields( [new TLabel("N° Plaqueta:", '#FF0000', '14px', null, '100%'),$nu_plaqueta], 
                                        [new TLabel("Valor Bem:", '#FF0000', '14px', null, '100%'), $vl_bem],
                                        [new TLabel("Situação:", '#FF0000', '14px', null, '100%'), $tp_situacao]);
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $this->form->setData( TSession::getValue('ProductSeek_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        // expand button
        $this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // creates the datagrid column
        $coluna_plaqueta   = new TDataGridColumn('nu_plaqueta', 'N° Plaqueta', 'left', '10%');
        $coluna_observacao = new TDataGridColumn('ds_observacao', 'Observação', 'left', '30%');
        $coluna_situacao   = new TDataGridColumn('tp_situacao', 'Situação', 'left', '20%');
        $coluna_valor      = new TDataGridColumn('vl_bem', 'Valor Bem', 'left', '20%');
        $coluna_data       = new TDataGridColumn('dt_aquisicao', 'Data Aquisição', 'left', '20%');
        
        $this->datagrid->addColumn($coluna_plaqueta);
        $this->datagrid->addColumn($coluna_observacao);
        $this->datagrid->addColumn($coluna_situacao);
        $this->datagrid->addColumn($coluna_valor);
        $this->datagrid->addColumn($coluna_data);

        $coluna_situacao->setTransformer(function ($value) {

            if ($value == 1)
            {
                $value = 'Ativo';
                return $value;
            }
            else
            {
                $value = 'Baixado';
                return $value;
            }
        });

        $coluna_data->setTransformer(function ($value) {
            return date('d/m/Y', strtotime($value));
        });

        $coluna_valor->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });
        
        // creates two datagrid actions
        $param_form = array('form_name'=>$param['form_name']);
        $action = new TDataGridAction(array($this, 'onSelect'), $param_form);
        $action->setField('id_patrimonio');
        $this->datagrid->addAction($action, 'Select', 'far:hand-pointer blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(['PatrimonioSeekWindow', 'onReload']));
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        $panel->getBody()->style = 'overflow-x:auto';
        parent::add($container);
    }
    
   
    public function onSelect($param)
    {
        try
        {
            $key = $param['key'];

            TTransaction::open(self::$data_base);
        
            $object = new Patrimonio($key);

            $patrimonio = new stdClass;
            $patrimonio->id_patrimonio        = $object->id_patrimonio;
            $patrimonio->nu_plaqueta          = $object->nu_plaqueta;
            $patrimonio->ds_observacao        = $object->ds_observacao;
            $patrimonio->vl_itembaixa         = number_format($object->vl_bem, 2, ',','.');
            $patrimonio->vl_itemtransferencia = number_format($object->vl_bem, 2, ',','.');
            $patrimonio->tp_situacao          = $object->tp_situacao;
            $patrimonio->dt_aquisicao         = $object->dt_aquisicao;
            
            TTransaction::close();
            
            TForm::sendData($param['form_name'], $patrimonio);

            parent::closeWindow();
        }
        catch (Exception $e)
        {            
            TTransaction::rollback();
        }
    }

    public function onReload($param = null)
    {
        try 
        {
            TTransaction::open(self::$data_base);

            $repository = new TRepository('patrimonio');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('tp_situacao', '=', '1'));
            
            $idSetor = TSession::getValue('id_setor');
            
            if($param['form_name'] == 'form_TransferenciaPatrimonioForm')
            {
                $criteria->add(new TFilter('id_setor', '=', $idSetor));
            }

            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new stdClass;
                    $item->id_patrimonio = $object->id_patrimonio;
                    $item->nu_plaqueta   = $object->nu_plaqueta;
                    $item->ds_observacao = $object->ds_observacao;
                    $item->vl_bem        = $object->vl_bem;
                    $item->tp_situacao   = $object->tp_situacao;
                    $item->dt_aquisicao  = $object->dt_aquisicao;
                    
                    $this->datagrid->addItem($item);
                }
            }

            TTransaction::close();
            $this->loaded = true;
        } 
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
