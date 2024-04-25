<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ConsultaTotalizadoraGrupo extends TPage 
{
    private $datagrid;

    use Adianti\base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
      
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $nm_grupobem = new TDataGridColumn('nm_grupobem', 'Grupo Bem', 'left', '50%');
        $vl_total    = new TDataGridColumn('vl_total', 'Valor Total', 'left', '50%');
    
        $this->datagrid->addColumn($nm_grupobem);
        $this->datagrid->addColumn($vl_total);

        $vl_total->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Relação totalizadora de Grupos de Bens');
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
    
            $consulta = ConsultaTotalizadoraGrupoService::consultaTotalGrupo($conn);
        
            // Certifique-se de que $consulta é um array de objetos antes de tentar iterar
            if ($consulta && is_array($consulta)) {
                foreach ($consulta as $row) {
                    $item = new StdClass;
                    
                    $item->nm_grupobem = $row->nm_grupobem;
                    $item->vl_total    = $row->vl_total;
            
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