<?php
namespace Turrsis\Cmis\Services;

class NavigationService
{
    /**
     * @var Rdb\ObjectsEngine */
    protected $objectsEngine;

    public function __construct($objectsEngine)
    {
       $this->objectsEngine = $objectsEngine;
    }

    /**2.2.3.6 Gets the list of documents that are checked out that the user has access to.
     */
    public function getCheckedOutDocs($optional = array())
    {
        return $this->objectsEngine->getCheckedOutDocs($optional);
    }

    /**2.2.3.1 Gets the list of child objects contained in the speciﬁed folder.
     *
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional ['maxItems', 'skipCount', 'orderBy', 'filter', 'includeRelationships', 'renditionFilter', 'includeAllowableActions', 'includePathSegment']
     */
    public function getChildren($folderId, $optional = array())
    {
        return $this->objectsEngine->getChildren($folderId, $optional);
    }

    /**2.2.3.2 Gets the set of descendant objects contained in the speciﬁed folder or any of its child-folders.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional ['depth', 'filter', 'includeRelationships', 'renditionFilter', 'includeAllowableActions', 'includePathSegment']
     */
    public function getDescendants($folderId, $optional = array())
    {
        return $this->objectsEngine->getDescendants($folderId, $optional);
    }

    /**2.2.3.4 Gets the parent folder object for the speciﬁed folder object.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $filter
     */
    public function getFolderParent($folderId, $filter = null)
    {
        return $this->objectsEngine->getFolderParent($folderId, $filter);
    }

    /**2.2.3.3 Gets the set of descendant folder objects contained in the speciﬁed folder.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional ['depth', 'filter', 'includeRelationships', 'renditionFilter', 'includeAllowableActions', 'includePathSegment']
     */
    public function getFolderTree($folderId, $optional = array())
    {
        return $this->objectsEngine->getFolderTree($folderId, $optional);
    }

    /**2.2.3.5 Gets the parent folder(s) for the speciﬁed fileable object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional ['filter', 'includeRelationships', 'renditionFilter', 'includeAllowableActions', 'includeRelativePathSegment']
     */
    public function getObjectParents($objectId, $optional = array())
    {
        return $this->objectsEngine->getObjectParents($objectId, $optional);
    }
}
