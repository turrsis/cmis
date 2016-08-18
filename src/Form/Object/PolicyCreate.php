<?php
namespace Cmis\Form\Object;

class PolicyCreate extends AbstractObject
{
    protected $optionalElements = array(
        'folderId'        => 'cmis:folderId',
        'policies'        => 'cmis:policies',
    );
}
