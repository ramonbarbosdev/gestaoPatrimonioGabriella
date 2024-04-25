<?php
use Adianti\Widget\Form\TEntry;

require_once (PATH . '/app/service/GrupoBemService.php');
require_once (PATH . '/app/utils/GBSessao.php');

use Adianti\Wrapper\BootstrapFormBuilderMod;

class GrupoBemForm extends TPage
{
    private $form;
    private static $database     = 'sample';
    private static $activeRecord = 'GrupoBem';
    private static $formName     = 'form_GrupoBemForm'; 
    private static $primaryKey   = 'id_grupobem';

    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Grupo Bem');
        $this->form->setClientValidation(true);

        $cd_grupobem = new TEntry('cd_grupobem');
        $nm_grupobem = new TEntry('nm_grupobem');
        $ds_grupobem = new TText('ds_grupobem');

        //Linhas e colunas
        $row1 = $this->form->addFields( [new TLabel('Código:', '#333', '14px', null, '100%'), $cd_grupobem],
                                        [new TLabel('Grupo Bem: (*)', '#FF0000', '14px', null, '100%'), $nm_grupobem]);
        $row1->layout = ['col-sm-4', 'col-sm-8'];
        
        $row2 = $this->form->addFields( [new TLabel('Descrição: (*)', '#FF0000', '14px', null, '100%'), $ds_grupobem]);
        $row2->layout = ['col-sm-12'];

        //Tamanho do campo
        $cd_grupobem->setSize('100%');
        $nm_grupobem->setSize('100%');
        $ds_grupobem->setSize('100%');

        //Validações
        $nm_grupobem->addValidation('Grupo Bem', new TRequiredValidator);
        $ds_grupobem->addValidation('Descrição', new TRequiredValidator);

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
            
            $object = new GrupoBem();  
            $object->fromArray( (array) $data);
            
            GBSessao::obterObjetoEdicaoSessao($object, self::$primaryKey, null, __CLASS__);

            $cd_grupobem = $param['cd_grupobem'] ?? null;
            
            GrupoBemService::validarSequenciaExistente($cd_grupobem, self::$primaryKey, self::$activeRecord, self::$database, __CLASS__);

            $object->store();

            $this->form->setData($object);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            
            TTransaction::close();

            GBSessao::removerObjetoEdicaoSessao(__CLASS__);

            AdiantiCoreApplication::loadPage('GrupoBemHeaderList', 'onShow');
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
                
                $object = new GrupoBem($key);

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

    public static function onClose()
    {
        AdiantiCoreApplication::loadPage('GrupoBemHeaderList', 'onShow');
    }
}
