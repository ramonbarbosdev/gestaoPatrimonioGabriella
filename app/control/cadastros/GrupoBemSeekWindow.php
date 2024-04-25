<?php

class GrupoBemSeekWindow extends TWindow
{
    protected $form;    
    protected $datagrid; 
    protected $pageNavigation;
    private static $formName  = 'GrupoBemSeekWindow';
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
        $this->setActiveRecord('grupobem');          
        $this->setDefaultOrder('id_grupobem', 'asc');         
        $this->addFilterField('cd_grupobem', '=');          
        $this->addFilterField('nm_grupobem', 'ilike');  
        
        $this->form = new BootstrapFormBuilder('form_search_GrupoBem');
        $this->form->setFieldSizes('100%');
        $this->form->setFormTitle('Buscar Grupo Bem');

        $cd_grupobem = new TEntry('cd_grupobem');
        $nm_grupobem = new TEntry('nm_grupobem');
        
        $row1 = $this->form->addFields( [new TLabel("Código:", '#FF0000', '14px', null, '100%'),$cd_grupobem],
                                        [new TLabel("Grupo Bem:", '#FF0000', '14px', null, '100%'),$nm_grupobem]);
        $row1->layout = ['col-sm-4',' col-sm-8'];
        
        $this->form->setData( TSession::getValue('ProductSeek_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        // expand button
        $this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // creates the datagrid column
        $coluna_codigo     = new TDataGridColumn('cd_grupobem', 'Código', 'left','20%');
        $coluna_nome       = new TDataGridColumn('nm_grupobem', 'Grupo Bem', 'left', '30%');
        $coluna_descricao  = new TDataGridColumn('ds_grupobem', 'Descrição', 'left', '50%');
        
        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_descricao);

        $coluna_descricao->setTransformer(function ($value, $object, $row, $cell, $previous_row) {

            return substr($value, 0, 50) . "...";
        });
        
        // creates two datagrid actions
        $param_form = array('form_name'=>$param['form_name']);
        $action = new TDataGridAction(array($this, 'onSelect'), $param_form);
        $action->setField('id_grupobem');
        $this->datagrid->addAction($action, 'Select', 'far:hand-pointer blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(['GrupoBemSeekWindow', 'onReload']));
        
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
        
            $object = new GrupoBem($key);

            $grupoBem = new stdClass;
            $grupoBem->id_grupobem = $object->id_grupobem;
            $grupoBem->cd_grupobem = $object->cd_grupobem;
            $grupoBem->nm_grupobem = $object->nm_grupobem;

            TTransaction::close();
            
            TForm::sendData($param['form_name'], $grupoBem);

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

            $repository = new TRepository('grupobem');
            $criteria = new TCriteria;
            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new stdClass;
                    $item->id_grupobem = $object->id_grupobem;
                    $item->cd_grupobem = $object->cd_grupobem;
                    $item->nm_grupobem = $object->nm_grupobem;
                    $item->ds_grupobem = $object->ds_grupobem;
                    
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
