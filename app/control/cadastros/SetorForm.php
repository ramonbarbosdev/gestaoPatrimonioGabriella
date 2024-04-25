<?php

require_once (PATH . '/app/service/SetorService.php');
require_once (PATH . '/app/service/EnderecoSetorService.php');
require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class SetorForm extends TPage
{
    private $form;
    private static $database     = 'sample';
    private static $activeRecord = 'Setor';
    private static $formName     = 'form_SetorForm'; 
    private static $primaryKey   = 'id_setor'; 

    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Setor');
        $this->form->setClientValidation(true);

        //Setor
        $cd_setor          = new TEntry('cd_setor');
        $nm_setor          = new TEntry('nm_setor'); 
        $id_fichacadastral = new THidden('id_fichacadastral');  
        $nu_cpfcnpj        = new TSeekButton('nu_cpfcnpj');
        $nm_fichacadastral = new TEntry('nm_fichacadastral');

        //Endereço
        $id_endereco       = new THidden('id_endereco');
        $nu_cep            = new TEntry('nu_cep');
        $ds_logradouro     = new TEntry('ds_logradouro');
        $nm_bairro         = new TEntry('nm_bairro');
        $nu_endereco       = new TEntry('nu_endereco');
        $ds_complemento    = new TEntry('ds_complemento');
        $sg_uf             = new TEntry('sg_uf');
        $nm_cidade         = new TEntry('nm_cidade');

        $nu_cpfcnpj->setAction(new TAction(['FichaCadastralSeekWindow', 'onReload']));
        $nu_cpfcnpj->setSize('30%');
        $nm_fichacadastral->setSize('70%');
        
        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Código: (*)', '#FF0000', '14px', null, '100%'), $cd_setor],
                                        [new TLabel('Setor: (*)', '#FF0000', '14px', null, '100%'), $nm_setor]);
        $row1->layout = ['col-sm-4', 'col-sm-8'];
        
        $row2 = $this->form->addFields( [$id_fichacadastral, new TLabel('Pessoa Responsável: (*)', '#FF0000', '14px', null, '100%'), $nu_cpfcnpj, $nm_fichacadastral]);
        $row2->layout = ['col-sm-12'];

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

        //Tamanho do campo
        $cd_setor->setSize('100%');
        $nm_setor->setSize('100%');

        $nu_cep->setSize('100%');
        $ds_logradouro->setSize('100%');
        $nm_bairro->setSize('100%');
        $nu_endereco->setSize('100%');
        $ds_complemento->setSize('100%');
        $sg_uf->setSize('100%');
        $nm_cidade->setSize('100%');

        //Máscaras
        $nu_cep->setMask(           '99999-999', true);
        $sg_uf->setMask(                   'SS', true);
        $sg_uf->forceUpperCase();

        //Desabilitar campo
        TEntry::disableField(self::$formName, 'nm_fichacadastral');
        TEntry::disableField(self::$formName, 'cd_setor');
        
        //Validações
        $nm_setor->addValidation(                'Setor', new TRequiredValidator);
        $nu_cpfcnpj->addValidation( 'Pessoa Responsável', new TRequiredValidator);

        $nu_cep->addValidation(            'CEP', new TRequiredValidator);
        $sg_uf->addValidation(              'UF', new TRequiredValidator);
        $nm_cidade->addValidation(      'Cidade', new TRequiredValidator);
        $nm_bairro->addValidation(      'Bairro', new TRequiredValidator);
        $ds_logradouro->addValidation('Endereço', new TRequiredValidator);

        //Funções de saída de campo
        if(!isset($param['key']))
        {
            $nu_cep->setExitAction( new TAction([ $this, 'onExitCEP']) );
        }

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
            $cep_data = EnderecoSetorService::cepApi($fieldValue);

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

            $object = new Setor();  
            $object->fromArray( (array) $data);
           
            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            $object->store();

            EnderecoSetorService::onSaveEndereco($object, $param);
            
            $this->form->setData($object);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('SetorHeaderList', 'onShow');
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

                $object = new Setor($key);

                $object->nu_cpfcnpj        = $object->fichacadastral->nu_cpfcnpj;
                $object->nm_fichacadastral = $object->fichacadastral->nm_fichacadastral;

                $endereco = EnderecoSetor::where('id_setor', '=', $key)->first();

                TForm::sendData(self::$formName, (object) $endereco);

                $this->form->setData($object);
                
                GBSessao::incluirObjetoEdicaoSessao($object, $key, self::$primaryKey, __CLASS__);

                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }

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
        $object->cd_setor = SetorService::gerarSequencia(self::$database);
        TForm::sendData(self::$formName, $object);
    }

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('SetorHeaderList', 'onShow');
    }
}
