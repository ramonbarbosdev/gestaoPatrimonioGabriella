<?php

require_once (PATH . '/app/utils/GBSessao.php');

class ItemBemForm extends TPage
{
    private $form;
    private static $database     = 'sample';
    private static $activeRecord = 'ItemBem';
    private static $formName     = 'form_ItemBemForm'; 
    private static $primaryKey   = 'id_itembem'; 

    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Item/Bem');
        $this->form->setClientValidation(true);

        $cd_itembem       = new TEntry('cd_itembem');
        $nm_itembem       = new TEntry('nm_itembem');
        $nm_unidademedida = new TCombo('nm_unidademedida');
        $id_grupobem      = new THidden('id_grupobem');  
        $cd_grupobem      = new TSeekButton('cd_grupobem');
        $nm_grupobem      = new TEntry('nm_grupobem');

        $nm_unidademedida->addItems( ['Unidade' => 'Unidade (un)', 'Quilograma' => 'Quilograma (kg)', 'Litro' => 'Litro (L)', 'Metro' => 'Metro (m)', 'Metro Quadrado' => 'Metro Quadrado (m²)'] );

        $cd_grupobem->setAction(new TAction(['GrupoBemSeekWindow', 'onReload']));
        $cd_grupobem->setSize('30%');
        $nm_grupobem->setSize('70%'); 
        
        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Código: (*)', '#FF0000', '14px', null, '100%'), $cd_itembem],
                                        [new TLabel('Item/Bem: (*)', '#FF0000', '14px', null, '100%'), $nm_itembem]);
        $row1->layout = ['col-sm-4', 'col-sm-8'];
        
        $row2 = $this->form->addFields( [new TLabel('Grupo Bem: (*)', '#FF0000', '14px', null, '100%'),$id_grupobem,$cd_grupobem, $nm_grupobem],
                                        [new TLabel('Unidade de Medida: (*)', '#FF0000', '14px', null, '100%'), $nm_unidademedida]);
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        //Tamanho do campo
        $cd_itembem->setSize('100%');
        $nm_itembem->setSize('100%');
        $nm_unidademedida->setSize('100%');

        //Desabilitar campo
        TEntry::disableField(self::$formName, 'nm_grupobem');
        $cd_itembem->setEditable(false);
        
        //Validações
        $nm_itembem->addValidation(               'Item/Bem', new TRequiredValidator);
        $cd_grupobem->addValidation(             'Grupo Bem', new TRequiredValidator);
        $nm_unidademedida->addValidation('Unidade de Medida', new TRequiredValidator);

        //Botões
        $btn_onSave = $this->form->addAction( 'Salvar', new TAction(array($this, 'onSave')), 'fas:save #ffffff');
        $btn_onSave->addStyleClass('btn-success');  

        $this->form->addActionLink('Limpar formulário', new TAction(array($this, 'onClear')), 'fa:eraser red');

        $this->form->addHeaderActionLink( _t('Close'),  new TAction([$this, 'onClose'], ['static'=>'1']), 'fa:times red');

        parent::add($this->form);
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(self::$database);

            $data = $this->form->getData();

            $this->form->validate();

            $object = new ItemBem();  
            $object->fromArray( (array) $data);
           
            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            $object->store();
            
            $this->form->setData($object);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('ItemBemHeaderList', 'onShow');
        } 
        catch(Exception $erro)
        {
            new TMessage('error', $erro->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }

    }

    public function onEdit($param)
    {
        try 
        {
            TTransaction::open(self::$database);

            if(isset($param['key']))
            {
                $key = $param['key'];
                $object = new ItemBem($key);

                $object->cd_grupobem = $object->grupobem->cd_grupobem;
                $object->nm_grupobem = $object->grupobem->nm_grupobem;
                
                $this->form->setData($object);

                GBSessao::incluirObjetoEdicaoSessao($object, $key, self::$primaryKey, __CLASS__);
            }
            else
            {
                $this->form->clear();
            }

            TTransaction::close();
        }
        catch(Exception $erro)
        {
            new TMessage('error', $erro->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $acao = new TAction([__CLASS__, 'clear']);
        $acao->setParameters($param);
        new TQuestion('Deseja limpar o formulário?', $acao);
    }

    public function clear()
    {
        $this->form->clear(true);
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public function onShow($param = null)
    {
        self::enviarSequencia();
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public static function enviarSequencia()
    {
        $object = new stdClass();
        $object->cd_itembem = ItemBemService::gerarSequencia(self::$database);
        TForm::sendData(self::$formName, $object);
    }

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('ItemBemHeaderList', 'onShow');
    }

}
