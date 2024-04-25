<?php

class SetorSeekWindow extends TWindow
{
    protected $form;    
    protected $datagrid; 
    protected $pageNavigation;
    private static $formName  = 'SetorSeekWindow';
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
        $this->setActiveRecord('setor');          
        $this->setDefaultOrder('id_setor', 'asc');         
        $this->addFilterField('cd_setor', '=');          
        $this->addFilterField('nm_setor', 'ilike');  
        
        $this->form = new BootstrapFormBuilder('form_search_Setor');
        $this->form->setFieldSizes('100%');
        $this->form->setFormTitle('Buscar Setor');

        $cd_setor = new TEntry('cd_setor');
        $nm_setor = new TEntry('nm_setor');
        
        $row1 = $this->form->addFields( [new TLabel("Código:", '#FF0000', '14px', null, '100%'),$cd_setor],
                                        [new TLabel("Setor:", '#FF0000', '14px', null, '100%'),$nm_setor]);
        $row1->layout = ['col-sm-4',' col-sm-8'];
        
        $this->form->setData( TSession::getValue('ProductSeek_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        // expand button
        $this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // creates the datagrid column
        $coluna_codigo = new TDataGridColumn('cd_setor', 'Código', 'left', '20%');
        $coluna_nome   = new TDataGridColumn('nm_setor', 'Setor', 'left', '30%');
        $coluna_pessoa = new TDataGridColumn('nm_fichacadastral', 'Pessoa Responsavel', 'left', '50%');
        
        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_pessoa);
        
        // creates two datagrid actions
        $param_form = array('form_name'=>$param['form_name'],'field_name'=>$param['field_name']);
        $action = new TDataGridAction(array($this, 'onSelect'), $param_form);
        $action->setField('id_setor');
        $this->datagrid->addAction($action, 'Select', 'far:hand-pointer blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(['SetorSeekWindow', 'onReload']));
        
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
        
            $object = new Setor($key);

            $setor = new stdClass;
           
            if($param['field_name'] == 'cd_setororigem')
            {
                $setor->id_setororigem = $object->id_setor;
                $setor->cd_setororigem = $object->cd_setor;
                $setor->nm_setororigem = $object->nm_setor;
            }
            elseif($param['field_name'] == 'cd_setordestino')
            {
                $setor->id_setordestino = $object->id_setor;
                $setor->cd_setordestino = $object->cd_setor;
                $setor->nm_setordestino = $object->nm_setor;
            }
            else
            {
                $setor->id_setor = $object->id_setor;
                $setor->cd_setor = $object->cd_setor;
                $setor->nm_setor = $object->nm_setor;
            }
            
            TTransaction::close();
            
            TForm::sendData($param['form_name'], $setor);

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

            $repository = new TRepository('setor');
            $criteria = new TCriteria;

            $idSetor = TSession::getValue('id_setor');

            if($param['field_name'] == 'cd_setordestino')
            {
                if(isset($idSetor) && !empty($idSetor))
                {
                    $criteria->add(new TFilter('id_setor', '!=', $idSetor));
                }
            }

            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new stdClass;
                    $item->id_setor          = $object->id_setor;
                    $item->cd_setor          = $object->cd_setor;
                    $item->nm_setor          = $object->nm_setor;
                    $item->nm_fichacadastral = $object->fichacadastral->nm_fichacadastral;
                    
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
