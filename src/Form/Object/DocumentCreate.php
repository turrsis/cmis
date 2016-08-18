<?php
namespace Cmis\Form\Object;

class DocumentCreate extends AbstractObject
{
    protected $optionalElements = array(
        'folderId'        => 'cmis:folderId',
        'contentStream'   => 'cmis:contentStream',
        'versioningState' => 'cmis:versioningState',
        //'policies'        => 'cmis:policies',
    );
}
