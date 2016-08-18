<?php
namespace Cmis\Form\Type;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AbstractProperties extends Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        $this->getFormFactory()->configureFieldset($this, array(
            'name'      => 'attributes',
            'elements' => array(
                array('spec' => array(
                    'name'          => 'delete',
                    'type'          => 'checkbox',
                    'options' => array(
                        'label'             => 'delete',
                        'use_hidden_element' => false,
                    ),
                )),
                array('spec' => array(
                    'name'          => 'id',
                    'type'          => 'hidden',
                    'options'    => array(
                        //'label'         => 'id:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'propertyType',
                    'type'          => 'select',
                    'attributes'    => array(
                        'autocomplete'  => false,
                    ),
                    'options'    => array(
                        'label'         => 'propertyType:',
                        'value_options' => array(
                            'xs:id'       => 'id',
                            'xs:string'   => 'string',
                            'xs:dateTime' => 'dateTime',
                            'xs:boolean'  => 'boolean',
                            'xs:integer'  => 'integer',
                            'xs:decimal'  => 'decimal',
                            'xs:uri'      => 'uri',
                            'xs:html'     => 'html',
                        ),
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
                    ),
                )),
                array('spec' => array(
                    'name'          => 'cardinality',
                    'type'          => 'select',
                    'attributes'    => array(
                        'autocomplete'  => false,
                    ),
                    'options'    => array(
                        'label'         => 'cardinality:',
                        'value_options' => array(
                            'single'  => 'single',
                            'multi'   => 'multi',
                        ),
                    ),
                )),
                array('spec' => array(
                    'name'          => 'updatability',
                    'type'          => 'select',
                    'attributes'    => array(
                        'autocomplete'  => false,
                    ),
                    'options'    => array(
                        'label'         => 'updatability:',
                        'value_options' => array(
                            'readonly'       => 'readonly',
                            'readwrite'      => 'readwrite',
                            'whencheckedout' => 'whencheckedout',
                            'oncreate'       => 'oncreate',
                        ),
                    ),
                )),
                array('spec' => array(
                    'name'          => 'required',
                    'type'          => 'checkbox',
                    'options'    => array(
                        'label'         => 'required:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'queryable',
                    'type'          => 'checkbox',
                    'options'    => array(
                        'label'         => 'queryable:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'orderable',
                    'type'          => 'checkbox',
                    'options'    => array(
                        'label'         => 'orderable:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'choices',
                    'type'          => 'text',
                    'options'    => array(
                        'label'         => 'choices:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'openChoice',
                    'type'          => 'checkbox',
                    'options'    => array(
                        'label'         => 'openChoice:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'defaultValue',
                    'type'          => 'text',
                    'options'    => array(
                        'label'         => 'defaultValue:',
                    ),
                )),
                array('spec' => array(
                    'name'          => 'typeSpecific',
                    'type'          => 'textarea',
                    'options'    => array(
                        'label'         => 'typeSpecific:',
                    ),
                )),
            ),

            /*'input_filter' => array(
                'cmis:objectId'               => array('required' => true,),
                'cmis:name'             => array(
                    'required' => true,
                    'name'              => 'name',
                    'validators'        => array(
                        array(
                            'name' => 'callback',
                            'options' => array(
                                'callback' => \Closure::bind(function ($name, $context = null) {
                                    try {
                                        $q = '';
                                        $role = $this->getServiceLocator()->getServiceLocator()
                                                    ->get('modelManager')
                                                    ->get('roles')
                                                    ->getRole(strtolower($name));
                                        return $role['properties']['cmis:objectId'] == $context['cmis:objectId'];
                                    } catch (\Cmis\Cmis\Exception\ObjectNotFound $e) {
                                        return false;
                                    }
                                }, $this),
                                'messages' => array(
                                    'callbackValue' => 'role already exist',
                                ),
                            ),
                        ),
                    ),
                 ),
                'cmis:description'  => array('required' => true,),
            ),*/
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'delete'    => array(
                'required' => false,
            ),
            'queryName' => array(
                'required' => true,
            ),
        );
    }

}
