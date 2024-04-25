<?php

class GBSequencia
{
    public static function gerarSequencia($campoOrdem, $database, $activeRecord, $filtro = null, $codigo = null)
    {
        $criteria = new TCriteria();
        $param = [];
        $param['order'] = $campoOrdem;
        $param['direction'] = 'desc';
        
        if(isset($filtro) && $filtro != null && $filters = $filtro)
        {
            foreach ($filters as $filter)
            {
                $criteria->add($filter);    
            }
        }
        
        $criteria->setProperties($param); // order, offset
        $criteria->setProperty('limit', 1);

        $repository = new TRepository($activeRecord);
        TTransaction::open($database);
        $resultado = $repository->load($criteria, FALSE);
        TTransaction::close();
        
        $sequencia = 1;

        if(isset($resultado) && count($resultado) > 0)
        {
            $sequencia = $resultado[0]->__get($campoOrdem) + 1;
        }

        return $sequencia;
    }

    public static function validarSequenciaExistente($activeRecord, $database, $primaryKey = null, $filtro = null, $class = null)
    {
        if($class == null)
        {
            $class = __CLASS__;
        }
        
        // validar a sequencia 
        $criteria = new TCriteria();

        if(isset($filtro) && $filtro != null && $filters = $filtro)
        {
            foreach ($filters as $filter)
            {
                $criteria->add($filter);    
            }
        }
        
        if($primaryKey != null)
        {
            $objectEdicao = GBSessao::obterObjetoEdicaoSessao(null, null, null, $class);
            
            if($objectEdicao)
            {
                $key = $objectEdicao->__get($primaryKey);
                $criteria->add(new TFilter($primaryKey, '!=', $key));
            }
        }

        $repository = new TRepository($activeRecord);
        TTransaction::open($database);
        $resultado = $repository->load($criteria, FALSE);
        TTransaction::close(); 

        if($resultado)
        {
            return true;
        }
    }
}