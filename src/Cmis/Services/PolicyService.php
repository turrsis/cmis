<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\PolicyServiceInterface;

class PolicyService implements PolicyServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
        $this->objectsEngine = $objectsEngine; 
    }

    public function applyPolicy($repositoryId, $policyId, $objectId)
    {
        
    }

    public function removePolicy($repositoryId, $policyId, $objectId)
    {
        
    }

    public function getAppliedPolicies($repositoryId, $objectId, $filter = null)
    {
       
    }
}
