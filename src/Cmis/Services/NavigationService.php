<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\NavigationServiceInterface;

class NavigationService implements NavigationServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
       $this->objectsEngine = $objectsEngine;
    }

    public function getCheckedOutDocs($optional = array())
    {
        return $this->objectsEngine->getCheckedOutDocs($optional);
    }

    public function getChildren($folderId, $optional = array())
    {
        return $this->objectsEngine->getChildren($folderId, $optional);
    }

    public function getDescendants($folderId, $optional = array())
    {
        return $this->objectsEngine->getDescendants($folderId, $optional);
    }

    public function getFolderParent($folderId, $filter = null)
    {
        return $this->objectsEngine->getFolderParent($folderId, $filter);
    }

    public function getFolderTree($folderId, $optional = array())
    {
        return $this->objectsEngine->getFolderTree($folderId, $optional);
    }

    public function getObjectParents($objectId, $optional = array())
    {
        return $this->objectsEngine->getObjectParents($objectId, $optional);
    }
}
