<?php
namespace Cmis\Cmis\Services\Rdb;

use Zend\Db\Adapter;
use Zend\Db\Sql;

class AbstractEngine
{
/**
     * @var Adapter\Adapter */
    protected $adapter;

    /** @var Sql\Builder\Builder */ 
    protected $sqlBuilder;
    
    public function __construct(Adapter\Adapter $adapter, Sql\Builder\Builder $sqlBuilder)
    {
        $this->adapter    = $adapter;
        $this->sqlBuilder = $sqlBuilder;
    }
    
    protected function executeSql($sql)
    {
        if (is_array($sql)) {
            foreach($sql as $query) {
                $this->executeSql($query);
            }
            return;
        }
        return $this->adapter->query(
            $this->sqlBuilder->buildSqlString($sql, $this->adapter),
            Adapter\Adapter::QUERY_MODE_EXECUTE
        );
    }
    
    protected function executeSqlRow($sql)
    {
        return $this->adapter->queryRow(
            $this->sqlBuilder->buildSqlString($sql, $this->adapter),
            Adapter\Adapter::QUERY_MODE_EXECUTE
        );
    }
    protected function executeSqlScalar($sql)
    {
        return $this->adapter->queryScalar(
            $this->sqlBuilder->buildSqlString($sql, $this->adapter),
            Adapter\Adapter::QUERY_MODE_EXECUTE
        );
    }
}
