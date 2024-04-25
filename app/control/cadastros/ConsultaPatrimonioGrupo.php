<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ConsultaPatrimonioGrupo extends TPage 
{
    private $datagrid;

    use Adianti\base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
      
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $this->datagrid->setGroupColumn('nm_grupobem', '<b>+ Grupo:</b> <i>{nm_grupobem}</i>');

        $nm_itembem    = new TDataGridColumn('nm_itembem',  'Nome do Bem', 'left', '20%');
        $nm_setor      = new TDataGridColumn('nm_setor', 'Setor',  'left', '20%');
        $vl_bem        = new TDataGridColumn('vl_bem', 'Valor do Bem (R$)', 'left', '20%');
        $dt_aquisicao  = new TDataGridColumn('dt_aquisicao', 'Data Aquisição', 'left',  '20%');
    
        $this->datagrid->addColumn($nm_itembem);
        $this->datagrid->addColumn($nm_setor);
        $this->datagrid->addColumn($vl_bem);
        $this->datagrid->addColumn($dt_aquisicao);

        $dt_aquisicao->setTransformer(function ($value) {
            return date('d/m/Y', strtotime($value));
        });

        $vl_bem->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Consulta Patrimonio');
        $panel->add($this->datagrid);

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);


        parent::add($vbox);
    }

    public function onReload($param)
    {
        try {
            $conn = TTransaction::open('sample');
    
            $consulta = ConsultaPatrimonioGrupoService::consultaPatrimonioGrupo($conn);
        
            // Certifique-se de que $consulta é um array de objetos antes de tentar iterar
            if ($consulta && is_array($consulta)) {
                foreach ($consulta as $row) {
                    $item = new StdClass;
                    
                    $item->nm_itembem   = $row->nm_itembem;
                    $item->nm_grupobem  = $row->nm_grupobem;
                    $item->nm_setor     = $row->nm_setor;
                    $item->vl_bem       = $row->vl_bem;
                    $item->dt_aquisicao = $row->dt_aquisicao;
            
                    $this->datagrid->addItem($item);
                }
            }
            
            TTransaction::close();
            
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage(), $this->afterSaveAction);
            TTransaction::rollback();
        }
    }
    

  
}