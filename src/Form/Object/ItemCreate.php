<?php
namespace Cmis\Form\Object;

class ItemCreate extends AbstractObject
{
    protected $optionalElements = array(
        'folderId'        => 'cmis:folderId',
        'policies'        => 'cmis:policies',
    );
}
