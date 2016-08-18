<?php
namespace Cmis\Cmis\Services\Rdb;

use Cmis\Cmis\Repository;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;
use Cmis\Cmis\Exception as CmisExceptions;

class SequrityEngine extends AbstractEngine
{
    
    public function __construct(Adapter $adapter, Sql\Builder\Builder $sqlBuilder, array $config)
    {
        parent::__construct($adapter, $sqlBuilder);
    }

    public function applyACL($objectId, $optional = array())
    {
        
    }

    public function getACL($objectId, $onlyBasicPermissions = true)
    {
        
    }
    
    public function applyPolicy($policyId, $objectId)
    {

    }

    public function removePolicy($policyId, $objectId)
    {

    }

    public function getAppliedPolicies($objectId, $filter = null)
    {
        return array(36);
    }
}
