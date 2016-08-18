<?php
namespace Cmis\Cmis\Interfaces;
/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 * 2.2.8 Relationship Services
 */
interface RelationshipServiceInterface
{
    /**2.2.8.1 Gets all or a subset of relationships associated with an independent object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function getObjectRelationships($objectId, $optional = [
        'includeSubRelationshipTypes',
        'relationshipDirection',
        'typeId',
        'maxItems',
        'skipCount',
        'filter',
        'includeAllowableActions'
    ]);


}
