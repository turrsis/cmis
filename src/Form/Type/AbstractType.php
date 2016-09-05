<?php
namespace Cmis\Form\Type;

use Zend\Form\Form;
use Cmis\Cmis;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Tgs\Form\Traits\FormPostValidTrait;
use Zend\Form\FormInterface;
use Turrsis\Cmis\Utils\ArrayUtils;

class AbstractType extends Form implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, FormPostValidTrait;

    protected $wrapElements = true;

    protected $removeDataKeys = array();

    public function init()
    {
        $this->getFormFactory()->configureForm($this, array('elements' => array(
            array('spec' => array(
                'name'          => 'id',
                'type'          => 'text',
                'attributes'    => array(
                    'readonly' => true,
                ),
                'options'    => array(
                    'label'         => 'id:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'displayName',
                'type'          => 'text',
                'attributes'    => array(
                    'autocomplete'  => false,
                ),
                'options'    => array(
                    'label'         => 'displayName:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'localName',
                'type'          => 'text',
                'attributes'    => array(
                    'autocomplete'  => false,
                ),
                'options'    => array(
                    'label'         => 'localName:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'localNamespace',
                'type'          => 'text',
                'attributes'    => array(
                    'autocomplete'  => false,
                ),
                'options'    => array(
                    'label'         => 'localNamespace:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'queryName',
                'type'          => 'text',
                'attributes'    => array(
                    'autocomplete'  => false,
                ),
                'options'    => array(
                    'label'         => 'queryName:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'description',
                'type'          => 'text',
                'attributes'    => array(
                    'autocomplete'  => false,
                ),
                'options'    => array(
                    'label'         => 'description:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'type'    => 'selecttree',
                'name'    => 'parentId',
                'options' => array(
                    'label'        => 'parentId',
                    'value_key'    => 'id',
                    'label_key'    => 'displayName',
                    'selected_key' => 'cmis:path',
                    'item_type'    => 'submit',
                    'value_options' => $this->getParentsList(),
                    'list_attributes' => array(
                        'class' => 'selectParentIdList',
                    ),
                    'selected_attributes' => array(
                        'class' => 'selected',
                    ),
                    'isAttribute'   => true,
                ),
                'attributes' => array(
                    'class' => 'selectParentId',
                ),
            )),
            array('spec' => array(
                'name'          => 'creatable',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'creatable:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'fileable',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'fileable:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'queryable',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'queryable:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'controllablePolicy',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'controllablePolicy:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'controllableACL',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'controllableACL:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'fulltextIndexed',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'fulltextIndexed:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'includedInSupertypeQuery',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'includedInSupertypeQuery:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'typeMutability.create',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'typeMutability.create:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'typeMutability.update',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'typeMutability.update:',
                    'isAttribute'   => true,
                ),
            )),
            array('spec' => array(
                'name'          => 'typeMutability.delete',
                'type'          => 'checkbox',
                'options'    => array(
                    'label'         => 'typeMutability.delete:',
                    'isAttribute'   => true,
                ),
            )),
            /*array('spec' => array(
                'name'          => 'typeSpecific',
                'type'          => 'textarea',
                'options'    => array(
                    'label'         => 'typeSpecific:',
                    'isAttribute'   => true,
                ),
            )),*/
            array('spec' => array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'propertyDefinitions',
                'options' => array(
                    //'label'                  => 'Please choose categories for this product',
                    'count'                  => 0,
                    'should_create_template' => true,
                    'allow_add'              => true,
                    'target_element'         => array(
                        'type' => 'Cmis\Form\Type\EditProperties',
                    ),
                ),
            )),
            array(
                'name'          => 'save',
                'type'          => 'submit',
                'options' => array(
                    'label'         => 'сохранить',
                ),
            ),
            array(
                'name'          => 'addProperty',
                'type'          => 'submit',
                'options' => array(
                    'label'         => 'addProperty',
                ),
            ),
        )));
        $this->removeDataKeys[] = 'save';
        $this->removeDataKeys[] = 'addProperty';
    }


    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);
        foreach($this->removeDataKeys as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    protected function getParentsList()
    {
        $repositoryService = $this->getCmis()->repositoryService();
        $list = $repositoryService->getTypeDescendants(null);
        $list = ArrayUtils::iteratorToNestedArray($list, 'depth');
        return $list;
    }
}
