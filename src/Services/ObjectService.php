<?php
namespace Turrsis\Cmis\Services;

class ObjectService
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
       $this->objectsEngine = $objectsEngine;
    }
    /**
     * Warning!!! - this is not CMIS specification function
     * @param type $object
     * @param array $optional
     * @return null
     */
    public function createObject($object, array $optional = array())
    {
        return $this->objectsEngine->createObject($object, $optional);
    }

    /**2.2.4.1 Creates a document object of the speciﬁed type (given by the cmis:objectTypeId property) in the (optionally) speciﬁed location.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional ['folderId', 'contentStream', 'versioningState', 'policies', 'addACEs', 'removeACEs']
     */
    public function createDocument($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:document';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

    /**2.2.4.2 Creates a document object as a copy of the given source document in the (optionally) speciﬁed location.
     *
     * @param type $repositoryId
     * @param type $sourceId
     * @param type $optional ['folderId', 'properties', 'versioningState', 'policies', 'addACEs', 'removeACEs']
     */
    public function createDocumentFromSource($sourceId, $optional = array())
    {
        $sourceDocument = $this->getObject($sourceId);
        if (isset($optional['properties'])) {
            foreach($optional['properties'] as $k=>$v) {
                $sourceDocument['properties'][$k] = $v;
            }
            unset($optional['properties']);
        }
        unset($sourceDocument['properties']['objectId']);
        return $this->createDocument($sourceDocument['properties'], $optional);
    }

    /**2.2.4.3 Creates a folder object of the speciﬁed type in the speciﬁed location.
     *
     * @param type  $repositoryId  The identiﬁer for the repository.
     * @param array $properties    The property values that MUST be applied to the newly-created folder object.
     * @param type  $folderId      The identiﬁer for the folder that MUST be the parent folder for the newly-created folder object.
     * @param array $optional
     *                  <Array> Id policies: A list of policy ids that MUST be applied to the newly-created folder object.
     *                  <Array> ACE addACEs: A list of ACEs that MUST be added to the newly-created folder object, either using the ACL from folderId if speciﬁed, or being applied if no folderId is speciﬁed.
     *                  <Array> ACE removeACEs: A list of ACEs that MUST be removed from the newly-created folder object, either using the ACL from folderId if speciﬁed, or being ignored if no folderId is speciﬁed.
     */
    public function createFolder($properties, $folderId, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:folder';
        }
        $optional['folderId']            = $folderId;
        return $this->objectsEngine->createObject($properties, $optional);
    }

    /**2.2.4.6 Creates an item object of the speciﬁed type.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional ['folderId', 'policies', 'addACEs', 'removeACEs']
     */
    public function createItem($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:item';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

    /**2.2.4.5 Creates a policy object of the speciﬁed type.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional ['folderId', 'policies', 'addACEs', 'removeACEs']
     */
    public function createPolicy($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:policy';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

    /**2.2.4.4 Creates a relationship object of the speciﬁed type.
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional ['policies', 'addACEs', 'removeACEs']
     */
    public function createRelationship($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:relationship';
        }
        $properties['cmis:sourceTypeId'] = $this->objectsEngine->_getTypeDefinitionByObject($properties['cmis:sourceId'])['id'];
        $properties['cmis:targetTypeId'] = $this->objectsEngine->_getTypeDefinitionByObject($properties['cmis:targetId'])['id'];
        return $this->objectsEngine->createObject($properties, $optional);
    }

    /**2.2.4.13 Updates properties and secondary types of the speciﬁed object.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of an update properties operation. Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - Only properties whose values are diﬀerent than the original value of the object SHOULD be provided.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $properties
     * @param type $changeToken
     */
    public function updateProperties($objectId, $properties, $changeToken = null)
    {
        $properties['cmis:objectId'] = $objectId;
        return $this->objectsEngine->updateProperties($properties, $changeToken);
    }

    /**2.2.4.9 Gets the list of properties for the object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $filter
     */
    public function getProperties($objectId, $filter = array(), $optional = array())
    {
        return $this->objectsEngine->getProperties($objectId, $filter, $optional);
    }

    /**2.2.4.8 Gets the speciﬁed information for the object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional ['filter', 'includeRelationships', 'includePolicyIds', 'renditionFilter', 'includeACL', 'includeAllowableActions']
     */
    public function getObject($objectId, $optional = array())
    {
        return $this->objectsEngine->getObject($objectId, $optional);
    }

    /**2.2.4.10 Gets the speciﬁed information for the object.
     *
     * @param type $repositoryId
     * @param type $path
     * @param type $optional ['filter', 'includeRelationships', 'includePolicyIds', 'renditionFilter', 'includeACL', 'includeAllowableActions']
     */
    public function getObjectByPath($path, $optional = array())
    {
        return $this->objectsEngine->getObjectByPath($path, $optional);
    }

    /**2.2.4.16 Deletes the speciﬁed object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param bool $allVersions
     */
    public function deleteObject($objectId, $allVersions = true)
    {
        return $this->objectsEngine->deleteObject($objectId, $allVersions);
    }

    /**2.2.4.17 Deletes the speciﬁed folder object and all of its child- and descendant-objects.
     * Notes:
     *      - A repository MAY attempt to delete child- and descendant-objects of the speciﬁed folder in any order.
     *      - Any child- or descendant-object that the repository cannot delete MUST persist in a valid state in the CMIS domain model.
     *      - This service is not atomic.
     *      - However, if deletesinglefiled is chosen and some objects fail to delete, then single-ﬁled objects are either deleted or kept,
     *        never just unﬁled. This is so that a user can call this command again to recover from the error by using the same tree.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional ['allVersions', 'unfileObjects', 'continueOnFailure']
     */
    public function deleteTree($folderId, $optional = array())
    {
        return $this->objectsEngine->deleteTree($folderId, $optional);
    }

    /**2.2.4.15 Moves the specified file-able object from one folder to another.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $targetFolderId
     * @param type $sourceFolderId
     */
    public function moveObject($objectId, $targetFolderId, $sourceFolderId)
    {
        return $this->objectsEngine->moveObject($objectId, $targetFolderId, $sourceFolderId);
    }

    /**2.2.4.11 Gets the content stream for the speciﬁed document object, or gets a rendition stream for a speciﬁed rendition of a document or folder object.
     * Notes: Each CMIS protocol binding MAY provide a way for fetching a sub-range within a content stream, in a manner appropriate to that protocol.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $streamId
     */
    public function getContentStream($objectId, $streamId = null)
    {
    }

    /**2.2.4.18 Sets the content stream for the speciﬁed document object.
     * Notes: A repository MAY automatically create new document versions as part of this service operations.
     *        Therefore, the objectId output NEED NOT be identical to the objectId input.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $contentStream
     * @param type $optional ['overwriteFlag', 'changeToken']
     */
    public function setContentStream($objectId, $contentStream, $optional = array())
    {
    }

    /**2.2.4.19 Appends to the content stream for the speciﬁed document object.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of this service method.
     *        Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - The document may or may not have a content stream prior to calling this service.
     *        If there is no content stream, this service has the eﬀect of setting the content stream with the value of the input contentStream.
     *      - This service is intended to be used by a single client. It should support the upload of very huge content streams.
     *        The behavior is repository speciﬁc if multiple clients call this service in succession or in parallel for the same document.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $contentStream
     * @param type $optional ['isLastChunk', 'changeToken']
     */
    public function appendContentStream($objectId, $contentStream, $optional = array())
    {
    }

    /**2.2.4.20 Deletes the content stream for the speciﬁed document object.
     * Notes: A repository MAY automatically create new document versions as part of this service method. Therefore, the obejctId output NEED NOT be identical to the objectId input.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $changeToken
     */
    public function deleteContentStream($objectId, $changeToken = null)
    {
    }

    /**2.2.4.14 Updates properties and secondary types of one or more objects.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of an update properties operation. Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - Only properties whose values are diﬀerent than the original value of the object SHOULD be provided.
     *      - This service is not atomic. If the update fails, some objects might have been updated and others might not have been updated.
     *      - This service MUST NOT throw an exception if the update of an object fails. If an update fails, the object id of this particular object MUST be omitted from the result.
     * @param type $repositoryId
     * @param type $objectIdAndChangeToken
     * @param type $optional ['properties', 'addSecondaryTypeIds', 'removeSecondaryTypeIds']
     */
    public function bulkUpdateProperties($objectIdAndChangeToken, $optional = array())
    {
    }
    /**2.2.4.7 Gets the list of allowable actions for an object (see section 2.2.1.2.6 Allowable Actions).
     *
     * @param type $repositoryId
     * @param type $objectId
     */

    public function getAllowableActions($objectId)
    {
        return array(
            'canDeleteObject'           => true,
            'canMoveObject'             => true,

            'canUpdateProperties'       => true,

            'canGetProperties'          => true,
            'canGetObjectRelationships' => false,
            'canGetObjectParents'       => true,

            'canCheckOut'               => false,
            'canCancelCheckOut'         => false,
            'canCheckIn'                => false,

            'canGetAllVersions'         => false,

            'canApplyPolicy'            => false,
            'canGetAppliedPolicies'     => false,
            'canRemovePolicy'           => false,
            'canGetChildren'            => false,

            'canCreateDocument'         => false,
            'canCreateFolder'           => false,
            'canCreateRelationship'     => false,

            'canGetFolderTree'          => false,
            'canGetFolderParent'        => false,
            'canGetDescendants'         => false,
            'canDeleteTree'             => false,
            'canAddObjectToFolder'      => true,
            'canRemoveObjectFromFolder' => true,

            'canSetContentStream'       => true,
            'canGetContentStream'       => true,
            'canDeleteContentStream'    => true,

            'canGetRenditions'          => false,
            'canGetACL'                 => false,
            'canApplyACL'               => false,
        );
    }

    /**2.2.4.12 Gets the list of associated renditions for the speciﬁed object. Only rendition attributes are returned, not rendition stream.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function getRenditions($objectId, $optional = array())
    {
    }
}
