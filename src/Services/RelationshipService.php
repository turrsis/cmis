<?php
namespace Turrsis\Cmis\Services;

class RelationshipService
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
        $this->objectsEngine = $objectsEngine; 
    }

    /**2.2.8.1 Gets all or a subset of relationships associated with an independent object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional ['includeSubRelationshipTypes', 'relationshipDirection', 'typeId', 'maxItems', 'skipCount', 'filter', 'includeAllowableActions']
     */
    public function getObjectRelationships($objectId, $optional = array())
    {
        return $this->objectsEngine->getObjectRelationships($objectId, $optional);
    }

}
