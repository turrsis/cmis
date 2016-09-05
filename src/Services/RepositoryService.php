<?php
namespace Turrsis\Cmis\Services;

class RepositoryService
{
    /**
     * @var Rdb\RepositoryEngine */
    protected $repositoryEngine;

    public function __construct($repositoryEngine) {
       $this->repositoryEngine = $repositoryEngine; 
    }

    /**2.2.2.6 Creates a new type deﬁnition that is a subtype of an existing speciﬁed parent type.
     * @param string $repositoryId
     * @param array $typeDeﬁnition
     */
    public function createType($typeDeﬁnition)
    {
        return $this->repositoryEngine->createType($typeDeﬁnition);
    }

    /**2.2.2.8 Deletes a type deﬁnition.
     *
     * @param string $repositoryId
     * @param string $typeId
     */
    public function deleteType($typeId)
    {
        return $this->repositoryEngine->deleteType($typeId);
    }

    /**2.2.2.1 Returns a list of CMIS repositories available from this CMIS service endpoint.
     * @return array */
    public function getRepositories()
    {
        throw new \Exception('not implemented');
    }

    /**2.2.2.2 Returns information about the CMIS repository, the optional capabilities
     * it supports and its access control information if applicable.
     * @param string $repositoryId
     * @return object RepositoryInfo */
    public function getRepositoryInfo()
    {
        return $this->repositoryEngine->getRepositoryInfo();
    }

    /**2.2.2.3 Returns the list of object-types deﬁned for the repository that are children of the speciﬁed type.
     *
     * @param string  $repositoryId
     * @param string  $typeId
     * @param bool    $includePropertyDefinitions
     * @param integer $maxItems
     * @param integer $skipCount
     * @return array */
    public function getTypeChildren($typeId = null, $includePropertyDefinitions = false, $maxItems = null, $skipCount = null)
    {
        return $this->repositoryEngine->getTypeChildren($typeId, $includePropertyDefinitions, $maxItems, $skipCount);
    }

    /**2.2.2.5 Gets the deﬁnition of the speciﬁed object-type.
     *
     * @param string $repositoryId
     * @param string $typeId
     */
    public function getTypeDefinition($typeId)
    {
        return $this->repositoryEngine->getTypeDefinition($typeId);
    }

    /**2.2.2.4 Returns the set of the descendant object-types deﬁned for the Repository under the speciﬁed type.
     * @param type $repositoryId
     * @param type $typeId
     * @param type $depth
     * @param type $includePropertyDefinitions
     */
    public function getTypeDescendants($typeId = null, $depth = null, $includePropertyDefinitions = false)
    {
        return $this->repositoryEngine->getTypeDescendants($typeId, $depth, $includePropertyDefinitions);
    }

    /**2.2.2.7 Updates a type deﬁnition.
     *
     * @param type $repositoryId
     * @param type $typeDeﬁnition
     */
    public function updateType($typeDeﬁnition)
    {
        return $this->repositoryEngine->updateType($typeDeﬁnition);
    }
}
