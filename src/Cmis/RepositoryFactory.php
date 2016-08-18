<?php
namespace Cmis\Cmis;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

class RepositoryFactory implements AbstractFactoryInterface
{
    protected $config = null;
    protected $repoPrefix = 'cmis:repo:';

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (stripos($name, $this->repoPrefix) !== 0) {
            return false;
        }
        $config = $this->getConfig($serviceLocator);
        
        $repoName = substr($name, strlen($this->repoPrefix));
        return isset($config[$repoName]);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $repoName = substr($name, strlen($this->repoPrefix));
        $config = $this->getConfig($serviceLocator)[$repoName];

        if (isset($config['adapter'])) {
            $config['adapter']       = $serviceLocator->get($config['adapter']);
        }

        return new Repository($config);
    }

    protected function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        if ($this->config === null) {
            $config = $serviceLocator->get('Config');
            if(!isset($config['cmis_repository'])) {
                throw new \Exception('"cmis_repository" section not found in config');
            }
            $this->config = $config['cmis_repository'];
        }
        return $this->config;
    }
}
