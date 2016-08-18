<?php
namespace Cmis\Cmis;

use Cmis\Cmis\Interfaces as CMISInterfaces;
use Cmis\Cmis\Services as ProxyServices;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config as ServiceManagerConfig;

class Cmis implements ServiceLocatorInterface
{
    /**
     * @var ServiceLocatorInterface */
    protected $serviceLocator = null;
    protected $configServices = array();
    protected $configRepositories = array();


    protected $_objectService       = null;
    protected $_discoveryService    = null;
    protected $_multiFilingService  = null;
    protected $_navigationService   = null;
    protected $_repositoryService   = null;
    protected $_relationshipService = null;
    protected $_policyService       = null;

    public function __construct(ServiceLocatorInterface $serviceLocator, $config)
    {
        $this->serviceLocator = $serviceLocator;
        $this->configServices = $config['services'];
        if (!isset($config['repositories'])) {
            $this->configRepositories = array(
                'default' => array(
                    'type'    => 'rdb',
                    //'adapter' => 'db/kernel',
                    'adapter' => 'db',
                ),
            );
        } else {
            $this->configRepositories = $config['repositories'];
        }

    }

    public function has($name)
    {
        return $this->serviceLocator->has($name);
    }

    public function get($name, &$repositoryId = null)
    {
        if ($repositoryId === null) {
            return $this->serviceLocator->get($name);
        }
        if (!is_array($repositoryId)) {
            $repositoryId = $this->getAdapter($repositoryId);
        }
        return $this->getService($name, $repositoryId);
    }

    protected function getAdapter($repositoryId)
    {
        if (!isset($this->configRepositories[$repositoryId])) {
            throw new \Exception('repository not found');
        }
        $repositoryConfig = &$this->configRepositories[$repositoryId];
        switch($repositoryConfig['type']) {
            case 'rdb' :
                if (is_string($repositoryConfig['adapter'])) {
                    $repositoryConfig['adapter'] = $this->get($repositoryConfig['adapter']);
                }
                break;
            case 'http':
                throw new \Exception('cmis http adapter is not implemented');
            default :
                throw new \Exception('adapter type is not supported');
        }
        return array(
            'id'      => $repositoryId,
            'type'    => $repositoryConfig['type'],
            'adapter' => $repositoryConfig['adapter'],
        );
    }

    protected function getService($serviceName, $repositoryId)
    {
        $serviceType = $repositoryId['type'];
        if (!isset($this->configServices[$serviceType])) {
            throw new \Exception('service type "' . $serviceType . '" not found');
        }
        if (is_array($this->configServices[$serviceType])) {
            $config = new ServiceManagerConfig($this->configServices[$serviceType]);
            $this->configServices[$serviceType] = $serviceManager = new ServiceManager($config);

            $serviceManager->addInitializer(function ($instance) {
                //$this->serviceLocator->initialize($instance);
                if ($instance instanceof ServiceLocatorAwareInterface) {
                    $instance->setServiceLocator($this);
                }
                if ($instance instanceof ServiceManagerAwareInterface) {
                    $instance->setServiceManager($this);
                }
                /*if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                    $adapter = method_exists($instance, 'getDbAdapter')
                                ? $instance->getDbAdapter()
                                : '';
                    $instance->setDbAdapter($this->get('db/'.$adapter));
                }*/
                if ($instance instanceof \Zend\Db\Sql\SqlAwareInterface) {
                    $instance->setSql($this->get('sql'));
                }
            }, false);
            //$serviceManager->addPeeringServiceManager($this->serviceLocator);
        }
        return $this->configServices[$serviceType]->get($serviceName);
    }

    /**
     * @return CMISInterfaces\ObjectServiceInterface */
    public function objectService()
    {
        if (!$this->_objectService) {
            $this->_objectService = new ProxyServices\ObjectService($this);
        }
        return $this->_objectService;
    }

    /**
     * @return CMISInterfaces\DiscoveryServiceInterface */
    public function discoveryService()
    {
        if (!$this->_discoveryService) {
            $this->_discoveryService = new ProxyServices\DiscoveryService($this);
        }
        return $this->_discoveryService;
    }

    /**
     * @return CMISInterfaces\MultiFilingServiceInterface */
    public function multiFilingService()
    {
        if (!$this->_multiFilingService) {
            $this->_multiFilingService = new ProxyServices\MultiFilingService($this);
        }
        return $this->_multiFilingService;
    }

    /**
     * @return CMISInterfaces\NavigationServiceInterface */
    public function navigationService()
    {
        if (!$this->_navigationService) {
            $this->_navigationService = new ProxyServices\NavigationService($this);
        }
        return $this->_navigationService;
    }
    /**
     * @return CMISInterfaces\RepositoryServiceInterface */
    public function repositoryService()
    {
        if (!$this->_repositoryService) {
            $this->_repositoryService = new ProxyServices\RepositoryService($this);
        }
        return $this->_repositoryService;
    }

    /**
     * @return CMISInterfaces\RelationshipServiceInterface */
    public function relationshipService()
    {
        if (!$this->_relationshipService) {
            $this->_relationshipService = new ProxyServices\RelationshipService($this);
        }
        return $this->_relationshipService;
    }

    /**
     * @return CMISInterfaces\ACLServiceInterface */
    public function aclService()
    {
        throw new \Exception('not implemented');
    }

    /**
     * @return CMISInterfaces\PolicyServiceInterface */
    public function policyService()
    {
        if (!$this->_policyService) {
            $this->_policyService = new ProxyServices\PolicyService($this);
        }
        return $this->_policyService;
    }



    /**
     * @return CMISInterfaces\VersioningServiceInterface */
    public function versioningService()
    {
        throw new \Exception('not implemented');
    }
}
