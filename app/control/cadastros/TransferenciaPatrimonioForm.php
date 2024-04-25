<?php

require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class TransferenciaPatrimonioForm extends TPage
{
    protected $form; 
    private static $database       = 'sample';
    private static $activeRecord   = 'TransferenciaPatrimonio';
    private static $formName       = 'form_TransferenciaPatrimonioForm'; 
    private static $primaryKey     = 'id_transferenciapatrimonio';
    
    function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Transferência Patrimônio');
        $this->form->setClientValidation(true);

        //Transferencia Patrimonio
        $cd_transferenciapatrimonio = new TEntry('cd_transferenciapatrimonio');
        $dt_transferenciapatrimonio = new TDate('dt_transferenciapatrimonio');
        $vl_total                   = new TNumeric('vl_total', 2, ',', '.', true);
        $id_setororigem             = new THidden('id_setororigem');
        $cd_setororigem             = new TSeekButton('cd_setororigem');
        $nm_setororigem             = new TEntry('nm_setororigem');
        
        //Item Transferencia
        $uniqid                     = new THidden('uniqid');
        $id                         = new THidden('id');
        $id_itemtransferencia       = new THidden('id_itemtransferencia');
        $id_transferenciapatrimonio = new THidden('id_transferenciapatrimonio');
        $id_patrimonio              = new THidden('id_patrimonio');
        $nu_plaqueta                = new TSeekButton('nu_plaqueta');
        $id_setordestino            = new THidden('id_setordestino');
        $cd_setordestino            = new TSeekButton('cd_setordestino');
        $nm_setordestino            = new TEntry('nm_setordestino');
        $vl_itemtransferencia       = new TNumeric('vl_itemtransferencia', 2, ',', '.', true);
        
        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Código:', '#333', '14px', null, '100%'),  $cd_transferenciapatrimonio],
                                        [new TLabel('Data Transferencia: (*)', '#FF0000', '14px', null, '100%'), $dt_transferenciapatrimonio],
                                        [new TLabel('Valor Total: (*)', '#FF0000', '14px', null, '100%'), $vl_total]);
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row2 = $this->form->addFields( [new TLabel('Setor Origem: (*)', '#FF0000', '14px', null, '100%'), $id_setororigem, $cd_setororigem, $nm_setororigem]);
        $row2->layout = ['col-sm-12'];

        //Aba detalhe 'Item Transferencia'
        $itemTransferencia = new BootstrapFormBuilder('form_itemTransferencia');
        TScript::create('
            $(document).ready(function() {
            
                $("#form_itemTransferencia .card-body, #form_itemTransferencia .panel-body").css({"padding-left": "10px", "padding-right": "0px"});
            });
        ');

        $itemTransferencia->setProperty('style', 'border: 0');

        $itemTransferencia->appendPage('Item Transferencia');
        
        $itemTransferencia->addContent( ['<hr>'] );
        
        $row7 = $itemTransferencia->addFields( [new TLabel('Patrimônio: (*)', '#FF0000', '14px', null, '100%'), $id_patrimonio, $nu_plaqueta],
                                        [new TLabel('Valor Bem (R$): ', '#333', '14px', null, '100%'),$uniqid, $id, $id_itemtransferencia, $id_transferenciapatrimonio, $vl_itemtransferencia]);
        $row7->layout = ['col-sm-8', 'col-sm-4'];

        $row7 = $itemTransferencia->addFields( [new TLabel('Setor Destino: (*)', '#FF0000', '14px', null, '100%'), $id_setordestino, $cd_setordestino, $nm_setordestino]);
        $row7->layout = ['col-sm-12'];
        
        $addItemTransferencia = TButton::create('addItemTransferencia', [$this, 'onAddItemTransferencia'], 'Adicionar', 'fa:plus-circle green');
        $addItemTransferencia->getAction()->setParameter('static','1');
        $itemTransferencia->addFields( [$addItemTransferencia] );
        
        $this->itemtransferencia_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->itemtransferencia_list->setHeight(150);
        $this->itemtransferencia_list->setId('itenstransferencia_list');
        $this->itemtransferencia_list->generateHiddenFields();
        $this->itemtransferencia_list->style = "min-width: 600px; width:100%";
        $this->itemtransferencia_list->setMutationAction(new TAction([$this, 'onMutationAction']));
        
        $coluna_uniqid              = new TDataGridColumn( 'uniqid', 'uniqId', 'left');
        $coluna_rowid               = new TDataGridColumn( 'id', 'rowId', 'left');
        $coluna_iditemtransferencia = new TDataGridColumn( 'id_itemtransferencia', 'ID Item Transferencia', 'left');
        $coluna_idpatrimonio        = new TDataGridColumn( 'id_patrimonio', 'ID Patrimonio', 'left');
        $coluna_codigo              = new TDataGridColumn( 'nu_plaqueta', 'Patrimônio', 'left', '50%');
        $coluna_idsetor             = new TDataGridColumn( 'id_setordestino', 'Setor Destino', 'left', '50%');
        $coluna_setor               = new TDataGridColumn( 'nm_setordestino', 'Setor Destino', 'left', '50%');
        $coluna_valor               = new TDataGridColumn( 'vl_itemtransferencia', 'Valor', 'left', '20%');
        
        $this->itemtransferencia_list->addColumn( $coluna_uniqid);
        $this->itemtransferencia_list->addColumn( $coluna_rowid);
        $this->itemtransferencia_list->addColumn( $coluna_iditemtransferencia);
        $this->itemtransferencia_list->addColumn( $coluna_idpatrimonio);
        $this->itemtransferencia_list->addColumn( $coluna_codigo);
        $this->itemtransferencia_list->addColumn( $coluna_idsetor);
        $this->itemtransferencia_list->addColumn( $coluna_setor);
        $this->itemtransferencia_list->addColumn( $coluna_valor);

        //Tamanho dos campos
        $cd_transferenciapatrimonio->setSize('100%');
        $dt_transferenciapatrimonio->setSize('100%');
        $vl_total->setSize('100%');
        $vl_itemtransferencia->setSize('100%');

        //Desabilitar campos
        TNumeric::disableField(self::$formName, 'vl_itemtransferencia');
        TNumeric::disableField(self::$formName, 'vl_total');
        TEntry::disableField(self::$formName, 'cd_transferenciapatrimonio');
        TEntry::disableField(self::$formName, 'nm_setororigem');
        TEntry::disableField(self::$formName, 'nm_setordestino');

        //Valor Padrão
        $vl_total->setValue(0);
        $vl_itemtransferencia->setValue(0);
        
        //TSeek
        $nu_plaqueta->setAction(new TAction(['PatrimonioSeekWindow', 'onReload']));
        $nu_plaqueta->setSize('100%');

        $cd_setororigem->setAction(new TAction(['SetorSeekWindow', 'onReload']));
        $cd_setororigem->setSize('30%');
        $nm_setororigem->setSize('70%');
        
        $cd_setordestino->setAction(new TAction(['SetorSeekWindow', 'onReload']));
        $cd_setordestino->setSize('30%');
        $nm_setordestino->setSize('70%');

        //Mascaras
        $dt_transferenciapatrimonio->setMask('dd/mm/yyyy');
        $dt_transferenciapatrimonio->setDatabaseMask('yyyy-mm-dd');
        
        //Coluna invisivel da datagrid
        $coluna_uniqid->setVisibility(false);
        $coluna_rowid->setVisibility(false);
        $coluna_iditemtransferencia->setVisibility(false);
        $coluna_idpatrimonio->setVisibility(false);
        $coluna_idsetor->setVisibility(false);
        
        //Validações
        $dt_transferenciapatrimonio->addValidation('Data transferencia', new TRequiredValidator);
        $cd_setororigem->addValidation(       'Setor Origem', new TRequiredValidator);
        $nu_plaqueta->addValidation(       'Patrimonio', new TRequiredValidator);
        $cd_setordestino->addValidation(       'Setor Destino', new TRequiredValidator);
        
        //Função saida de campo
        $nu_plaqueta->setExitAction(new TAction([$this, 'verificarPatrimonio']));
        $cd_setororigem->setExitAction(new TAction([$this, 'enviarIdSetorSessao']));
        
        //Editar e Excluir da datagrid
        $action1 = new TDataGridAction([$this, 'onEditItemTransferencia'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDeleteItemTransferencia']);
        $action2->setField('uniqid');
        
        //Adicionar as ações na datagrid
        $this->itemtransferencia_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->itemtransferencia_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->itemtransferencia_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->itemtransferencia_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $itemTransferencia->addContent( [$panel] );

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $coluna_valor->setTransformer( $format_value );
        
        //Botões
        $btn_onSave = $this->form->addAction( 'Salvar', new TAction(array($this, 'onSave')), 'fas:save #ffffff');
        $btn_onSave->addStyleClass('btn-success');  
        
        $this->form->addActionLink('Limpar formulário', new TAction(array($this, 'onClear')), 'fa:eraser red');
        
        $this->form->addHeaderActionLink( _t('Close'),  new TAction([$this, 'onClose'], ['static'=>'1']), 'fa:times red');
        
        $this->form->addContent([$itemTransferencia]);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        parent::add($container);
    }
    
    public function onAddItemTransferencia($param)
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            $uniqid = !empty($data->uniqid) ? $data->uniqid : uniqid();

            $grid_data = ['uniqid'               => $uniqid,
                          'id'                   => $data->id,
                          'id_itemtransferencia' => $data->id_itemtransferencia,
                          'id_patrimonio'        => $data->id_patrimonio,
                          'nu_plaqueta'          => $data->nu_plaqueta,
                          'id_setordestino'      => $data->id_setordestino,
                          'cd_setordestino'      => $data->cd_setordestino,
                          'nm_setordestino'      => $data->nm_setordestino,
                          'vl_itemtransferencia' => $data->vl_itemtransferencia
                        ];
            
            $row = $this->itemtransferencia_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('itenstransferencia_list', $uniqid, $row);

            $data->uniqid               = '';
            $data->id                   = '';
            $data->id_itemtransferencia = '';
            $data->id_patrimonio        = '';
            $data->nu_plaqueta          = '';
            $data->id_setordestino      = '';
            $data->cd_setordestino      = '';
            $data->nm_setordestino      = '';
            $data->vl_itemtransferencia = '';
            
            TForm::sendData(self::$formName, $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onEditItemTransferencia($param)
    {
        $data = new stdClass;
        $data->uniqid               = $param['uniqid'];
        $data->id                   = $param['uniqid'];
        $data->id_itemtransferencia = $param['id_itemtransferencia'];
        $data->id_patrimonio        = $param['id_patrimonio'];
        $data->nu_plaqueta          = $param['nu_plaqueta'];
        $data->id_setordestino      = $param['id_setordestino'];
        $data->cd_setordestino      = $param['cd_setordestino'];
        $data->nm_setordestino      = $param['nm_setordestino'];
        $data->vl_itemtransferencia = $param['vl_itemtransferencia'];
        
        TForm::sendData(self::$formName, $data, false, false );
    }

    public static function onDeleteItemTransferencia($param)
    {
        $data = new stdClass;
        $data->uniqid               = '';
        $data->id                   = '';
        $data->id_itemtransferencia = '';
        $data->id_patrimonio        = '';
        $data->nu_plaqueta          = '';
        $data->id_setordestino      = '';
        $data->cd_setordestino      = '';
        $data->nm_setordestino      = '';
        $data->vl_itemtransferencia = '';
        
        // send data, do not fire change/exit events
        TForm::sendData(self::$formName, $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('itenstransferencia_list', $param['uniqid']);
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(self::$database);
            
            $this->form->validate();
            $data = $this->form->getData();

            $object = new TransferenciaPatrimonio;

            $object->fromArray((array) $data);

            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            TSession::setValue('id_setor');
      
            $object->store();

            $itemtranferencia = ItemTransferencia::where('id_transferenciapatrimonio', '=', $object->id_transferenciapatrimonio)->load();
            $id_patrimonio = array_column($itemtranferencia, 'id_patrimonio');

            foreach ($id_patrimonio as $key)
            {
                $patrimonio = Patrimonio::find($key);
                $patrimonio->id_setor = $object->id_setororigem;
                $patrimonio->store();
            }

            ItemTransferencia::where('id_transferenciapatrimonio', '=', $object->id_transferenciapatrimonio)->delete();

            if( !empty($param['itenstransferencia_list_id_patrimonio'] ))
            {
                foreach( $param['itenstransferencia_list_id_patrimonio'] as $key => $patrimonio_id)
                {
                    
                    $patrim = new Patrimonio($patrimonio_id);
                    $item = new ItemTransferencia;
                    $item->id_patrimonio              = $patrimonio_id;              
                    $item->id_transferenciapatrimonio = $object->id_transferenciapatrimonio;
                    $item->id_setordestino            = $param['itenstransferencia_list_id_setordestino'][$key];
                    $item->vl_itemtransferencia       = $patrim->vl_bem;
                    $item->store();
                    
                    $patrimonio = Patrimonio::find($patrimonio_id);
                    $patrimonio->id_setor = $param['itenstransferencia_list_id_setordestino'][$key];
                    $patrimonio->store();
                } 
            }
            
            TForm::sendData(self::$formName, (object) ['id_transferenciapatrimonio' => $object->id_transferenciapatrimonio]); 
            
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('TransferenciaPatrimonioHeaderList', 'onShow');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    public function onEdit($param)
    {
        try
        {
            TTransaction::open(self::$database);
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new TransferenciaPatrimonio($key);

                $itensTransferencia = ItemTransferencia::where('id_transferenciapatrimonio', '=', $object->id_transferenciapatrimonio)->load();
                
                foreach( $itensTransferencia as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->itemtransferencia_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                
                GBSessao::incluirObjetoEdicaoSessao($object, $key, self::$primaryKey, __CLASS__);

                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onMutationAction($param)
    {
        $total = 0;

        if (isset($param['list_data']) && !empty($param['list_data']))
        {
            foreach ($param['list_data'] as $row)
            {
                $total += floatval($row['vl_itemtransferencia']);
            }

            $object = new stdClass;
            $object->vl_total = number_format($total, 2, ',', '.');
            TForm::sendData(self::$formName, $object);
        }
    }
    
    function onClear($param)
    {
        $this->form->clear();
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }
    
    public function onShow($param)
    {
        self::enviarSequencia();
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('TransferenciaPatrimonioHeaderList', 'onShow');
    }

    public static function verificarPatrimonio($param)
    {
        try 
        {
            $row_id = $param['id_itemtransferencia'];
            $nu_plaqueta = @$param['nu_plaqueta'];
            $nu_plaquetaList = @$param['itenstransferencia_list_nu_plaqueta'];

            if (!empty($nu_plaqueta) && !empty($nu_plaquetaList)) {

                if (isset($nu_plaquetaList)) {
                    if (in_array($nu_plaqueta, $nu_plaquetaList) && empty($row_id)) {

                        $object = new stdClass();
                        $object->nu_plaqueta  = '';
                        $object->vl_itemtransferencia = '';
                        TForm::sendData(self::$formName, $object);
                        throw new Exception('Patrimonio já Cadastrado!');
                    }
                }
            }
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function enviarIdSetorSessao($param)
    {
        TSession::setValue('id_setor', $param['id_setororigem']);
    }

    public static function enviarSequencia()
    {
        $object = new stdClass();
        $object->cd_transferenciapatrimonio = TransferenciaPatrimonioService::gerarSequencia(self::$database);
        TForm::sendData(self::$formName, $object);
    }
}