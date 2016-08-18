<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\NavigationServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class NavigationService extends AbstractService implements NavigationServiceInterface
{
    public function getCheckedOutDocs($optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getChildren($folderId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getDescendants($folderId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getFolderParent($folderId, $filter = null)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getFolderTree($folderId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getObjectParents($objectId, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }
}
