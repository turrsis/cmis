<?php
namespace Cmis\Form\Object;

class RelationshipCreate extends AbstractObject
{
    protected $optionalElements = array(
        'policies'        => 'cmis:policies',
    );
}
