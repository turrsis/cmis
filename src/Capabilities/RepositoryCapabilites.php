<?php
namespace Cmis\Cmis\Capabilities;

use Zend\Stdlib\ArrayUtils;

/**
 * @property bool   $getDescendants             <b>Navigation Capabilities : </b><br> Ability for an application to enumerate the descendants of a folder via the getDescendants service.
 * @property bool   $getFolderTree              <b>Navigation Capabilities : </b><br> Ability for an application to retrieve the folder tree via the getFolderTree service.
 * @property string $orderBy                    <b>Navigation Capabilities : </b><br> Indicates the ordering capabilities of the repository.<br>
 *                                              Valid values are : <br><table>
 *                                                  <tr><td><b>none</b>   : Ordering is not supported.</td></tr>
 *                                                  <tr><td><b>common</b> : Only common CMIS properties are supported. See <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-1600007</b></td></tr>
 *                                                  <tr><td><b>custom</b> : Common CMIS properties and custom object-type properties are supported.</td></tr>
 *                                              </table>
 *
 * @property string $contentStreamUpdatability  <b>Object Capabilities : </b><br> Indicates the support a repository has for updating a documents content stream.<br>
 *                                              Valid values are : <br><table>
 *                                                  <tr><td><b>none</b></td>    <td>The content stream may never be updated.</td></tr>
 *                                                  <tr><td><b>anytime</b></td> <td>The content stream may be updated any time.</td></tr>
 *                                                  <tr><td><b>pwconly</b></td> <td>The content stream may be updated only when checked out. Private Working Copy (PWC) is described in section 2.1.13 <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-9000013</b></td></tr>
 *                                              </table>
 *                                              See section 2.1.4.1 Content Stream. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-390001</b>
 *
 * @property string $changes                    <b>Object Capabilities : </b><br> Indicates what level of changes (if any) the repository exposes via the getContentChanges service.<br>
 *                                              Valid values are : <br><table>
 *                                                  <tr><td><b>none</b></td>            <td>The repository does not support the change log feature.</td></tr>
 *                                                  <tr><td><b>objectidsonly</b></td>   <td>The change log can return only the object ids for changed objects in the repository and an indication of the type of change, not details of the actual change.</td></tr>
 *                                                  <tr><td><b>properties</b></td>      <td>The change log can return properties and the object id for the changed objects.</td></tr>
 *                                                  <tr><td><b>all</b></td>             <td>The change log can return the object ids for changed objects in the repository and more information about the actual change.</td></tr>
 *                                              </table>
 *                                              See section 2.1.15 Change Log <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-12700015</b>.
 *
 * @property string $renditions                 <b>Object Capabilities : </b><br> Indicates whether or not the repository exposes renditions of document or folder objects.<br>
 *                                              Valid values are : <br><table>
 *                                                  <tr><td><b>none</b></td>    <td>The repository does not expose renditions at all.</td></tr>
 *                                                  <tr><td><b>read</b></td>    <td>Renditions are provided by the repository and readable by the client.</td></tr>
 *                                              </table>
 *                                              See section 2.1.4.2 Renditions <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-400002</b>
 *
 * @property bool   $multiﬁling                 <b>Filing Capabilities : </b><br> Ability for an application to ﬁle a document or other ﬁle-able object in more than one folder.<br>
 *                                              See section 2.1.5 Folder Object. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-470005</b>
 * @property bool   $unﬁling                    <b>Filing Capabilities : </b><br> Ability for an application to leave a document or other ﬁle-able object not ﬁled in any folder.<br>
 *                                              See section 2.1.5 Folder Object. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-470005</b>
 * @property bool   $versionSpeciﬁcFiling       <b>Filing Capabilities : </b><br> Ability for an application to ﬁle individual versions (i.e., not all versions) of a document in a folder.<br>
 *                                              See section 2.1.13 Versioning. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-9000013</b>
 *
 *
 * @property bool   $PWCUpdatable               <b>Versioning Capabilities : </b><br> Ability for an application to update the "Private Working Copy" of a checked-out document.<br>
 *                                              See section 2.1.13 Versioning. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-9000013</b>
 * @property bool   $PWCSearchable              <b>Versioning Capabilities : </b><br> Ability of the Repository to include the "Private Working Copy" of checked-out documents in query search scope; otherwise PWC’s are not searchable.<br>
 *                                              See section 2.1.13 Versioning. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-9000013</b>
 * @property bool   $AllVersionsSearchable      <b>Versioning Capabilities : </b><br> Ability of the Repository to include all versions of document. If False, typically either the latest or the latest major version will be searchable.<br>
 *                                              See section 2.1.13 Versioning. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-9000013</b>
 *
 * @property string $query                      <b>Query Capabilities : </b><br> Indicates the types of queries that the Repository has the ability to fulﬁll. Query support levels are:
 *                                              <table>
 *                                                  <tr><td><b>none</b></td>            <td>No queries of any kind can be fulﬁlled.</td></tr>
 *                                                  <tr><td><b>metadataonly</b></td>    <td>Only queries that ﬁlter based on object properties can be fulﬁlled. Speciﬁcally, the CONTAINS() predicate function is not supported.</td></tr>
 *                                                  <tr><td><b>fulltextonly</b></td>    <td>Only queries that ﬁlter based on the full-text content of documents can be fulﬁlled. Speciﬁcally, only the CONTAINS() predicate function can be included in the WHERE clause.</td></tr>
 *                                                  <tr><td><b>bothseparate</b></td>    <td>The repository can fulﬁll queries that ﬁlter EITHER on the full-text content of documents OR on their properties, but NOT if both types of ﬁlters are included in the same query.</td></tr>
 *                                                  <tr><td><b>bothcombined</b></td>    <td>The repository can fulﬁll queries that ﬁlter on both the full-text content of documents and their properties in the same query.</td></tr>
 *                                              </table>
 *                                              See section 2.1.14 Query. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-10500014</b>
 *
 * @property string $join                       <b>Query Capabilities : </b><br> Indicates the types of JOIN keywords that the Repository can fulﬁll in queries. Support levels are:
 *                                              <table>
 *                                                  <tr><td><b>none</b></td>            <td>The repository cannot fulﬁll any queries that include any JOIN clauses on two primary types. If the Repository supports secondary types, JOINs on secondary types SHOULD be supported, even if the support level is none.</td></tr>
 *                                                  <tr><td><b>inneronly</b></td>       <td>The repository can fulﬁll queries that include an INNER JOIN clause, but cannot fulﬁll queries that include other types of JOIN clauses.</td></tr>
 *                                                  <tr><td><b>innerandouter</b></td>   <td>The repository can fulﬁll queries that include any type of JOIN clause deﬁned by the CMIS query grammar.</td></tr>
 *                                              </table>
 *                                              See section 2.1.14 Query. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-10500014</b>
 *
 * @property string $creatablePropertyTypes     <b>Type Capabilities : </b><br> A list of all property data types that can be used by a client to create or update an object-type deﬁnition. <br>
 *                                              See sections 2.1.2.1 Property <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-230001</b><br>
 *                                              and 2.1.10.1 General Constraints on Metadata Changes. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-750001</b>
 *
 *
 * @property array $newTypeSettableAttributes   <b>Type Capabilities : </b><br> Indicates which object-type attributes can be set by a client when a new object-type is created. This capibility is a set of booleans; one for each of the following attributes:
 *                                              <ul>
 *                                                  <li>id</li>
 *                                                  <li>localName</li>
 *                                                  <li>localNamespace</li>
 *                                                  <li>displayName</li>
 *                                                  <li>queryName</li>
 *                                                  <li>description</li>
 *                                                  <li>creatable</li>
 *                                                  <li>ﬁleable</li>
 *                                                  <li>queryable</li>
 *                                                  <li>fulltextIndexed</li>
 *                                                  <li>includedInSupertypeQuery</li>
 *                                                  <li>controllablePolicy</li>
 *                                                  <li>controllableACL</li>
 *                                              </ul>
 *
 * @property string $ACL                        <b>ACL Capabilities : </b><br> Indicates the level of support for ACLs by the repository.
 *                                              <table>
 *                                                  <tr><td><b>none</b></td>        <td>The repository does not support ACL services.</td></tr>
 *                                                  <tr><td><b>discover</b></td>    <td>The repository supports discovery of ACLs (getACL and other services).</td></tr>
 *                                                  <tr><td><b>manage</b></td>      <td>The repository supports discovery of ACLs AND applying ACLs (getACL and applyACL services).</td></tr>
 *                                              </table>
 *                                              See section 2.1.12 Access Control. <b>http://docs.oasis-open.org/cmis/CMIS/v1.1/cos01/CMIS-v1.1-cos01.html#x1-7700012</b>
 */

