<?php

require_once (PATH . '/app/service/FichaCadastralService.php');
require_once (PATH . '/app/service/EnderecoService.php');
require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class FichaCadastralForm extends TPage
{
    private $form;
    private $form_endereco;
    private static $database     = 'sample';
    private static $activeRecord = 'FichaCadastral';
    private static $formName     = 'form_FichaCadastralForm';
    private static $primaryKey   = 'id_fichacadastral';

    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Ficha Cadastral');
        $this->form->setClientValidation(true);

        //PF ou PJ 
        $tp_fichacadastral = new TRadioGroup('tp_fichacadastral'); 
        $nm_fichacadastral = new TEntry('nm_fichacadastral');
        $nu_cpfcnpj        = new TEntry('nu_cpfcnpj');
        $ds_email          = new TEntry('ds_email');
        $nu_telefone       = new TEntry('nu_telefone');
        $nm_fantasia       = new TEntry('nm_fantasia');

        //Endereço
        $id_endereco       = new THidden('id_endereco');
        $nu_cep            = new TEntry('nu_cep');
        $ds_logradouro     = new TEntry('ds_logradouro');
        $nm_bairro         = new TEntry('nm_bairro');
        $nu_endereco       = new TEntry('nu_endereco');
        $ds_complemento    = new TEntry('ds_complemento');
        $sg_uf             = new TEntry('sg_uf');
        $nm_cidade         = new TEntry('nm_cidade');

        //Tamanho do campo
        $tp_fichacadastral->setSize('100%');
        $nm_fichacadastral->setSize('100%');
        $nu_cpfcnpj->setSize('100%');
        $ds_email->setSize('100%');
        $nu_telefone->setSize('100%');
        $nm_fantasia->setSize('100%');

        $nu_cep->setSize('100%');
        $ds_logradouro->setSize('100%');
        $nm_bairro->setSize('100%');
        $nu_endereco->setSize('100%');
        $ds_complemento->setSize('100%');
        $sg_uf->setSize('100%');
        $nm_cidade->setSize('100%');

        //Máscaras
        $nu_cpfcnpj->setMask(  '999.999.999-99', true);
        $nu_telefone->setMask('(99) 99999-9999', true);
        $nu_cep->setMask(           '99999-999', true);
        $sg_uf->setMask(                   'SS', true);
        $sg_uf->forceUpperCase();
        
        $tp_fichacadastral->addItems(["1" => "Pessoa Física", "2" => "Pessoa Juridica"]);
        $tp_fichacadastral->setLayout('horizontal');
        $tp_fichacadastral->setValue('1');
        $tp_fichacadastral->setUseButton();
        
        //Ocultar campo 
        self::ocultarExibirCampoNomeFantasia('hide');

        //Funções de saída de campo
        $tp_fichacadastral->setChangeAction(new TAction([$this,'alterarFichaCadastral']));
        if(!isset($param['key']))
        {
            $nu_cep->setExitAction( new TAction([ $this, 'onExitCEP']) );
        }

        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Tipo Ficha Cadastral:', '#333', '14px', null, '100%'), $tp_fichacadastral]);
        $row1->layout = ['col-sm-4'];
        
        $row2 = $this->form->addFields( [new TLabel('CPF: (*)', '#FF0000', '14px', null, '100%'), $nu_cpfcnpj],
                                        [new TLabel('Nome Fantasia:', '#333', '14px', null, '100%'), $nm_fantasia]);
        $row2->layout = ['col-sm-4', 'col-sm-8'];

        $row3 = $this->form->addFields( [new TLabel('Nome Completo: (*)', '#FF0000', '14px', null, '100%'), $nm_fichacadastral]);
        $row3->layout = ['col-sm-12'];
        
        $row4 = $this->form->addFields( [new TLabel('Telefone:', '#333', '14px', null, '100%'), $nu_telefone],
                                        [new TLabel('Email: (*)', '#FF0000', '14px', null, '100%'), $ds_email]);
        $row4->layout = ['col-sm-6', 'col-sm-6'];

        //Aba 'Endereço'
        $endereco = new BootstrapFormBuilderMod(self::$formName);
        $endereco->setProperty('style', 'border: 0');

        $endereco->appendPage('Endereço');
        
        $row5 = $endereco->addFields(   [$id_endereco, new TLabel('CEP: (*)', '#FF0000', '14px', null, '100%'), $nu_cep],
                                        [new TLabel('UF: (*)', '#FF0000', '14px', null, '100%'), $sg_uf],
                                        [new TLabel('Cidade: (*)', '#FF0000', '14px', null, '100%'), $nm_cidade]);
        $row5->layout = ['col-sm-4', 'col-sm-2', 'col-sm-6' ];
        
        $row6 = $endereco->addFields(   [new TLabel('Bairro: (*)', '#FF0000', '14px', null, '100%'), $nm_bairro],
                                        [new TLabel('Endereço: (*)', '#FF0000', '14px', null, '100%'), $ds_logradouro]);
        $row6->layout = ['col-sm-6', 'col-sm-6'];
        
        $row7 = $endereco->addFields(   [new TLabel('Número:', '#333', '14px', null, '100%'), $nu_endereco],
                                        [new TLabel('Complemento:', '#333', '14px', null, '100%'), $ds_complemento]);
        $row7->layout = ['col-sm-4', 'col-sm-8'];

        //Validações
        $nu_cpfcnpj->addValidation(                         'CPF/CNPJ', new TRequiredValidator);
        $nm_fichacadastral->addValidation('Nome Completo/Razão Social', new TRequiredValidator);
        $ds_email->addValidation(                              'Email', new TRequiredValidator);
        
        $nu_cep->addValidation(            'CEP', new TRequiredValidator);
        $sg_uf->addValidation(              'UF', new TRequiredValidator);
        $nm_cidade->addValidation(      'Cidade', new TRequiredValidator);
        $nm_bairro->addValidation(      'Bairro', new TRequiredValidator);
        $ds_logradouro->addValidation('Endereço', new TRequiredValidator);
        
        $this->form->addContent([$endereco]);

        //Botões
        $btn_onSave = $this->form->addAction( 'Salvar', new TAction(array($this, 'onSave')), 'fas:save #ffffff');
        $btn_onSave->addStyleClass('btn-success');  

        $this->form->addActionLink('Limpar formulário', new TAction(array($this, 'onClear')), 'fa:eraser red');

        $this->form->addHeaderActionLink( _t('Close'),  new TAction([$this, 'onClose'], ['static'=>'1']), 'fa:times red');

        parent::add($this->form);
    }

    public static function onExitCEP($param)
    {
        session_write_close();
        
        try
        {
            $fieldValue = $param['_field_value'] ?? null;
            $cep_data = EnderecoService::cepApi($fieldValue);

            $data = new stdClass;

            if (is_object($cep_data) && empty($cep_data->erro))
            {
                $data->ds_logradouro  = $cep_data->logradouro;
                $data->ds_complemento = $cep_data->complemento;
                $data->nm_bairro      = $cep_data->bairro;
                $data->sg_uf          = $cep_data->uf;
                $data->nm_cidade      = $cep_data->localidade;
            }
            else
            {
                $data->ds_logradouro  = '';
                $data->ds_complemento = '';
                $data->nm_bairro      = '';
                $data->sg_uf          = '';
                $data->nm_cidade      = '';    
            }

            TForm::sendData(self::$formName, $data, false, true);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(self::$database);

            $data = $this->form->getData();

            $this->form->validate();

            $object = new FichaCadastral;  
            $object->fromArray( (array) $data);

            $tp_fichacadastral = $param['tp_fichacadastral'] ?? null;
            $nu_cpfcnpj        = $param['nu_cpfcnpj'] ?? null;
            $nm_fichacadastral = $param['nm_fichacadastral'] ?? null;

            FichaCadastralService::verificarCampoCNPJCPF($tp_fichacadastral, $nu_cpfcnpj);
            FichaCadastralService::verificarCampoNomeRazao($tp_fichacadastral, $nm_fichacadastral);

            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            $object->store();
            
            EnderecoService::onSaveEndereco($object, $param);
            
            $this->form->setData($object);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('FichaCadastralHeaderList', 'onShow');
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
                
                $object = new FichaCadastral($key);
                
                $endereco = Endereco::where('id_fichacadastral', '=', $key)->first();

                TForm::sendData(self::$formName, (object) $endereco );
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
    
    public function onShow($param)
    {
        GBSessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public static function alterarFichaCadastral($param)
    {
        $tp_fichacadastral = $param['tp_fichacadastral'] ?? null;

        if($tp_fichacadastral == 1) //Pessoa fisica
        {
            //Altera o rotulo
            TScript::create("$('label:contains(\"CNPJ: (*)\")').html('CPF: (*)')");
            TScript::create("$('label:contains(\"Razão Social: (*)\")').html('Nome Completo: (*)')");

            //Limpa e altera a máscara
            self::limparFichaCadastral();
            TEntry::changeMask(self::$formName, 'nu_cpfcnpj', '000.000.000-00');

            //Ocultar campo
            self::ocultarExibirCampoNomeFantasia('hide');
        }
        else //Pessoa Juridica
        {
            //Altera o rotulo
            TScript::create("$('label:contains(\"CPF: (*)\")').html('CNPJ: (*)')");
            TScript::create("$('label:contains(\"Nome Completo: (*)\")').html('Razão Social: (*)')");

            //Limpa e altera a máscara
            self::limparFichaCadastral();
            TEntry::changeMask(self::$formName, 'nu_cpfcnpj', '00.000.000/0000-00');

            //Exibir campo
            self::ocultarExibirCampoNomeFantasia('show');
        }
    }

    public static function limparFichaCadastral()
    {
        //Limpar campos
        TEntry::clearField(self::$formName, 'nu_cpfcnpj');
        TEntry::clearField(self::$formName, 'nm_fichacadastral');
        TEntry::clearField(self::$formName, 'nm_fantasia');
        TEntry::clearField(self::$formName, 'nu_telefone');
        TEntry::clearField(self::$formName, 'ds_email');
    }
    
    public static function ocultarExibirCampoNomeFantasia($situação)
    {
        //Ocultar ou exibir campo nm_fantasia
        TScript::create("$('label:contains(\"Nome Fantasia:\")').$situação();");
        TScript::create("$(\"[name='nm_fantasia']\").closest('.form-control.tfield').$situação()");
    }

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('FichaCadastralHeaderList', 'onShow');
    }
}
