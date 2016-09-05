<?php
namespace Turrsis\Cmis\Services;

class MultiFilingService
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine) {
       $this->objectsEngine = $objectsEngine; 
    }

    /**2.2.5.1 Adds an existing fileable non-folder object to a folder.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $folderId
     * @param type $allVersions
     */
    public function addObjectToFolder($objectId, $folderId, $allVersions = true)
    {
        return $this->objectsEngine->addObjectToFolder($objectId, $folderId, $allVersions);
    }

    /**2.2.5.2 Removes an existing fileable non-folder object from a folder.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $folderId
     */
    public function removeObjectFromFolder($objectId, $folderId = null)
    {
        return $this->objectsEngine->removeObjectFromFolder($objectId, $folderId);
    }
}
