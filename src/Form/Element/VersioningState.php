<?php
namespace Cmis\Form\Element;

use Zend\Form\Element\Select;

class VersioningState extends Select
{
    public function init()
    {
        $this->setOptions(array(
            'value_options' => array(
                'none'       => 'none',
                'checkedout' => 'checkedout',
                'major'      => 'major',
                'minor'      => 'minor',
            ),
        ));
    }

    public function getInputSpecification()
    {
        $res = parent::getInputSpecification();
        $res['required'] = false;
        return $res;
    }
}
