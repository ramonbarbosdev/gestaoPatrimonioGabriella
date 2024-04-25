<?php

require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class BaixaPatrimonioForm extends TPage
{
    protected $form; 
    private static $database       = 'sample';
    private static $activeRecord   = 'BaixaPatrimonio';
    private static $formName       = 'form_BaixaPatrimonioForm'; 
    private static $primaryKey     = 'id_baixapatrimonio';
    
    function __construct()
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Baixa Patrimônio');
        $this->form->setClientValidation(true);

        //Baixa Patrimonio
        $cd_baixapatrimonio = new TEntry('cd_baixapatrimonio');
        $dt_baixapatrimonio = new TDate('dt_baixapatrimonio');
        $vl_total           = new TNumeric('vl_total', 2, ',', '.', true);
        $ds_observacao      = new TText('ds_observacao');
        
        //Item Baixa
        $uniqid        = new THidden('uniqid');
        $id            = new THidden('id');
        $id_itembaixa  = new THidden('id_itembaixa');
        $id_patrimonio = new THidden('id_patrimonio');
        $nu_plaqueta   = new TSeekButton('nu_plaqueta');
        $vl_itembaixa  = new TNumeric('vl_itembaixa', 2, ',', '.', true);
        
        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Código:', '#333', '14px', null, '100%'),  $cd_baixapatrimonio],
                                        [new TLabel('Data Baixa: (*)', '#FF0000', '14px', null, '100%'), $dt_baixapatrimonio],
                                        [new TLabel('Valor Total (R$): ', '#333', '14px', null, '100%'), $vl_total]);
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row2 = $this->form->addFields( [new TLabel('Observação: (*)', '#FF0000', '14px', null, '100%'), $ds_observacao]);
        $row2->layout = ['col-sm-12'];

        //Aba detalhe 'Item Baixa'
        $itemBaixa = new BootstrapFormBuilder('form_itemBaixa');
        TScript::create('
            $(document).ready(function() {
            
                $("#form_itemBaixa .card-body, #form_itemBaixa .panel-body").css({"padding-left": "10px", "padding-right": "0px"});
            });
        ');

        $itemBaixa->setProperty('style', 'border: 0');

        $itemBaixa->appendPage('Item Baixa');
        
        $itemBaixa->addContent( ['<hr>'] );
        
        $row7 = $itemBaixa->addFields( [new TLabel('Patrimônio: (*)', '#FF0000', '14px', null, '100%'), $id_patrimonio, $nu_plaqueta],
                                        [new TLabel('Valor Bem (R$):', '#333', '14px', null, '100%'),$uniqid, $id, $id_itembaixa, $vl_itembaixa]);
        $row7->layout = ['col-sm-8', 'col-sm-4'];
        
        $addItemBaixa = TButton::create('addItemBaixa', [$this, 'onAddItemBaixa'], 'Adicionar', 'fa:plus-circle green');
        $addItemBaixa->getAction()->setParameter('static','1');
        $itemBaixa->addFields( [$addItemBaixa] );
        
        $this->itembaixa_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->itembaixa_list->setHeight(150);
        $this->itembaixa_list->setId('itensbaixa_list');
        $this->itembaixa_list->generateHiddenFields();
        $this->itembaixa_list->style = "min-width: 600px; width:100%";
        $this->itembaixa_list->setMutationAction(new TAction([$this, 'onMutationAction']));
        
        $coluna_uniqid       = new TDataGridColumn( 'uniqid', 'uniqId', 'left');
        $coluna_rowid        = new TDataGridColumn( 'id', 'rowId', 'left');
        $coluna_iditembaixa  = new TDataGridColumn( 'id_itembaixa', 'ID Item Baixa', 'left');
        $coluna_idpatrimonio = new TDataGridColumn( 'id_patrimonio', 'ID Patrimonio', 'left');
        $coluna_codigo       = new TDataGridColumn( 'nu_plaqueta', 'Patrimônio', 'left', '50%');
        $coluna_valor        = new TDataGridColumn( 'vl_itembaixa', 'Valor', 'left', '20%');
        
        $this->itembaixa_list->addColumn( $coluna_uniqid);
        $this->itembaixa_list->addColumn( $coluna_rowid);
        $this->itembaixa_list->addColumn( $coluna_iditembaixa);
        $this->itembaixa_list->addColumn( $coluna_idpatrimonio);
        $this->itembaixa_list->addColumn( $coluna_codigo);
        $this->itembaixa_list->addColumn( $coluna_valor);

        //Tamanho dos campos
        $cd_baixapatrimonio->setSize('100%');
        $dt_baixapatrimonio->setSize('100%');
        $vl_total->setSize('100%');
        $ds_observacao->setSize('100%');
        $vl_itembaixa->setSize('100%');

        //Desabilitar campos
        TNumeric::disableField(self::$formName, 'vl_itembaixa');
        TNumeric::disableField(self::$formName, 'vl_total');
        TEntry::disableField(self::$formName, 'cd_baixapatrimonio');

        //Valor Padrão
        $vl_total->setValue(0);
        $vl_itembaixa->setValue(0);
        
        //TSeek
        $nu_plaqueta->setAction(new TAction(['PatrimonioSeekWindow', 'onReload']));
        $nu_plaqueta->setSize('100%');

        //Mascaras
        $dt_baixapatrimonio->setMask('dd/mm/yyyy');
        $dt_baixapatrimonio->setDatabaseMask('yyyy-mm-dd');
        
        //Coluna invisivel da datagrid
        $coluna_uniqid->setVisibility(false);
        $coluna_rowid->setVisibility(false);
        $coluna_iditembaixa->setVisibility(false);
        $coluna_idpatrimonio->setVisibility(false);

        //Função saida de campo
        $nu_plaqueta->setExitAction(new TAction([$this, 'verificarPatrimonio']));
        
        //Validações
        $dt_baixapatrimonio->addValidation('Data Baixa', new TRequiredValidator);
        $ds_observacao->addValidation(     'Observação', new TRequiredValidator);
        $nu_plaqueta->addValidation(       'Patrimonio', new TRequiredValidator);
        
        //Editar e Excluir da datagrid
        $action1 = new TDataGridAction([$this, 'onEditItemBaixa'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDeleteItemBaixa']);
        $action2->setField('uniqid');
        
        //Adicionar as ações na datagrid
        $this->itembaixa_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->itembaixa_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->itembaixa_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->itembaixa_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $itemBaixa->addContent( [$panel] );

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
        
        $this->form->addContent([$itemBaixa]);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        parent::add($container);
    }
    
    public function onAddItemBaixa($param)
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            $uniqid = !empty($data->uniqid) ? $data->uniqid : uniqid();

            $grid_data = ['uniqid'        => $uniqid,
                          'id'            => $data->id,
                          'id_itembaixa'  => $data->id_itembaixa,
                          'id_patrimonio' => $data->id_patrimonio,
                          'nu_plaqueta'   => $data->nu_plaqueta,
                          'vl_itembaixa'  => $data->vl_itembaixa
                        ];
            
            $row = $this->itembaixa_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('itensbaixa_list', $uniqid, $row);

            $data->uniqid        = '';
            $data->id            = '';
            $data->id_itembaixa  = '';
            $data->id_patrimonio = '';
            $data->nu_plaqueta   = '';
            $data->vl_itembaixa  = '';
            
            TForm::sendData(self::$formName, $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onEditItemBaixa($param)
    {
        $data = new stdClass;
        $data->uniqid        = $param['uniqid'];
        $data->id            = $param['uniqid'];
        $data->id_itembaixa  = $param['id_itembaixa'];
        $data->id_patrimonio = $param['id_patrimonio'];
        $data->nu_plaqueta   = $param['nu_plaqueta'];
        $data->vl_itembaixa  = $param['vl_itembaixa'];
        
        TForm::sendData(self::$formName, $data, false, false );
    }

    public static function onDeleteItemBaixa($param)
    {
        $data = new stdClass;
        $data->uniqid        = '';
        $data->id            = '';
        $data->id_itembaixa  = '';
        $data->id_patrimonio = '';
        $data->nu_plaqueta   = '';
        $data->vl_itembaixa  = '';
        
        // send data, do not fire change/exit events
        TForm::sendData(self::$formName, $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('itensbaixa_list', $param['uniqid']);
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(self::$database);
            
            $this->form->validate();
            $data = $this->form->getData();

            $object = new BaixaPatrimonio;

            $object->fromArray((array) $data);

            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);
            
            $object->store();

            $itembaixa = ItemBaixa::where('id_baixapatrimonio', '=', $object->id_baixapatrimonio)->load();

            $id_patrimonio = array_column($itembaixa, 'id_patrimonio');

            foreach($id_patrimonio as $key)
            {
                $patrimonio = Patrimonio::find($key);
                $patrimonio->tp_situacao = 1;
                $patrimonio->store();
            }

            ItemBaixa::where('id_baixapatrimonio', '=', $object->id_baixapatrimonio)->delete();

            if( !empty($param['itensbaixa_list_id_patrimonio'] ))
            {
                foreach( $param['itensbaixa_list_id_patrimonio'] as $key => $patrimonio_id)
                {
                    $patrim = new Patrimonio($patrimonio_id);
                    $item = new ItemBaixa;
                    $item->id_patrimonio      = $patrimonio_id;              
                    $item->id_baixapatrimonio = $object->id_baixapatrimonio;
                    $item->vl_itembaixa       = $patrim->vl_bem;
                    $item->store();

                    $patrimonio = Patrimonio::find($patrimonio_id);
                    $patrimonio->tp_situacao = 2;
                    $patrimonio->store();
                } 
            }
            
            TForm::sendData(self::$formName, (object) ['id_baixapatrimonio' => $object->id_baixapatrimonio]); 
            
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('BaixaPatrimonioHeaderList', 'onShow');
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
                
                $object = new BaixaPatrimonio($key);

                $itensBaixa = ItemBaixa::where('id_baixapatrimonio', '=', $object->id_baixapatrimonio)->load();
                
                foreach( $itensBaixa as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->itembaixa_list->addItem( $item );
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
                $total += floatval($row['vl_itembaixa']);
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
        AdiantiCoreApplication::loadPage('BaixaPatrimonioHeaderList', 'onShow');
    }

    public static function verificarPatrimonio($param)
    {
        try 
        {
            $row_id = $param['id_itembaixa'];
            $nu_plaqueta = @$param['nu_plaqueta'];
            $nu_plaquetaList = @$param['itensbaixa_list_nu_plaqueta'];

            if (!empty($nu_plaqueta) && !empty($nu_plaquetaList)) {

                if (isset($nu_plaquetaList)) {
                    if (in_array($nu_plaqueta, $nu_plaquetaList) && empty($row_id)) {

                        $object = new stdClass();
                        $object->nu_plaqueta  = '';
                        $object->vl_itembaixa = '';
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

    public static function enviarSequencia()
    {
        $object = new stdClass();
        $object->cd_baixapatrimonio = BaixaPatrimonioService::gerarSequencia(self::$database);
        TForm::sendData(self::$formName, $object);
    }
}