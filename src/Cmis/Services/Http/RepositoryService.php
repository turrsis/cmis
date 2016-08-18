<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\RepositoryServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class RepositoryService extends AbstractService implements RepositoryServiceInterface
{
    public function createType($typeDeﬁnition)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function deleteType($typeId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getRepositories()
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getRepositoryInfo()
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getTypeChildren($typeId = null, $includePropertyDefinitions = false, $maxItems = null, $skipCount = null)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getTypeDefinition($typeId)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getTypeDescendants($typeId = null, $depth = null, $includePropertyDefinitions = false)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function updateType($typeDeﬁnition)
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }
}
