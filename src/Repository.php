<?php
namespace Turrsis\Cmis;

class Repository
{
    //protected $repositoryEngine = Services\Rdb\RepositoryEngine::class;
    //protected $sequrityEngine   = Services\Rdb\SequrityEngine::class;
    //protected $objectsEngine    = Services\Rdb\ObjectsEngine::class;
            
    protected $_objectService;
    protected $_discoveryService;
    protected $_multiFilingService;
    protected $_navigationService;
    protected $_repositoryService;
    protected $_relationshipService;
    protected $_policyService;
    protected $_aclService;
    protected $_versioningService;
    
    public function __construct($objectsEngine, $sequrityEngine, $repositoryEngine)
    {
        $this->_repositoryService   = new Services\RepositoryService   ($repositoryEngine);
        $this->_objectService       = new Services\ObjectService       ($objectsEngine);
        $this->_relationshipService = new Services\RelationshipService ($objectsEngine);
        $this->_navigationService   = new Services\NavigationService   ($objectsEngine);
        $this->_discoveryService    = new Services\DiscoveryService    ($objectsEngine);
        $this->_multiFilingService  = new Services\MultiFilingService  ($objectsEngine);
        $this->_policyService       = new Services\PolicyService       ($objectsEngine);
        $this->_versioningService   = new Services\VersioningService   ($objectsEngine);
    }
 
    /**
     * @return Services\ObjectService */
    public function getObjectService()
    {
        return $this->_objectService;
    }

    /**
     * @return Services\DiscoveryService */
    public function getDiscoveryService()
    {
        return $this->_discoveryService;
    }

    /**
     * @return Services\MultiFilingService */
    public function getMultiFilingService()
    {
        return $this->_multiFilingService;
    }

    /**
     * @return Services\NavigationService */
    public function getNavigationService()
    {
        return $this->_navigationService;
    }
    /**
     * @return Services\RepositoryService */
    public function getRepositoryService()
    {
        return $this->_repositoryService;
    }

    /**
     * @return Services\RelationshipService */
    public function getRelationshipService()
    {
        return $this->_relationshipService;
    }

    /**
     * @return Services\PolicyService */
    public function getPolicyService()
    {
        return $this->_policyService;
    }

    /**
     * @return Services\ACLService */
    public function getAclService()
    {
        return $this->_aclService;
    }

    /**
     * @return Services\VersioningService */
    public function getVersioningService()
    {
        return $this->_versioningService;
    }
}
