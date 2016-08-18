<?php
namespace Cmis\Form\Object;

use Zend\Form;
use Cmis\Cmis;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractObject extends Form\Form implements InputFilterProviderInterface
{

    protected $propertyToElementMap      = array(
        'xs:boolean'  => 'checkbox',
        'xs:dateTime' => 'text',
        'xs:decimal'  => 'number',
        'xs:html'     => 'textarea',
        'xs:id'       => 'text',
        'xs:integer'  => 'number',
        'xs:string'   => 'text',
        'xs:uri'      => 'text',
        'cmis:link'   => 'url',
    );

    protected $propertyOrder = array(
        'cmis:objectTypeId' => 1000,
        'cmis:name'         => 100,
        'cmis:description'  => 99,
        'cmis:parentId'     => 101,
    );

    protected $optionalElements = array();

    protected $defaultPermissions = array(
        'cmis:parentId.update'     => true,
        'cmis:objectTypeId.update' => true,
    );
    /**
     * @var Form\Fieldset
     */
    protected $propertiesFieldset = null;

    protected $inputFilterSpec = array(
        'properties' => array(
            'type' => 'Zend\InputFilter\InputFilter',
        ),
    );

    public static function factory(ServiceLocatorInterface $serviceManager, Cmis\Repository $repository, $options)
    {
        if (!isset($options['type'])) {
            throw new \Exception("'type' is required options");
        } elseif (!$options['type'] instanceof Cmis\Types\Type) {
            $options['type'] = $repository->getRepositoryService()->getTypeDefinition($options['type']);
        }

        if (isset($options['parentFolder']) && !is_array($options['parentFolder'])) {
            $options['parentFolder'] = $repository->getObjectService()->getObject($options['parentFolder']);
        }

        $formManager = $serviceManager->get('formElementManager');
        $formAlias   = $formManager->has($options['type']['queryName'])
                ? $options['type']['queryName']
                : $repository->getRepositoryService()->getTypeDefinition($options['type']['baseId'])['queryName'];
        
        $options['repository'] = $repository;
        return $formManager->get($formAlias, $options);
    }

    public function init()
    {
        // properties Fieldset
        $this->propertiesFieldset = new Form\Fieldset('properties');
        $this->add($this->propertiesFieldset);
        // properties Elements
        $type = $this->getOption('type');
        
        foreach($type['propertyDefinitions'] as $queryName => $property) {
            $methodName = '_factory' . ucfirst(substr($queryName, 5));
            $methodName = method_exists($this, $methodName) ? $methodName : null;
            if ($property['queryable'] == '0' && !$methodName) {
                continue;
            }

            $element = $methodName
                ? $element = $this->$methodName($property)
                : $this->_factoryElement($property);

            $flag = isset($this->propertyOrder[$queryName])
                    ? array('priority' => $this->propertyOrder[$queryName])
                    : array();

            $this->propertiesFieldset->add($element, $flag);
            $this->inputFilterSpec['properties'][] = $this->getElementFilterSpec($element, $property);
        }

        if ($this->optionalElements) {
            foreach($this->optionalElements as &$element) {
                if (is_string($element)) {
                    $element = array(
                        'type' => $element,
                    );
                    $element['options']['repository'] = $this->options['repository'];
                }
            }
            $this->getFormFactory()->configureForm($this, array('elements' => $this->optionalElements));
        }
        
        // save Button
        $this->add(array(
            'type'          => 'submit',
            'name'          => 'save',
            'attributes' => array(
                'value' => 'save',
            ),
            'options' => array(
                'label'         => 'сохранить',
            ),
        ));
    }

    public function setOptions($options)
    {
        if (isset($options['permissions'])) {
            $this->defaultPermissions = ArrayUtils::merge($this->defaultPermissions, $options['permissions']);
        }
        return parent::setOptions($options);
    }

    public function setData($data)
    {
        parent::setData($data);
        // set Values & Permissions
        if ($objectTypeId = $this->propertiesFieldset->get('cmis:objectTypeId')) {
            if (!$objectTypeId->getValue()) {
                $objectTypeId->setValue($this->getOption('type')['id']);
            }
            if (!$this->defaultPermissions['cmis:objectTypeId.update']) {
                $objectTypeId->setAttribute('disabled', true);
            }
        }
        if ($parentId = $this->propertiesFieldset->get('cmis:parentId', false)) {
            if (!$parentId->getValue() && $this->getOption('parentFolder')){
                $parentId->setValue($this->getOption('parentFolder')['properties']['cmis:objectId']);
            }
            if (!$this->defaultPermissions['cmis:parentId.update']) {
                $parentId->setAttribute('disabled', true);
            }
        }
        // Disable forbidden values
        if ($objectTypeId && $parentId) {
            $objectTypeIdValue = $objectTypeId->getValue();
            $parentId->setOption('onRenderOption', function (&$value) use ($objectTypeIdValue) {
                if ($value['cmis:allowedChildObjectTypeIds'] && !array_key_exists($objectTypeIdValue, array_flip(explode(',', $value['cmis:allowedChildObjectTypeIds'])))) {
                    $value['disabled'] = true;
                }
            });

            $parentFolder = null;
            if ($this->getOption('parentFolder')) {
                $parentFolder = $this->getOption('parentFolder');
            } elseif ($parentId->getValue()) {
                $parentFolder = $this->getOption('repository')->getObjectService()->getObject($parentId->getValue());
            }


            if ($parentFolder && $parentFolder['properties']['cmis:allowedChildObjectTypeIds']) {
                $objectTypeId->setOption(
                    'enabled_values',
                    explode(',', $parentFolder['properties']['cmis:allowedChildObjectTypeIds'])
                );
            }
        }
        return $this;
    }

    protected function _factoryElement($property)
    {
        return $this->getFormFactory()->create(array(
            'type'       => $this->resolvePropertyToElementType($property),
            'name'       => $property['queryName'],
            'attributes' => array(
                'autocomplete'  => false,
                'cmis:property' => $property,
                'readonly'      => $this->isPropertyReadonly($property),
            ),
            'options'    => array(
                'label'         => $property['displayName'],
            ),
        ));
    }

    protected function _factoryObjectTypeId($property)
    {
        if (!$this->getOption('isCreate')) {
            return $this->_factoryElement($property);
        }
        $objectTypeId = $this->getFormFactory()->create(array(
            'type'    => 'selecttree',
            'name'    => $property['queryName'],
            'options' => array(
                'label'           => $property['displayName'],
                'value_key'       => 'id',
                'label_key'       => 'queryName',
                'itemTemplate'    => 'button',
                'list_attributes' => array(
                    'class' => 'selectParentIdList',
                ),
                'selected_attributes' => array(
                    'class' => 'selected',
                ),
            ),
            'attributes' => array(
                'multiple' => false,
                'class'    => 'selectParentId',
            ),
            'priority' => 100,
        ));

        $valueOptions = $this->getOption('repository')->getRepositoryService()->getTypeDescendants(null);
        $objectTypeId->setValueOptions($valueOptions);
        return $objectTypeId;
    }

    protected function _factoryParentId($property)
    {
        $parentId = $this->getFormFactory()->create(array(
            'type'    => 'selecttree',
            'name'    => $property['queryName'],
            'options' => array(
                'label'           => $property['displayName'],

                'itemTemplate'    => 'button',

                'value_key'       => 'cmis:objectId',
                'label_key'       => 'cmis:name',
                'selected_key'    => 'cmis:path',
                'list_attributes' => array(
                    'class' => 'selectParentIdList',
                ),
                'selected_attributes' => array(
                    'class' => 'selected',
                ),
            ),
            'attributes' => array(
                'multiple' => false,
                'class'    => 'selectParentId',
            ),
            'priority' => 100,
        ));

        $valueOptions = $this->getOption('repository')->getNavigationService()->getFolderTree(null, array('depth'=>-1));
        $parentId->setValueOptions($valueOptions);

        return $parentId;
    }

    protected function _factoryPassword($property)
    {
        return $this->getFormFactory()->create(array(
            'type'    =>'changePassword',
            'name'    => $property['queryName'],
            'options' => array(
                'label'    => $property['displayName'],
                'isChange' => !$this->getOption('isCreate'),
            ),
        ));
    }

    protected function getElementFilterSpec(Form\ElementInterface $element, $property)
    {
        $elementFilter = $element instanceof InputProviderInterface
                            ? $element->getInputSpecification()
                            : array();

        $elementFilter['name']     = $element->getName();
        $elementFilter['required'] = isset($property['required'])
                                        ? (bool)$property['required']
                                        : false;
        return $elementFilter;
    }

    protected function isPropertyReadonly($property)
    {
        return (!$this->getOption('isCreate') && $property['updatability'] == 'oncreate')
            ? true
            : $property['updatability'] == 'readonly';
    }

    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpec;
    }
    
    protected function resolvePropertyToElementType($property)
    {
        if (isset($this->propertyToElementMap[$property['queryName']])) {
            return $this->propertyToElementMap[$property['queryName']];
        }
        if (isset($this->propertyToElementMap[$property['propertyType']])) {
            return $this->propertyToElementMap[$property['propertyType']];
        }
        return 'text';
    }
}
