<?php
namespace Turrsis\Cmis\Services;

class ACLService
{
    /**
     * @var Rdb\SequrityEngine */
    protected $sequrityEngine;

    public function __construct($sequrityEngine)
    {
        $this->sequrityEngine = $sequrityEngine; 
    }

    /**2.2.10.1 Adds or removes the given ACEs to or from the ACL of an object
     * Notes: This service MUST be supported by the repository, if the optional capability capabilityACL is manage.
     *      - How ACEs are added or removed to or from the object is repository speciﬁc – with respect to the ACLPropagation parameter.
     *      - Some ACEs that make up an object’s ACL may not be set directly on the object,
     *        but determined in other ways, such as inheritance.
     *        A repository MAY merge the ACEs provided with the ACEs of the ACL already applied
     *        to the object (i.e. the ACEs provided MAY not be completely added or removed from the eﬀective ACL for the object).
     * @param type $repositoryId
     * @param type $objectId
     * @param type $optional ['addACEs', 'removeACEs', 'ACLPropagation']
     */
    public function applyACL($objectId, $optional = array()) {
        
    }

    /**2.2.10.2 Get the ACL currently applied to the speciﬁed object.
     * Notes: This service MUST be supported by the repository, if the optional
     * capability capabilityACL is discover or manage.
     * A client MUST NOT assume that the returned ACEs can be applied via applyACL.
     * @param type $repositoryId
     * @param type $objectId
     * @param type $onlyBasicPermissions
     */
    public function getACL($objectId, $onlyBasicPermissions = true) {
        
    }

}
