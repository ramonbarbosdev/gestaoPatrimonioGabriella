<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class FichaCadastralHeaderList extends TPage 
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private static $data_base     = 'sample';
    private static $active_record = 'FichaCadastral';
    private static $formName      = 'FichaCadastralHeaderList';

    // trait onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Listagem de Fichas Cadastrais');

        $this->setDatabase(self::$data_base);
        $this->setActiveRecord(self::$active_record);
        $this->setDefaultOrder('id_fichacadastral', 'asc');
        $this->setLimit(10);

        $this->addFilterField('nu_cpfcnpj', '=', 'nu_cpfcnpj');
        $this->addFilterField('nm_fichacadastral', 'ilike', 'nm_fichacadastral');

        $nu_cpfcnpj        = new TEntry('nu_cpfcnpj');
        $nm_fichacadastral = new TEntry('nm_fichacadastral');

        $row1 = $this->form->addFields( [new TLabel('CPF/CNPJ:', '#333', '14px', null, '100%'), $nu_cpfcnpj],
                                        [new TLabel('Nome Ficha Cadastral:', '#333', '14px', null, '100%'), $nm_fichacadastral]);
        $row1->layout = ['col-sm-4', 'col-sm-8'];
        
        $nu_cpfcnpj->setSize('100%');
        $nm_fichacadastral->setSize('100%');

        $this->form->addAction(  'Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo', new TAction(['FichaCadastralForm', 'onShow']), 'fa:plus-circle green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'Width: 100%';

        $coluna_cpfcnpj     = new TDataGridColumn('nu_cpfcnpj', 'CPF/CNPJ', 'left', '30%');
        $coluna_nome        = new TDataGridColumn('nm_fichacadastral', 'Nome', 'left', '30%');
        $coluna_telefone    = new TDataGridColumn('nu_telefone', 'Telefone', 'left', '20%');
        $coluna_email       = new TDataGridColumn('ds_email', 'Email', 'left', '20%');

        $this->datagrid->addColumn($coluna_cpfcnpj);
        $this->datagrid->addColumn($coluna_nome);
        $this->datagrid->addColumn($coluna_telefone);
        $this->datagrid->addColumn($coluna_email);

        $actionOnEditar  = new TDataGridAction(['FichaCadastralForm', 'onEdit'], ['key' => '{id_fichacadastral}']);
        $actionOnDelete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_fichacadastral}']);

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

                $object = new FichaCadastral($key, FALSE);

                //Exclusão com suas validações
                FichaCadastralService::excluir($object, $conn);

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

        AdiantiCoreApplication::loadPage('FichaCadastralHeaderList', 'onShow', $loadPageParam);
    }
}