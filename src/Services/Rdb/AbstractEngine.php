<?php
namespace Turrsis\Cmis\Services\Rdb;

use Zend\Db\Adapter;
use Zend\Db\Sql;

class AbstractEngine
{
/**
     * @var Adapter\Adapter */
    protected $adapter;
    
    public function __construct(Adapter\Adapter $adapter)
    {
        $this->adapter    = $adapter;
    }

    protected function executeSql($sql)
    {
        if (is_array($sql)) {
            foreach($sql as $query) {
                $this->executeSql($query);
            }
            return;
        }
        return $this->adapter->query($sql, Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    protected function executeSqlRow($sql)
    {
        return $this->adapter->queryRow($sql, Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
    protected function executeSqlScalar($sql)
    {
        return $this->adapter->queryScalar($sql, Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
}
