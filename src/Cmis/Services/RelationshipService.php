<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\RelationshipServiceInterface;

class RelationshipService implements RelationshipServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
        $this->objectsEngine = $objectsEngine; 
    }

    public function getObjectRelationships($objectId, $optional = array())
    {
        return $this->objectsEngine->getObjectRelationships($objectId, $optional);
    }

}