class RepositoryCapabilites
{
    protected $capabilities = array(
        // Navigation
        'capabilityGetDescendants' => false,
        'capabilityGetFolderTree'  => false,
        'capabilityOrderBy'        => 'none',
        //Object
        'capabilityContentStreamUpdatability'   => 'none',
        'capabilityChanges'                     => 'none',
        'capabilityRenditions'                  => 'none',
        // Filing
        'capabilityMultiﬁling'              => false,
        'capabilityUnﬁling'                 => false,
        'capabilityVersionSpeciﬁcFiling'    => false,
        //Versioning
        'capabilityPWCUpdatable'            => false,
        'capabilityPWCSearchable'           => false,
        'capabilityAllVersionsSearchable'   => false,

        //Query
        'capabilityQuery'            => 'none',
        'capabilityJoin'           => 'none',
        //Type
        'capabilityCreatablePropertyTypes'      => false,
        'capabilityNewTypeSettableAttributes'   => array(
            'id'                        => false,
            'localName'                 => false,
            'localNamespace'            => false,
            'displayName'               => false,
            'queryName'                 => false,
            'description'               => false,
            'creatable'                 => false,
            'fileable'                  => false,
            'queryable'                 => false,
            'fulltextIndexed'           => false,
            'includedInSupertypeQuery'  => false,
            'controllablePolicy'        => false,
            'controllableACL'           => false,
        ),

        //ACL
        'capabilityACL'                     => 'none',
    );

    public function __construct(array $capabilities = array())
    {
        $this->capabilities = ArrayUtils::merge($capabilities, $this->capabilities);
    }

    public function __get($name)
    {
        $name = 'capability' . ucfirst($name);
        if (isset($this->capabilities[$name])) {
            return $this->capabilities[$name];
        }
        return null;
    }

}
