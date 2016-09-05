<?php
namespace Turrsis\Cmis;

use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Interop\Container\ContainerInterface;

class RepositoryAbstractFactory implements AbstractFactoryInterface
{
    protected $config = null;

    protected $prefix = 'cmis:repo:';

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (stripos($requestedName, $this->prefix) !== 0) {
            return false;
        }
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        $repositoryName = substr($requestedName, strlen($this->prefix));
        return (
            isset($config[$repositoryName])
            && is_array($config[$repositoryName])
            && ! empty($config[$repositoryName])
        );
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositoryName = substr($requestedName, strlen($this->prefix));
        $config = $this->config[$repositoryName];

        $adapter    = null;
        if (isset($config['adapter'])) {
            $adapter    = $container->get($config['adapter']);
        }
        $options = [];
        $repositoryEngine = new $config['repositoryEngine']($adapter, $options);
        $sequrityEngine   = new $config['sequrityEngine']  ($adapter, $options);
        $objectsEngine    = new $config['objectsEngine']   ($adapter, $options);
        $objectsEngine->setRepositoryEngine($repositoryEngine);
        $objectsEngine->setSequrityEngine($sequrityEngine);

        return new Repository($objectsEngine, $sequrityEngine, $repositoryEngine);
    }
    
    protected function getConfig(ContainerInterface $container)
    {
        if ($this->config === null) {
            $config = $container->get('Config');
            if(!isset($config['cmis_repositories'])) {
                $this->config = [];
            } else {
                $this->config = $config['cmis_repositories'];
            }
        }
        return $this->config;
    }
}
