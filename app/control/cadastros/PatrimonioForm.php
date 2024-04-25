<?php

require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class PatrimonioForm extends TPage
{
    private $form;
    private static $database     = 'sample';
    private static $activeRecord = 'Patrimonio';
    private static $formName     = 'form_PatrimonioForm';
    private static $primaryKey   = 'id_patrimonio';

    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Patrimônio');
        $this->form->setClientValidation(true);

        //Patrimonio
        $nu_plaqueta   = new TEntry('nu_plaqueta');
        $dt_aquisicao  = new TDate('dt_aquisicao');
        $tp_situacao   = new TRadioGroup('tp_situacao'); 
        $tp_entrada    = new TCombo('tp_entrada');
        $ds_observacao = new TText('ds_observacao');
        $vl_bem        = new TNumeric('vl_bem', 2, ',', '.', true);

        $id_itembem    = new THidden('id_itembem');
        $cd_itembem    = new TSeekButton('cd_itembem');
        $nm_itembem    = new TEntry('nm_itembem');
        
        $id_setor      = new THidden('id_setor');
        $cd_setor      = new TSeekButton('cd_setor');
        $nm_setor      = new TEntry('nm_setor');

        //Nota fiscal
        $id_notafiscal     = new THidden('id_notafiscal');
        $nu_chavenf        = new TEntry('nu_chavenf');
        $dt_emissao        = new TDate('dt_emissao');
        $nu_serie          = new TEntry('nu_serie');
        $sg_uf             = new TEntry('sg_uf');
        $id_fichacadastral = new THidden('id_fichacadastral');
        $nu_cpfcnpj        = new TSeekButton('nu_cpfcnpj');
        $nm_fichacadastral = new TEntry('nm_fichacadastral');

        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('N° Plaqueta:', '#333', '14px', null, '100%'),  $nu_plaqueta],
                                        [new TLabel('Data Aquisição: (*)', '#FF0000', '14px', null, '100%'), $dt_aquisicao],
                                        [new TLabel('Situação: (*)', '#FF0000', '14px', null, '100%'), $tp_situacao]);
        $row1->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row2 = $this->form->addFields( [new TLabel('Tipo Entrada: (*)', '#FF0000', '14px', null, '100%'), $tp_entrada],
                                        [new TLabel('Valor Bem (R$): (*)', '#FF0000', '14px', null, '100%'), $vl_bem]);
        $row2->layout = ['col-sm-8', 'col-sm-4'];

        $row4 = $this->form->addFields( [new TLabel('Item/Bem: (*)', '#FF0000', '14px', null, '100%'), $id_itembem, $cd_itembem, $nm_itembem],
                                        [new TLabel('Setor: (*)', '#FF0000', '14px', null, '100%'), $id_setor, $cd_setor, $nm_setor]);
        $row4->layout = ['col-sm-6', 'col-sm-6'];
        
        $row3 = $this->form->addFields( [new TLabel('Observação: (*)', '#FF0000', '14px', null, '100%'), $ds_observacao]);
        $row3->layout = ['col-sm-12'];
        
        //Aba 'Nota Fiscal'
        $notaFiscal = new BootstrapFormBuilderMod(self::$formName);
        $notaFiscal->setProperty('style', 'border: 0');

        $notaFiscal->appendPage('Nota Fiscal');

        $row5 = $notaFiscal->addFields( [new TLabel('Chave NF: (*)', '#FF0000', '14px', null, '100%'), $id_notafiscal, $nu_chavenf]);
        $row5->layout = ['col-sm-12'];
        
        $row6 = $notaFiscal->addFields( [new TLabel('Data Emissão: (*)', '#FF0000', '14px', null, '100%'), $dt_emissao],
                                        [new TLabel('Serie: (*)', '#FF0000', '14px', null, '100%'), $nu_serie],
                                        [new TLabel('UF: (*)', '#FF0000', '14px', null, '100%'), $sg_uf]);
        $row6->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row7 = $notaFiscal->addFields( [new TLabel('CPF/CNPJ:', '#333', '14px', null, '100%'), $id_fichacadastral, $nu_cpfcnpj, $nm_fichacadastral]);
        $row7->layout = ['col-sm-12'];

        //Tamanho do campo
        $nu_plaqueta->setSize('100%');
        $dt_aquisicao->setSize('100%');
        $tp_situacao->setSize(80);
        $tp_entrada->setSize('100%');
        $ds_observacao->setSize('100%');
        $vl_bem->setSize('100%');

        $nu_chavenf->setSize('100%');
        $dt_emissao->setSize('100%');
        $nu_serie->setSize('100%');
        $sg_uf->setSize('100%');

        //Mascaras
        $dt_aquisicao->setMask('dd/mm/yyyy');
        $dt_aquisicao->setDatabaseMask('yyyy-mm-dd');
        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $sg_uf->setMask('SS', true);
        $sg_uf->forceUpperCase();

        //Valor Padrão
        $vl_bem->setValue(0);

        //Desabilitar campos
        TEntry::disableField(self::$formName, 'nm_itembem');
        TEntry::disableField(self::$formName, 'nm_setor');
        TEntry::disableField(self::$formName, 'nm_fichacadastral');
        TEntry::disableField(self::$formName, 'nu_plaqueta');

        //TCombo
        $tp_entrada->addItems(["1" => "Inventário", "2" => "Compra", "3" => "Outras"]);
        
        //RadioGroup
        $tp_situacao->addItems(["1" => "Ativo", "2" => "Baixado"]);
        $tp_situacao->setLayout('horizontal');
        $tp_situacao->setValue(1);
        $tp_situacao->setUseButton();

        //TSeek
        $cd_itembem->setAction(new TAction(['ItemBemSeekWindow', 'onReload']));
        $cd_itembem->setSize('30%');
        $nm_itembem->setSize('70%');

        $cd_setor->setAction(new TAction(['SetorSeekWindow', 'onReload']));
        $cd_setor->setSize('30%');
        $nm_setor->setSize('70%'); 

        $nu_cpfcnpj->setAction(new TAction(['FichaCadastralSeekWindow', 'onReload']));
        $nu_cpfcnpj->setSize('30%');
        $nm_fichacadastral->setSize('70%');

        //Função saida de campo
        $tp_entrada->setChangeAction(new TAction([$this, 'esconderAbaNotaFiscal']));

        //Validações
        $dt_aquisicao->addValidation(    'Data Aquisição', new TRequiredValidator);
        $tp_entrada->addValidation(    'Tipo Entrada', new TRequiredValidator);
        $vl_bem->addValidation(    'Valor Bem', new TRequiredValidator);
        $cd_itembem->addValidation(    'Item/Bem', new TRequiredValidator);
        $cd_setor->addValidation(    'Setor', new TRequiredValidator);
        $ds_observacao->addValidation('Observação', new TRequiredValidator);
        
        $this->form->addContent([$notaFiscal]);

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

            $object = new Patrimonio;  
            $object->fromArray( (array) $data);
            
            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            self::esconderAbaNotaFiscal($param);

            $object->store();
            
            $this->form->setData($object);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('PatrimonioHeaderList', 'onShow');
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
                
                $object = new Patrimonio($key);
                
                $object->cd_itembem = $object->itembem->cd_itembem;
                $object->nm_itembem = $object->itembem->nm_itembem;
                $object->cd_setor   = $object->setor->cd_setor;
                $object->nm_setor   = $object->setor->nm_setor;
                
                $this->form->setData($object);
                self::esconderAbaNotaFiscal(null, $object->tp_entrada);
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
    
    public function onShow($param)
    {
        self::enviarSequencia();
        self::esconderAbaNotaFiscal($param);
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('PatrimonioHeaderList', 'onShow');
    }

    public static function esconderAbaNotaFiscal($param = null, $object = null)
    {
        $tp_entrada = $param['tp_entrada'] ?? $object;

        $aba = 'hide';

        if($tp_entrada == 2) 
        {
            $aba = 'show';
            BootstrapFormBuilder::showField(self::$formName, 'nu_chavenf');
            BootstrapFormBuilder::showField(self::$formName, 'dt_emissao');
            BootstrapFormBuilder::showField(self::$formName, 'nu_cpfcnpj');
        } 
        else
        {
            BootstrapFormBuilder::hideField(self::$formName, 'nu_chavenf');
            BootstrapFormBuilder::hideField(self::$formName, 'dt_emissao');
            BootstrapFormBuilder::hideField(self::$formName, 'nu_cpfcnpj');
        }

        TScript::create("$('li[role=presentation]').eq(0).$aba();");
        
    }

    public static function enviarSequencia()
    {
        $object = new stdClass();
        $object->nu_plaqueta = PatrimonioService::gerarSequencia(self::$database);
        TForm::sendData(self::$formName, $object);
    }
}