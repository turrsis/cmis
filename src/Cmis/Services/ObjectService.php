<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\ObjectServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class ObjectService implements ObjectServiceInterface
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

    public function createDocument($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:document';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

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

    public function createFolder($properties, $folderId, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:folder';
        }
        $optional['folderId']            = $folderId;
        return $this->objectsEngine->createObject($properties, $optional);
    }

    public function createItem($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:item';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

    public function createPolicy($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:policy';
        }
        return $this->objectsEngine->createObject($properties, $optional);
    }

    public function createRelationship($properties, $optional = array())
    {
        if (!isset($properties['cmis:objectTypeId'])) {
            $properties['cmis:objectTypeId'] = 'cmis:relationship';
        }
        $properties['cmis:sourceTypeId'] = $this->objectsEngine->_getTypeDefinitionByObject($properties['cmis:sourceId'])['id'];
        $properties['cmis:targetTypeId'] = $this->objectsEngine->_getTypeDefinitionByObject($properties['cmis:targetId'])['id'];
        return $this->objectsEngine->createObject($properties, $optional);
    }

    public function updateProperties($objectId, $properties, $changeToken = null)
    {
        $properties['cmis:objectId'] = $objectId;
        return $this->objectsEngine->updateProperties($properties, $changeToken);
    }

    public function getProperties($objectId, $filter = array(), $optional = array())
    {
        return $this->objectsEngine->getProperties($objectId, $filter, $optional);
    }

    public function getObject($objectId, $optional = array())
    {
        return $this->objectsEngine->getObject($objectId, $optional);
    }

    public function getObjectByPath($path, $optional = array())
    {
        return $this->objectsEngine->getObjectByPath($path, $optional);
    }

    public function deleteObject($objectId, $allVersions = true)
    {
        return $this->objectsEngine->deleteObject($objectId, $allVersions);
    }

    public function deleteTree($folderId, $optional = array())
    {
        return $this->objectsEngine->deleteTree($folderId, $optional);
    }

    public function moveObject($objectId, $targetFolderId, $sourceFolderId)
    {
        return $this->objectsEngine->moveObject($objectId, $targetFolderId, $sourceFolderId);
    }

    public function getContentStream($objectId, $streamId = null)
    {
    }

    public function setContentStream($objectId, $contentStream, $optional = array())
    {
    }

    public function appendContentStream($objectId, $contentStream, $optional = array())
    {
    }

    public function deleteContentStream($objectId, $changeToken = null)
    {
    }

    public function bulkUpdateProperties($objectIdAndChangeToken, $optional = array())
    {
    }
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

    public function getRenditions($objectId, $optional = array())
    {
    }
}
