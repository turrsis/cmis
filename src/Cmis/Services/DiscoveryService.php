<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\DiscoveryServiceInterface;

class DiscoveryService implements DiscoveryServiceInterface
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
        $this->objectsEngine = $objectsEngine; 
    }

    public function query($statement, $optional = array())
    {
        return $this->objectsEngine->query($statement, $optional);
    }

    public function getContentChanges($optional = array())
    {
    }    
}
