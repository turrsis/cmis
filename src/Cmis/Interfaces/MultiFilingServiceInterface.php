<?php
namespace Cmis\Cmis\Interfaces;
/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 * 2.2.5 Multi-ﬁling Services
 */
interface MultiFilingServiceInterface
{
    /**2.2.5.1 Adds an existing fileable non-folder object to a folder.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $folderId
     * @param type $allVersions
     */
    public function addObjectToFolder($objectId, $folderId, $allVersions = true);
    /**2.2.5.2 Removes an existing fileable non-folder object from a folder.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $folderId
     */
    public function removeObjectFromFolder($objectId, $folderId = null);
}
