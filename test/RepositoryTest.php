<?php
namespace TurrsisTest\Cmis;

use Turrsis\Cmis;

class RepositoryTest extends AbstractTest
{
    public function testCreateRepository()
    {
        $sm = $this->getServiceManager();
        $repo = $sm->get('cmis:repo:repo1');
        $this->assertInstanceOf(Cmis\Repository::class, $repo);
        
        $this->assertInstanceOf('Turrsis\Cmis\Services\ObjectService',       $repo->getObjectService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\DiscoveryService',    $repo->getDiscoveryService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\MultiFilingService',  $repo->getMultiFilingService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\NavigationService',   $repo->getNavigationService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\RepositoryService',   $repo->getRepositoryService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\RelationshipService', $repo->getRelationshipService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\PolicyService',       $repo->getPolicyService());
        $this->assertInstanceOf('Turrsis\Cmis\Services\VersioningService',   $repo->getVersioningService());
        //$this->assertInstanceOf('Turrsis\Cmis\Services\AclService',          $repo->getAclService());
    }
}