<?php
namespace TgsTest\Cmis\Cmis;

use Cmis\Cmis;
use Zend\Db\Adapter\Adapter;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Adapter */
    protected $adapter;

    /**
     * @var Services\Rdb\QueryBuilder */
    protected $queryBuilder;
    /**
     * @var Cmis\Repository */
    protected $repository;

    public function setUp()
    {   
        $this->adapter = new Adapter(array(
            'host'   => '',
            'user'   => '',
            'pass'   => '',
            'driver' => 'pdo_mysql',
            'dbname' => '',
        ));
        $this->queryBuilder     = new Cmis\Services\Rdb\QueryBuilder();
    }
    
    public function tearDown()
    {

    }
    
    public function testCreateRepositoryWithoutAdapter()
    {
        $this->setExpectedException('Exception', '"adapter" option is required');
        new Cmis\Repository(array());
    }

    public function testCreateRepositoryWithDefaults()
    {
        $repository = new Cmis\Repository(array(
            'adapter'       => $this->adapter,
            'query_builder' => $this->queryBuilder,
        ));
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\ACLService',
            $repository->getAclService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\DiscoveryService', 
            $repository->getDiscoveryService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\MultiFilingService', 
            $repository->getMultiFilingService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\NavigationService', 
            $repository->getNavigationService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\ObjectService', 
            $repository->getObjectService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\PolicyService', 
            $repository->getPolicyService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\RelationshipService', 
            $repository->getRelationshipService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\RepositoryService', 
            $repository->getRepositoryService()
        );
        
        $this->assertInstanceOf(
            'Cmis\Cmis\Services\Rdb\VersioningService', 
            $repository->getVersioningService()
        );
    }
    
    public function testCreateRepositoryWithOverride()
    {
        $repositoryConfig = array(
            'adapter'               => $this->adapter,
            'query_builder'         => $this->queryBuilder,
            'acl_service'           => 'Cmis\Cmis\Services\Http\ACLService',
            'discovery_service'     => 'Cmis\Cmis\Services\Http\DiscoveryService',
            'multi_filing_service'  => 'Cmis\Cmis\Services\Http\MultiFilingService',
            'navigation_service'    => 'Cmis\Cmis\Services\Http\NavigationService',
            'object_service'        => 'Cmis\Cmis\Services\Http\ObjectService',
            'policy_service'        => 'Cmis\Cmis\Services\Http\PolicyService',
            'relationship_service'  => 'Cmis\Cmis\Services\Http\RelationshipService',
            'repository_service'    => 'Cmis\Cmis\Services\Http\RepositoryService',
            'versioning_service'    => 'Cmis\Cmis\Services\Http\VersioningService',
        );
        
        $repository = new Cmis\Repository($repositoryConfig);

        $this->assertInstanceOf(
            $repositoryConfig['acl_service'],
            $repository->getAclService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['discovery_service'],
            $repository->getDiscoveryService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['multi_filing_service'],
            $repository->getMultiFilingService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['navigation_service'],
            $repository->getNavigationService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['object_service'],
            $repository->getObjectService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['policy_service'],
            $repository->getPolicyService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['relationship_service'],
            $repository->getRelationshipService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['repository_service'],
            $repository->getRepositoryService()
        );
        
        $this->assertInstanceOf(
            $repositoryConfig['versioning_service'],
            $repository->getVersioningService()
        );
    }
}