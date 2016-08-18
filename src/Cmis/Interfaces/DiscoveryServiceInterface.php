<?php
namespace Cmis\Cmis\Interfaces;
/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html
 * 2.2.6 Discovery Services
 */
interface DiscoveryServiceInterface
{
    /**2.2.6.1 Executes a CMIS query statement against the contents of the repository.
     *
     * @param type $repositoryId
     * @param type $statement
     * @param type $optional
     */
    public function query($statement, $optional = [
        'searchAllVersions',
        'includeRelationships',
        'renditionFilter',
        'includeAllowableActions',
        'maxItems',
        'skipCount',
    ]);
    /**2.2.6.2 Gets a list of content changes. This service is intended to be used by
     * search crawlers or other applications that need to eﬃciently understand what has changed in the repository. See section 2.1.15 Change Log.
     * Notes:
     *      - The content stream is NOT returned for any change event.
     *      - The deﬁnition of the authority needed to call this service is repository speciﬁc.
     *      - The latest change log token for a repository can be acquired via the getRepositoryInfo service.
     * @param type $repositoryId
     * @param type $optional
     */
    public function getContentChanges($optional = [
        'changeLogToken',
        'includeProperties',
        'includePolicyIds',
        'includeACL',
        'maxItems'
    ]);
}
