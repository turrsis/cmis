<?php
namespace Cmis\Form\Element;

use Zend\Form\Element\SelectTree;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Policies extends SelectTree implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function init()
    {
        $policiesList = $this->getOption('repository')->getDiscoveryService()->query("SELECT * FROM cmis:policy");

        $this->setOptions(array(
            'value_key'     => 'cmis:objectId',
            'label_key'     => 'cmis:policyText',
            //'item_type'     => 'checkbox',
            'value_options' => array_column($policiesList['queryResults'], 'properties'),
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
}
