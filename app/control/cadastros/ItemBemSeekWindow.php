<?php

class ItemBemSeekWindow extends TWindow
{
    protected $form;    
    protected $datagrid; 
    protected $pageNavigation;
    private static $formName  = 'ItemBemSeekWindow';
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
        $this->setActiveRecord('itembem');          
        $this->setDefaultOrder('id_itembem', 'asc');         
        $this->addFilterField('cd_itembem', '=');          
        $this->addFilterField('nm_itembem', 'ilike');  
        
        $this->form = new BootstrapFormBuilder('form_search_ItemBem');
        $this->form->setFieldSizes('100%');
        $this->form->setFormTitle('Buscar Item/Bem');

        $cd_itembem = new TEntry('cd_itembem');
        $nm_itembem = new TEntry('nm_itembem');
        
        $row1 = $this->form->addFields( [new TLabel("Código:", '#FF0000', '14px', null, '100%'),$cd_itembem],
                                        [new TLabel("Item/Bem:", '#FF0000', '14px', null, '100%'),$nm_itembem]);
        $row1->layout = ['col-sm-4',' col-sm-8'];
        
        $this->form->setData( TSession::getValue('ProductSeek_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        // expand button
        $this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // creates the datagrid column
        $coluna_codigo  = new TDataGridColumn('cd_itembem', 'Código', 'left', '20%');
        $coluna_nome    = new TDataGridColumn('nm_itembem', 'Item/Bem', 'left', '30%');
        $coluna_unidade = new TDataGridColumn('nm_unidademedida', 'Unidade Medida', 'left', '20%');
        $coluna_grupo   = new TDataGridColumn('nm_grupobem', 'Grupo Bem', 'left', '30%');
        
        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_unidade);
        $this->datagrid->addColumn($coluna_grupo);
        
        // creates two datagrid actions
        $param_form = array('form_name'=>$param['form_name']);
        $action = new TDataGridAction(array($this, 'onSelect'), $param_form);
        $action->setField('id_itembem');
        $this->datagrid->addAction($action, 'Select', 'far:hand-pointer blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(['ItemBemSeekWindow', 'onReload']));
        
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
        
            $object = new ItemBem($key);

            $itemBem = new stdClass;
            $itemBem->id_itembem       = $object->id_itembem;
            $itemBem->cd_itembem       = $object->cd_itembem;
            $itemBem->nm_itembem       = $object->nm_itembem;
            $itemBem->nm_unidademedida = $object->nm_unidademedida;
            $itemBem->nm_grupobem      = $object->grupobem->nm_grupobem;

            TTransaction::close();
            
            TForm::sendData($param['form_name'], $itemBem);

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

            $repository = new TRepository('itembem');
            $criteria = new TCriteria;
            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new stdClass;
                    $item->id_itembem       = $object->id_itembem;
                    $item->cd_itembem       = $object->cd_itembem;
                    $item->nm_itembem       = $object->nm_itembem;
                    $item->nm_unidademedida = $object->nm_unidademedida;
                    $item->nm_grupobem      = $object->grupobem->nm_grupobem;
                    
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
