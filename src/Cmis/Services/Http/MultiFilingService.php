<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\MultiFilingServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class MultiFilingService extends AbstractService implements MultiFilingServiceInterface
{
    public function addObjectToFolder($objectId, $folderId, $allVersions = true)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function removeObjectFromFolder($objectId, $folderId = null)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }
}
