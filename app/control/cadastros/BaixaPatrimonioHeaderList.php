<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class BaixaPatrimonioHeaderList extends TPage 
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private static $data_base     = 'sample';
    private static $active_record = 'BaixaPatrimonio';
    private static $formName      = 'BaixaPatrimonioHeaderList';

    // trait onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de Baixas Patrimonios');

        $this->setDatabase(self::$data_base);
        $this->setActiveRecord(self::$active_record);
        $this->setDefaultOrder('id_baixapatrimonio', 'asc');
        $this->setLimit(10);

        $this->addFilterField('cd_baixapatrimonio', '=', 'cd_baixapatrimonio');

        $cd_baixapatrimonio = new TEntry('cd_baixapatrimonio');

        $this->form->addFields( [new TLabel('Código:', '#333', '14px', null, '100%')], [$cd_baixapatrimonio]);

        $this->form->addAction(  'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo', new TAction(['BaixaPatrimonioForm', 'onShow']), 'fa:plus-circle green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'Width: 100%';
        
        $coluna_codigo     = new TDataGridColumn('cd_baixapatrimonio', 'Código', 'left', '20%');
        $coluna_observacao = new TDataGridColumn('ds_observacao', 'Observação', 'left', '40%');
        $coluna_valor      = new TDataGridColumn('vl_total', 'Valor Total', 'left', '20%');
        $coluna_data       = new TDataGridColumn('dt_baixapatrimonio', 'Data Aquisição', 'left', '20%');

        $this->datagrid->addColumn($coluna_codigo);
        $this->datagrid->addColumn($coluna_observacao);
        $this->datagrid->addColumn($coluna_valor);
        $this->datagrid->addColumn($coluna_data);

        $coluna_observacao->setTransformer(function ($value, $object, $row, $cell, $previous_row) {

            return substr($value, 0, 50) . "...";
        });

        $coluna_data->setTransformer(function ($value) {
            return date('d/m/Y', strtotime($value));
        });

        $coluna_valor->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        $actionOnEditar  = new TDataGridAction(['BaixaPatrimonioForm', 'onEdit'], ['key' => '{id_baixapatrimonio}']);
        $actionOnDelete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_baixapatrimonio}']);

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

                $conn = TTransaction::open(self::$data_base);

                $object = new BaixaPatrimonio($key, FALSE);
                
                //Exclusão com suas validações
                BaixaPatrimonioService::excluir($object, $conn);

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

        AdiantiCoreApplication::loadPage('BaixaPatrimonioHeaderList', 'onShow', $loadPageParam);
    }
}