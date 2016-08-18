<?php
namespace Cmis\Cmis\Services;

use Cmis\Cmis\Interfaces\RepositoryServiceInterface;

class RepositoryService implements RepositoryServiceInterface
{

    protected $typesCache = array();
    
    /**
     * @var Rdb\RepositoryEngine */
    protected $repositoryEngine;

    public function __construct($repositoryEngine) {
       $this->repositoryEngine = $repositoryEngine; 
    }

    public function createType($typeDeﬁnition)
    {
        return $this->repositoryEngine->createType($typeDeﬁnition);
    }

    public function deleteType($typeId)
    {
        return $this->repositoryEngine->deleteType($typeId);
    }

    public function getRepositories()
    {
        throw new \Exception('not implemented');
    }

    public function getRepositoryInfo()
    {
        return $this->repositoryEngine->getRepositoryInfo();
    }

    public function getTypeChildren($typeId = null, $includePropertyDefinitions = false, $maxItems = null, $skipCount = null)
    {
        return $this->repositoryEngine->getTypeChildren($typeId, $includePropertyDefinitions, $maxItems, $skipCount);
    }

    public function getTypeDefinition($typeId)
    {
        return $this->repositoryEngine->getTypeDefinition($typeId);
    }

    public function getTypeDescendants($typeId = null, $depth = null, $includePropertyDefinitions = false)
    {
        return $this->repositoryEngine->getTypeDescendants($typeId, $depth, $includePropertyDefinitions);
    }

    public function updateType($typeDeﬁnition)
    {
        return $this->repositoryEngine->updateType($typeDeﬁnition);
    }
}
