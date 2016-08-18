<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\RelationshipServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class RelationshipService extends AbstractService implements RelationshipServiceInterface
{
    public function getObjectRelationships($objectId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

}
