<?php

class FichaCadastralSeekWindow extends TWindow
{
    protected $form;    
    protected $datagrid; 
    protected $pageNavigation;
    private static $formName  = 'FichaCadastralSeekWindow';
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
        $this->setActiveRecord('fichacadastral');          
        $this->setDefaultOrder('id_fichacadastral', 'asc');         
        $this->addFilterField('nu_cpfcnpj', '=');          
        $this->addFilterField('nm_fichacadastral', 'ilike');  
        
        $this->form = new BootstrapFormBuilder('form_search_FichaCadastral');
        $this->form->setFieldSizes('100%');
        $this->form->setFormTitle('Buscar Ficha Cadastral');

        $nu_cpfcnpj        = new TEntry('nu_cpfcnpj');
        $nm_fichacadastral = new TEntry('nm_fichacadastral');
        
        $row1 = $this->form->addFields( [new TLabel("CPF/CNPJ:", '#FF0000', '14px', null, '100%'),$nu_cpfcnpj],
                                        [new TLabel("Nome:", '#FF0000', '14px', null, '100%'),$nm_fichacadastral]);
        $row1->layout = ['col-sm-4',' col-sm-8'];
        
        $this->form->setData( TSession::getValue('ProductSeek_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        // expand button
        $this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // creates the datagrid column
        $coluna_cpfcnpj     = new TDataGridColumn('nu_cpfcnpj', 'CPF/CNPJ', 'left', '30%');
        $coluna_nome        = new TDataGridColumn('nm_fichacadastral', 'Nome', 'left', '30%');
        $coluna_telefone    = new TDataGridColumn('nu_telefone', 'Telefone', 'left', '20%');
        $coluna_email       = new TDataGridColumn('ds_email', 'Email', 'left', '20%');
        
        $this->datagrid->addColumn($coluna_cpfcnpj);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_telefone);
        $this->datagrid->addColumn($coluna_email);
        
        // creates two datagrid actions
        $param_form = array('form_name'=>$param['form_name']);
        $action = new TDataGridAction(array($this, 'onSelect'), $param_form);
        $action->setField('id_fichacadastral');
        $this->datagrid->addAction($action, 'Select', 'far:hand-pointer blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(['FichaCadastralSeekWindow', 'onReload']));
        
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
        
            $object = new FichaCadastral($key);

            $fichaCadastral = new stdClass;
            $fichaCadastral->id_fichacadastral = $object->id_fichacadastral;
            $fichaCadastral->nu_cpfcnpj        = $object->nu_cpfcnpj;
            $fichaCadastral->nm_fichacadastral = $object->nm_fichacadastral;
            $fichaCadastral->nu_telefone       = $object->nu_telefone;
            $fichaCadastral->ds_email          = $object->ds_email;

            TTransaction::close();
            
            TForm::sendData($param['form_name'], $fichaCadastral);

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

            $repository = new TRepository('fichacadastral');
            $criteria = new TCriteria;

            if($param['form_name'] == 'form_SetorForm')
            {
                $criteria->add(new TFilter('tp_fichacadastral', '=', '1'));
            }

            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new stdClass;
                    $item->id_fichacadastral = $object->id_fichacadastral;
                    $item->nu_cpfcnpj        = $object->nu_cpfcnpj;
                    $item->nm_fichacadastral = $object->nm_fichacadastral;
                    $item->nu_telefone       = $object->nu_telefone;
                    $item->ds_email          = $object->ds_email;
                    
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
