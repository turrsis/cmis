<?php
namespace Cmis\Cmis\Interfaces;
/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 * 2.2.4 Object Services
 */
interface ObjectServiceInterface
{
    /**2.2.4.1 Creates a document object of the speciﬁed type (given by the cmis:objectTypeId property) in the (optionally) speciﬁed location.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional
     */
    public function createDocument($properties, $optional = [
        'folderId'          => null,
        'contentStream'     => null,
        'versioningState'   => null,
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);

    /**2.2.4.2 Creates a document object as a copy of the given source document in the (optionally) speciﬁed location.
     *
     * @param type $repositoryId
     * @param type $sourceId
     * @param type $optional
     */
    public function createDocumentFromSource($sourceId, $optional = [
        'folderId'          => null,
        'properties'        => null,
        'versioningState'   => null,
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);

    /**2.2.4.3 Creates a folder object of the speciﬁed type in the speciﬁed location.
     *
     * @param type  $repositoryId  The identiﬁer for the repository.
     * @param array $properties    The property values that MUST be applied to the newly-created folder object.
     * @param type  $folderId      The identiﬁer for the folder that MUST be the parent folder for the newly-created folder object.
     * @param array $optional
     *                  <Array> Id policies: A list of policy ids that MUST be applied to the newly-created folder object.
     *                  <Array> ACE addACEs: A list of ACEs that MUST be added to the newly-created folder object, either using the ACL from folderId if speciﬁed, or being applied if no folderId is speciﬁed.
     *                  <Array> ACE removeACEs: A list of ACEs that MUST be removed from the newly-created folder object, either using the ACL from folderId if speciﬁed, or being ignored if no folderId is speciﬁed.
     */
    public function createFolder($properties, $folderId, $optional = [
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);

    /**2.2.4.4 Creates a relationship object of the speciﬁed type.
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional
     */
    public function createRelationship($properties, $optional = [
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);

    /**2.2.4.5 Creates a policy object of the speciﬁed type.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional
     */
    public function createPolicy($properties, $optional = [
        'folderId'          => null,
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);
    /**2.2.4.6 Creates an item object of the speciﬁed type.
     *
     * @param type $repositoryId
     * @param type $properties
     * @param type $optional
     */
    public function createItem($properties, $optional = [
        'folderId'          => null,
        'policies'          => null,
        'addACEs'           => null,
        'removeACEs'        => null,
    ]);
    /**2.2.4.7 Gets the list of allowable actions for an object (see section 2.2.1.2.6 Allowable Actions).
     *
     * @param type $repositoryId
     * @param type $objectId
     */
    public function getAllowableActions($objectId);

    /**2.2.4.8 Gets the speciﬁed information for the object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function getObject($objectId, $optional = [
        'filter'                    => null,
        'includeRelationships'      => null,
        'includePolicyIds'          => null,
        'renditionFilter'           => null,
        'includeACL'                => null,
        'includeAllowableActions'   => null,
    ]);
    /**2.2.4.9 Gets the list of properties for the object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $filter
     */
    public function getProperties($objectId, $filter = null);

    /**2.2.4.10 Gets the speciﬁed information for the object.
     *
     * @param type $repositoryId
     * @param type $path
     * @param type $optional
     */
    public function getObjectByPath($path, $optional = [
        'filter'                    => null,
        'includeRelationships'      => null,
        'includePolicyIds'          => null,
        'renditionFilter'           => null,
        'includeACL'                => null,
        'includeAllowableActions'   => null,
    ]);
    /**2.2.4.11 Gets the content stream for the speciﬁed document object, or gets a rendition stream for a speciﬁed rendition of a document or folder object.
     * Notes: Each CMIS protocol binding MAY provide a way for fetching a sub-range within a content stream, in a manner appropriate to that protocol.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $streamId
     */
    public function getContentStream($objectId, $streamId = null);

    /**2.2.4.12 Gets the list of associated renditions for the speciﬁed object. Only rendition attributes are returned, not rendition stream.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function getRenditions($objectId, $optional = []);

    /**2.2.4.13 Updates properties and secondary types of the speciﬁed object.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of an update properties operation. Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - Only properties whose values are diﬀerent than the original value of the object SHOULD be provided.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $properties
     * @param type $changeToken
     */
    public function updateProperties($objectId, $properties, $changeToken = null);
    /**2.2.4.14 Updates properties and secondary types of one or more objects.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of an update properties operation. Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - Only properties whose values are diﬀerent than the original value of the object SHOULD be provided.
     *      - This service is not atomic. If the update fails, some objects might have been updated and others might not have been updated.
     *      - This service MUST NOT throw an exception if the update of an object fails. If an update fails, the object id of this particular object MUST be omitted from the result.
     * @param type $repositoryId
     * @param type $objectIdAndChangeToken
     * @param type $optional
     */
    public function bulkUpdateProperties($objectIdAndChangeToken, $optional = [
        'properties',
        'addSecondaryTypeIds',
        'removeSecondaryTypeIds',
    ]);
    /**2.2.4.15 Moves the specified file-able object from one folder to another.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param type $targetFolderId
     * @param type $sourceFolderId
     */
    public function moveObject($objectId, $targetFolderId, $sourceFolderId);
    /**2.2.4.16 Deletes the speciﬁed object.
     *
     * @param type $repositoryId
     * @param type $objectId
     * @param bool $allVersions
     */
    public function deleteObject($objectId, $allVersions = true);

    /**2.2.4.17 Deletes the speciﬁed folder object and all of its child- and descendant-objects.
     * Notes:
     *      - A repository MAY attempt to delete child- and descendant-objects of the speciﬁed folder in any order.
     *      - Any child- or descendant-object that the repository cannot delete MUST persist in a valid state in the CMIS domain model.
     *      - This service is not atomic.
     *      - However, if deletesinglefiled is chosen and some objects fail to delete, then single-ﬁled objects are either deleted or kept,
     *        never just unﬁled. This is so that a user can call this command again to recover from the error by using the same tree.
     * @param type $repositoryId
     * @param type $folderId
     * @param type $optional
     */
    public function deleteTree($folderId, $optional = [
        'allVersions',
        'unfileObjects',
        'continueOnFailure'
    ]);
    /**2.2.4.18 Sets the content stream for the speciﬁed document object.
     * Notes: A repository MAY automatically create new document versions as part of this service operations.
     *        Therefore, the objectId output NEED NOT be identical to the objectId input.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $contentStream
     * @param type $optional
     */
    public function setContentStream($objectId, $contentStream, $optional = [
        'overwriteFlag',
        'changeToken',
    ]);
    /**2.2.4.19 Appends to the content stream for the speciﬁed document object.
     * Notes:
     *      - A repository MAY automatically create new document versions as part of this service method.
     *        Therefore, the objectId output NEED NOT be identical to the objectId input.
     *      - The document may or may not have a content stream prior to calling this service.
     *        If there is no content stream, this service has the eﬀect of setting the content stream with the value of the input contentStream.
     *      - This service is intended to be used by a single client. It should support the upload of very huge content streams.
     *        The behavior is repository speciﬁc if multiple clients call this service in succession or in parallel for the same document.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $contentStream
     * @param type $optional
     */
    public function appendContentStream($objectId, $contentStream, $optional = [
        'isLastChunk',
        'changeToken',
    ]);
    /**2.2.4.20 Deletes the content stream for the speciﬁed document object.
     * Notes: A repository MAY automatically create new document versions as part of this service method. Therefore, the obejctId output NEED NOT be identical to the objectId input.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $changeToken
     */
    public function deleteContentStream($objectId, $changeToken = null);
}
