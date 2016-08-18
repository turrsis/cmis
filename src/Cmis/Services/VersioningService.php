<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\VersioningServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class VersioningService implements VersioningServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
       $this->objectsEngine = $objectsEngine;
    }

    public function cancelCheckOut($objectId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function checkIn($objectId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function checkOut($objectId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getAllVersions($versionSeriesId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getObjectOfLatestVersion($versionSeriesId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getPropertiesOfLatestVersion($versionSeriesId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

}
