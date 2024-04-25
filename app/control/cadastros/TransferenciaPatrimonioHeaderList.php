<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class TransferenciaPatrimonioHeaderList extends TPage 
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private static $data_base     = 'sample';
    private static $active_record = 'TransferenciaPatrimonio';
    private static $formName      = 'TransferenciaPatrimonioHeaderList';

    // trait onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de Transferencias Patrimonios');

        $this->setDatabase(self::$data_base);
        $this->setActiveRecord(self::$active_record);
        $this->setDefaultOrder('id_transferenciapatrimonio', 'asc');
        $this->setLimit(10);

        $this->addFilterField('cd_transferenciapatrimonio', '=', 'cd_transferenciapatrimonio');

        $cd_transferenciapatrimonio = new TEntry('cd_transferenciapatrimonio');

        $this->form->addFields( [new TLabel('Código:', '#333', '14px', null, '100%')], [$cd_transferenciapatrimonio]);

        $this->form->addAction(  'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo', new TAction(['TransferenciaPatrimonioForm', 'onShow']), 'fa:plus-circle green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'Width: 100%';
        
        $coluna_codigo     = new TDataGridColumn('cd_transferenciapatrimonio', 'Código', 'left', '30%');
        $coluna_data       = new TDataGridColumn('dt_transferenciapatrimonio', 'Data Aquisição', 'left', '50%');
        $coluna_valor      = new TDataGridColumn('vl_total', 'Valor Total', 'left', '20%');

        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_data);
        $this->datagrid->addColumn($coluna_valor);

        $coluna_data->setTransformer(function ($value) {
            return date('d/m/Y', strtotime($value));
        });

        $coluna_valor->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        $actionOnEditar  = new TDataGridAction(['TransferenciaPatrimonioForm', 'onEdit'], ['key' => '{id_transferenciapatrimonio}']);
        $actionOnDelete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_transferenciapatrimonio}']);

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
            try {

                $key = $param['key'];

                TTransaction::open(self::$data_base);

                $object = new TransferenciaPatrimonio($key, FALSE);
                $object->delete($key);

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

        AdiantiCoreApplication::loadPage('TransferenciaPatrimonioHeaderList', 'onShow', $loadPageParam);
    }
}