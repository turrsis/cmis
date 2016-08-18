<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\MultiFilingServiceInterface;
use Zend\Db\Sql;

class MultiFilingService implements MultiFilingServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine) {
       $this->objectsEngine = $objectsEngine; 
    }

    public function addObjectToFolder($objectId, $folderId, $allVersions = true)
    {
        return $this->objectsEngine->addObjectToFolder($objectId, $folderId, $allVersions);
    }

    public function removeObjectFromFolder($objectId, $folderId = null)
    {
        return $this->objectsEngine->removeObjectFromFolder($objectId, $folderId);
    }
}
