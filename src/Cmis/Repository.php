<?php
namespace Cmis\Cmis;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Repository implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $repositoryEngine = 'Cmis\Cmis\Services\Rdb\RepositoryEngine';
    protected $sequrityEngine   = 'Cmis\Cmis\Services\Rdb\SequrityEngine';
    protected $objectsEngine    = 'Cmis\Cmis\Services\Rdb\ObjectsEngine';
            
    protected $_objectService;
    protected $_discoveryService;
    protected $_multiFilingService;
    protected $_navigationService;
    protected $_repositoryService;
    protected $_relationshipService;
    protected $_policyService;
    protected $_aclService;
    protected $_versioningService;
    
    public function __construct(array $options)
    {
        $adapter = $options['adapter'];
        $sqlBuilder = $options['sql_builder'];
        
        if (isset($options['repository_engine'])) {
            $this->repositoryEngine = $options['repository_engine'];
        }
        if (is_string($this->repositoryEngine)) {
            $this->repositoryEngine = new $this->repositoryEngine($adapter, $sqlBuilder, $options);
        }
        
        if (isset($options['sequrity_engine'])) {
            $this->sequrityEngine = $options['sequrity_engine'];
        }
        if (is_string($this->sequrityEngine)) {
            $this->sequrityEngine = new $this->sequrityEngine($adapter, $sqlBuilder, $options);
        }
        
        if (isset($options['objects_engine'])) {
            $this->objectsEngine = $options['objects_engine'];
        }
        if (is_string($this->objectsEngine)) {
            $this->objectsEngine = new $this->objectsEngine($adapter, $sqlBuilder, $options);
            $this->objectsEngine->setRepositoryEngine($this->repositoryEngine);
            $this->objectsEngine->setSequrityEngine($this->sequrityEngine);
        }

        $this->_repositoryService   = new Services\RepositoryService   ($this->repositoryEngine);

        $this->_objectService       = new Services\ObjectService       ($this->objectsEngine);
        $this->_relationshipService = new Services\RelationshipService ($this->objectsEngine);
        $this->_navigationService   = new Services\NavigationService   ($this->objectsEngine);
        $this->_discoveryService    = new Services\DiscoveryService    ($this->objectsEngine);
        $this->_multiFilingService  = new Services\MultiFilingService  ($this->objectsEngine);
    }
 
    /**
     * @return Interfaces\ObjectServiceInterface */
    public function getObjectService()
    {
        return $this->_objectService;
    }

    /**
     * @return Interfaces\DiscoveryServiceInterface */
    public function getDiscoveryService()
    {
        return $this->_discoveryService;
    }

    /**
     * @return Interfaces\MultiFilingServiceInterface */
    public function getMultiFilingService()
    {
        return $this->_multiFilingService;
    }

    /**
     * @return Interfaces\NavigationServiceInterface */
    public function getNavigationService()
    {
        return $this->_navigationService;
    }
    /**
     * @return Interfaces\RepositoryServiceInterface */
    public function getRepositoryService()
    {
        return $this->_repositoryService;
    }

    /**
     * @return Interfaces\RelationshipServiceInterface */
    public function getRelationshipService()
    {
        return $this->_relationshipService;
    }

    /**
     * @return Interfaces\PolicyServiceInterface */
    public function getPolicyService()
    {
        return $this->_policyService;
    }

    /**
     * @return Interfaces\ACLServiceInterface */
    public function getAclService()
    {
        return $this->_aclService;
    }

    /**
     * @return Interfaces\VersioningServiceInterface */
    public function getVersioningService()
    {
        return $this->_versioningService;
    }
}
