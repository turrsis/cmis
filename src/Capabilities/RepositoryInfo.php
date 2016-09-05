<?php
namespace Cmis\Cmis\Capabilities;

use Zend\Stdlib\ArrayUtils;

class RepositoryInfo
{
    protected $info = array();

    public function __construct(array $info = array())
    {
        $this->info = ArrayUtils::merge($info, $this->info);
    }
    /**The identiﬁer for the repository<br>
     * <b>Note:</b> This MUST be the same identiﬁer as the input to the method.
     * @return string */
    public function repositoryId()
    {
        return $this->info['id'];
    }
    /**A display name for the repository.
     * @return string */
    public function repositoryName()
    {
        return $this->info['name'];
    }
    /**A display description for the repository.
     * @return string */
    public function repositoryDescription()
    {
        return $this->info['description'];
    }
    /**A display name for the vendor of the repository’s underlying application.
     * @return string */
    public function vendorName()
    {
        return $this->info['vendorName'];
    }
    /**A display name for the repository’s underlying application.
     * @return string */
    public function productName()
    {
        return $this->info['productName'];
    }
    /**A display name for the version number of the repository’s underlying application.
     * @return string */
    public function productVersion()
    {
        return $this->info['productVersion'];
    }
    /**The id of the root folder object for the repository.
     * @return string */
    public function rootFolderId()
    {
        return $this->info['rootFolderId'];
    }
    /**The set of values for the repository-optional capabilities speciﬁed in section 2.1.1.1 Optional Capabilities.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-190001
     * @return RepositoryCapabilites */
    public function capabilities()
    {
        return $this->info['capabilities'];
    }
    /**The change log token corresponding to the most recent change event for any object in the repository.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-12700015
     * @see See section 2.1.15 Change Log.
     * @return string */
    public function latestChangeLogToken()
    {
        return $this->info['latestChangeLogToken'];
    }
    /**A Decimal as String that indicates what version of the CMIS speciﬁcation this repository supports as speciﬁed in section 2.1.1.2 Implementation Information. <br>
     * This value MUST be "1.1".
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-200002
     * @return string
     */
    public function cmisVersionSupported()
    {
        if (isset($this->info['cmisVersionSupported'])) {
            return $this->info['cmisVersionSupported'];
        }
        return '1.1';
    }
    /**A optional repository-speciﬁc URI pointing to the repository’s web interface. <br>
     * <b>MAY</b> be not set.
     * @return string */
    public function thinClientURI()
    {
        if (isset($this->info['thinClientURI'])) {
            return $this->info['thinClientURI'];
        }
        return null;
    }
    /**Indicates whether or not the repository’s change log can return all changes ever made to any object
     * in the repository or only changes made after a particular point in time.
     * Applicable when the repository’s optional capability capabilityChanges is not none.
     * <ul>
     *      <li>If <b>FALSE</b>, then the change log can return all changes ever made to every object.</li>
     *      <li>If <b>TRUE</b>, then the change log includes all changes made since a particular point in time, but not all changes ever made.</li>
     * </ul>
     * @return bool */
    public function changesIncomplete()
    {
        return (bool)$this->info['changesIncomplete'];
    }
    /**Indicates whether changes are available for base types in the repository. Valid values are from enumBaseObjectTypeIds. See section 2.1.15 Change Log.
     * <ul>
     *  <li>cmis:document</li>
     *  <li>cmis:folder</li>
     *  <li>cmis:policy</li>
     *  <li>cmis:relationship</li>
     *  <li>cmis:item</li>
     * </ul>
     * Note: The base type <i>cmis:secondary</i> <b>MUST NOT</b> be used here. Only primary base types can be in this list.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-12700015
     * @return array */
    public function changesOnType()
    {
        return $this->info['changesOnType'];
    }
    /**Speciﬁes which types of permissions are supported.
     * <table>
     *      <tr><td><b>'basic'</b></td><td>Indicates that the CMIS basic permissions are supported.</td></tr>
     *      <tr><td><b>'repository'</b></td><td>Indicates that repository speciﬁc permissions are supported.</td></tr>
     *      <tr><td><b>'both'</b></td><td>Indicates that both CMIS basic permissions and repository speciﬁc permissions are supported.</td></tr>
     * <table>
     * @return string
     */
    public function supportedPermissions()
    {
        return $this->info['supportedPermissions'];
    }
    /**The allowed value(s) for applyACL, which control how non-direct ACEs are handled by the repository. See section 2.1.12.3 ACL Capabilities.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-800003
     * @return string
     */
    public function propagation()
    {
        return $this->info['propagation'];
    }
    /**The list of repository-speciﬁc permissions the repository supports for managing ACEs. See section 2.1.12 Access Control.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-7700012
     * @return array */
    public function permissions()
    {
        return $this->info['permissions'];
    }
    /**The list of mappings for the CMIS basic permissions to allowable actions. See section 2.1.12 Access Control.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-7700012
     * @return array */
    public function mapping()
    {
        return $this->info['mapping'];
    }
    /**If set, this ﬁeld holds the principal who is used for anonymous access.
     * This principal can then be passed to the ACL services to specify what permissions anonymous users should have.
     * @return string */
    public function principalAnonymous()
    {
        return $this->info['principalAnonymous'];
    }
    /**If set, this ﬁeld holds the principal who is used to indicate any authenticated user.
     * This principal can then be passed to the ACL services to specify what permissions any authenticated user should have.
     * @return string */
    public function principalAnyone()
    {
        return $this->info['principalAnyone'];
    }
    /**Optional list of additional repository features. See section 2.1.1.3 Repository Features.
     * @link http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-210003
     * @return array */
    public function extendedFeatures()
    {
        if (isset($this->info['extendedFeatures'])) {
            return $this->info['extendedFeatures'];
        }
        return array();
    }
}
