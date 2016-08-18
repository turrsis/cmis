<?php
namespace Cmis\Cmis\Services\Http;

use Cmis\Cmis\Interfaces\DiscoveryServiceInterface;
use Cmis\Cmis\Exception as CmisExceptions;

class DiscoveryService extends AbstractService implements DiscoveryServiceInterface
{
    public function query($statement, $optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }

    public function getContentChanges($optional = array())
    {
        throw new CmisExceptions\NotSupported(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__ . ' is not implemented');
    }
}
