<?php
namespace Cmis\Cmis\Interfaces;
/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 * 2.2.7 Versioning Services
 */

interface VersioningServiceInterface
{
    /**2.2.7.1 Create a private working copy (PWC) of the document. See section 2.1.13.5.1 Checkout.
     *
     * @param type $repositoryId
     * @param type $objectId
     */
    public function checkOut($objectId);
    /**2.2.7.2 Reverses the eﬀect of a check-out (checkOut). Removes the Private Working Copy of the checked-out document,
     * allowing other documents in the version series to be checked out again.
     * If the private working copy has been created by createDocument,
     * cancelCheckOut MUST delete the created document. See section 2.1.13.5.3 Discarding Check out.
     *
     * @param type $repositoryId
     * @param type $objectId
     */
    public function cancelCheckOut($objectId);
    /**2.2.7.3  Checks-in the Private Working Copy document. See section 2.1.13.5.4 Checkin.
     * Notes:
     *      - For repositories that do NOT support the optional capabilityPWCUpdatable capability, the properties and contentStream input parameters MUST be provided on the checkIn service for updates to happen as part of checkIn.
     *      - Each CMIS protocol binding MUST specify whether the checkin service MUST always include all updatable properties, or only those properties whose values are diﬀerent than the original value of the object.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional
     */
    public function checkIn($objectId, $optional = [
        'major',
        'properties',
        'contentStream',
        'checkinComment',
        'policies',
        'addACEs',
        'removeACEs',
    ]);
    /**2.2.7.4 Get the latest document object in the version series.
     *
     * @param type $repositoryId
     * @param type $versionSeriesId
     * @param type $optional
     */
    public function getObjectOfLatestVersion($versionSeriesId, $optional = [
        'major',
        'filter',
        'includeRelationships',
        'includePolicyIds',
        'renditionFilter',
        'includeACL',
        'includeAllowableActions',
    ]);
    /**2.2.7.5 Get a subset of the properties for the latest document object in the version series.
     *
     * @param type $repositoryId
     * @param type $versionSeriesId
     * @param type $optional
     */
    public function getPropertiesOfLatestVersion($versionSeriesId, $optional =[
        'major',
        'filter'
    ]);
    /**2.2.7.6 Returns the list of all document objects in the speciﬁed version series, sorted by cmis:creationDate descending.
     * Notes: If a Private Working Copy exists for the version series and the caller has permissions to access it, then it MUST be returned as the ﬁrst object in the result list.
     * @param type $repositoryId
     * @param type $versionSeriesId
     * @param type $optional
     */
    public function getAllVersions($versionSeriesId, $optional = [
        'filter',
        'includeAllowableActions',
    ]);
}
