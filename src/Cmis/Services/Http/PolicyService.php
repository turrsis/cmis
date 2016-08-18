<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\PolicyServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class PolicyService extends AbstractService implements PolicyServiceInterface
{
    public function applyPolicy($policyId, $objectId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getAppliedPolicies($objectId, $filter = null)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function removePolicy($policyId, $objectId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

}
