<?php
namespace Cmis\Form\Object;

class FolderCreate extends AbstractObject
{
    protected $optionalElements = array(
        'folderId' => 'cmis:folderId',
        'policies' => 'cmis:policies',
    );

    /*protected function _factoryParentId($property)
    {
        $parentId = parent::_factoryParentId($property);
        $parentFolder = $this->getOption('parentFolder');
        if ($parentFolder) {
            $parentId->setAttribute('disabled', true);
            $parentId->setValue($parentFolder['properties']['cmis:objectId']);
        }

        return $parentId;
    }*/
}
