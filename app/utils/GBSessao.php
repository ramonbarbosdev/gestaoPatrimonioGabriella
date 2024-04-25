<?php

class GBSessao
{
    /*
    @author: Gabriella
    @created: 04/03/2024
    @summary: Insere na sessão o objeto em edição
    @$object: objeto em edição que será adicionado na sessão
    @$key: chave do registro
    @$primaryKey: nome da chave primária do objeto (null)
    @$class: classe que o objeto é chamado (null)
    */
    public static function incluirObjetoEdicaoSessao($object, $key, $primaryKey = null, $class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
            $objectCopy = clone $object;
            $objectCopy->keyValue = $key;
            if($primaryKey != null)
                $objectCopy->__set($primaryKey, $key);
            TSession::setValue($class.'.objectEdit',$objectCopy);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /*
    @author: Gabriella
    @created: 04/03/2024
    @summary: Obtem da sessão o objeto colocado em edição
    @$object: objeto em edição que será adicionado na sessão
    @$primaryKey: nome da chave primária do objeto (null)
    @$readOnlyFields: array de campos que não devem ser editados na tela (null)
    @$class: classe que o objeto é chamado (null)
    */
    public static function obterObjetoEdicaoSessao(Object $object = null, $primaryKey = null, $readOnlyFields = null, $class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
            
            $objectEdit = TSession::getValue($class.'.objectEdit');
            if($primaryKey != null)
            {
                if($objectEdit != NULL)
                {
                    $object->__set($primaryKey, $objectEdit->keyValue);
                    if($readOnlyFields != null && is_array($readOnlyFields))
                    {
                        foreach ($readOnlyFields as $field)
                        {
                            $object->__set($field, $objectEdit->__get($field));
                        }
                    }
                }
                else
                {
                    $object->__set($primaryKey, NULL);
                }
            }
            else
            {
                return $objectEdit;
            }
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /*
    @author: Gabriella
    @created: 04/03/2024
    @summary: Remove o objeto da sessão
    @$class: classe que o objeto é chamado (null)
    */
    public static function removerObjetoEdicaoSessao($class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
           TSession::delValue($class.'.objectEdit');
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}