<?php
namespace Cmis\Cmis\Interfaces;

/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 */
interface NavigationServiceInterface
{
    /**2.2.3.1 Gets the list of child objects contained in the speciﬁed folder.
     *
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional
     */
    public function getChildren($folderId, $optional = [
                                                            'maxItems'                  => null,
                                                            'skipCount'                 => null,
                                                            'orderBy'                   => null,
                                                            'filter'                    => null,
                                                            'includeRelationships'      => null,
                                                            'renditionFilter'           => null,
                                                            'includeAllowableActions'   => false,
                                                            'includePathSegment'        => false
    ]);

    /**2.2.3.2 Gets the set of descendant objects contained in the speciﬁed folder or any of its child-folders.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional
     */
    public function getDescendants($folderId, $optional = [
        'depth'                     => null,
        'filter'                    => null,
        'includeRelationships'      => null,
        'renditionFilter'           => null,
        'includeAllowableActions'   => null,
        'includePathSegment'        => null,
    ]);

    /**2.2.3.3 Gets the set of descendant folder objects contained in the speciﬁed folder.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional
     */
    public function getFolderTree($folderId, $optional = [
        'depth'                     => null,
        'filter'                    => null,
        'includeRelationships'      => null,
        'renditionFilter'           => null,
        'includeAllowableActions'   => null,
        'includePathSegment'        => null,
    ]);

    /**2.2.3.4 Gets the parent folder object for the speciﬁed folder object.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $filter
     */
    public function getFolderParent($folderId, $filter = null);

    /**2.2.3.5 Gets the parent folder(s) for the speciﬁed fileable object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function getObjectParents($objectId, $optional = [
        'filter'                        => null,
        'includeRelationships'          => null,
        'renditionFilter'               => null,
        'includeAllowableActions'       => null,
        'includeRelativePathSegment'    => null,
    ]);

    /**2.2.3.6 Gets the list of documents that are checked out that the user has access to.
     */
    public function getCheckedOutDocs($optional = []);


}
