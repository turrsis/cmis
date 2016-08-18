<?php
namespace Cmis\Cmis\Services\Http;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected function getService($serviceName)
    {
        //return $this->getServiceLocator()->get($serviceName);
    }

}
