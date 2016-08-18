<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\VersioningServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class VersioningService extends AbstractService implements VersioningServiceInterface
{

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
