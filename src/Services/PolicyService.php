<?php
namespace Turrsis\Cmis\Services;

class PolicyService
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
        $this->objectsEngine = $objectsEngine; 
    }

    /**2.2.9.1 Applies a speciﬁed policy to an object.
     *
     * @param type $repositoryId
     * @param type $policyId
     * @param type $objectId
     */
    public function applyPolicy($policyId, $objectId)
    {
        
    }

    /**2.2.9.2 Removes a speciﬁed policy from an object.
     *
     * @param type $repositoryId
     * @param type $policyId
     * @param type $objectId
     */
    public function removePolicy($policyId, $objectId)
    {
        
    }

    /**2.2.9.3 Gets the list of policies currently applied to the speciﬁed object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $filter
     */
    public function getAppliedPolicies($objectId, $filter = null)
    {
       
    }
}
