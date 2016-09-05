<?php
namespace Cmis\Form\Element;

use Zend\Form\Element\SelectTree;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Turrsis\Cmis\Utils\ArrayUtils;

class FolderId extends SelectTree implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function init()
    {
        $foldersList = ArrayUtils::iteratorToNestedArray(
            $this->options['repository']->getNavigationService()->getFolderTree(null, array('depth'=>-1)),
            'cmis:level'
        );
        $this->setOptions(array(
            'value_key'     => 'cmis:objectId',
            'label_key'     => 'cmis:name',
            //'item_type'     => 'checkbox',
            'value_options' => $foldersList,
        ));
        $this->setAttributes(array(
            'multiple' => 'multiple'
        ));
    }

    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();
        $spec['required'] = false;
        return $spec;
    }

    public function setValue($value)
    {
        return parent::setValue($value);
    }
}
