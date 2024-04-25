<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ItemBemHeaderList extends TPage 
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private static $data_base     = 'sample';
    private static $active_record = 'ItemBem';
    private static $formName      = 'ItemBemHeaderList';

    // trait onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de Item/Bem');

        $this->setDatabase(self::$data_base);
        $this->setActiveRecord(self::$active_record);
        $this->setDefaultOrder('id_itembem', 'asc');
        $this->setLimit(10);

        $this->addFilterField('nm_itembem', 'ilike', 'nm_itembem');

        $nm_itembem = new TEntry('nm_itembem');

        $this->form->addFields( [new TLabel('Item/Bem:', '#333', '14px', null, '100%')], [$nm_itembem]);

        $this->form->addAction(  'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo', new TAction(['ItemBemForm', 'onShow']), 'fa:plus-circle green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'Width: 100%';
        
        $coluna_codigo     = new TDataGridColumn('cd_itembem', 'Código', 'left', '20%');
        $coluna_nome       = new TDataGridColumn('nm_itembem', 'Item/Bem', 'left', '30%');
        $coluna_seek       = new TDataGridColumn('grupobem->nm_grupobem', 'Grupo Bem', 'left', '30%');
        $coluna_unidade    = new TDataGridColumn('nm_unidademedida', 'Unidade Medida', 'left', '20%');

        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_seek);
        $this->datagrid->addColumn($coluna_unidade);

        $actionOnEditar = new TDataGridAction(['ItemBemForm', 'onEdit'], ['key' => '{id_itembem}']);
        $actionOnDelete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_itembem}']);

        $this->datagrid->addAction($actionOnEditar, 'Editar', 'far:edit blue');
        $this->datagrid->addAction($actionOnDelete, 'Excluir', 'far:trash-alt red');
        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $painel = new TPanelGroup();
        $painel->add($this->datagrid);
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add($painel);
        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }

    public function onDelete($param = null)
    {
        $loadPageParam = [];

        if (isset($param['delete']) && $param['delete'] == 1) 
        {
            try 
            {
                $key = $param['key'];

                $conn = TTransaction::open(self::$data_base);

                $object = new ItemBem($key, FALSE);

                //Exclusão com suas validações
                ItemBemService::excluir($object, $conn);

                TTransaction::close();

                TToast::show('success', AdiantiCoreTranslator::translate('Record deleted'), 'topRight', 'far:check-circle');

            } 
            catch (Exception $erro)
            {
                new TMessage('error', $erro->getMessage());
                TTransaction::rollback();
            }
        } 
        else 
        {
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); 
            $action->setParameter('delete', 1);
            
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action); 
        }

        AdiantiCoreApplication::loadPage('ItemBemHeaderList', 'onShow', $loadPageParam);
    }
}