<?php
// CMIS
return array(
    'service_manager'   => array(
        'abstract_factories' => array(
            'Cmis\Cmis\RepositoryFactory'
        ),
    ),
    'cmis_repository' => array(
        'default' => array(
            'adapter'         => 'db',
            'common_table'    => 'cmis_object',
            'common_type_properties' =>  array(
                'propertyId'     => array('required' => true),
                'propertyType'   => array('required' => true),
                'localName'      => array('required' => false),
                'localNamespace' => array('required' => false),
                'queryName'      => array('required' => true),
                'displayName'    => array('required' => true),
                'description'    => array('required' => false),
                'cardinality'    => array('required' => true),
                'updatability'   => array('required' => true),
                'required'       => array('required' => true),
                'queryable'      => array('required' => true),
                'orderable'      => array('required' => true),
                'choices'        => array('required' => true),
                'openChoice'     => array('required' => true),
                'defaultValue'   => array('required' => false),
            ),
            'common_type_attributes' => array(
                'id' => array(
                    'type'                 => array('VARCHAR', 255),
                    'required'             => true,
                    'defaultValueInParent' => false,
                    'updatability'         => 'readonly',
                ),
                'parentId' => array(
                    'required'             => false,
                    'defaultValueInParent' => false,
                    'updatability'         => 'oncreate',
                ),
                'queryName' => array(
                    'required'             => true,
                    'defaultValueInParent' => false,
                    'updatability'         => 'readwrite',
                ),
                'displayName' => array(
                    'required'             => true,
                    'defaultValueInParent' => false,
                    'updatability'         => 'readwrite',
                ),
                'localName' => array(
                    'required'             => false,
                    'defaultValueInParent' => false,
                    'updatability'         => 'oncreate',
                ),
                'localNamespace' => array(
                    'required'             => false,
                    'defaultValueInParent' => false,
                    'updatability'         => 'oncreate',
                ),
                'baseId' => array(
                    'required'             => true,
                    'defaultValueInParent' => true,
                    'updatability'         => 'oncreate',
                ),
                'description' => array(
                    'required'             => false,
                    'defaultValueInParent' => false,
                    'updatability'         => 'readwrite',
                ),
                'creatable' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'oncreate',
                ),
                'fileable' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'oncreate',
                ),
                'queryable' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'controllablePolicy' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'controllableACL' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'fulltextIndexed' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'includedInSupertypeQuery' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'typeMutability.create' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'typeMutability.update' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'typeMutability.delete' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
                'typeSpecific' => array(
                    'required'             => false,
                    'defaultValueInParent' => true,
                    'updatability'         => 'readwrite',
                ),
            ),
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
            'cmis-edit-type'         => 'Cmis\Form\Type\Edit',
            'cmis-create-type'       => 'Cmis\Form\Type\Create',
            'cmis:folderId'          => 'Cmis\Form\Element\FolderId',
            'cmis:contentStream'     => 'Cmis\Form\Element\ContentStream',
            'cmis:policies'          => 'Cmis\Form\Element\Policies',
            'cmis:acl'               => 'Cmis\Form\Element\Acl',
            'cmis:versioningState'   => 'Cmis\Form\Element\VersioningState',
            'cmis:Document'     => 'Cmis\Form\Object\DocumentCreate',
            'cmis:Folder'       => 'Cmis\Form\Object\FolderCreate',
            'cmis:Item'         => 'Cmis\Form\Object\ItemCreate',
            'cmis:Policy'       => 'Cmis\Form\Object\PolicyCreate',
            'cmis:Relationship' => 'Cmis\Form\Object\RelationshipCreate',
        ),
        'factories' => array(

        ),
        'abstract_factories' => array(

        ),
        //'delegators'
    ),
    'controllers' => array(
        'invokables' => array(
            'cmis-folder'     => 'Cmis\Controller\FolderController',
            'cmis-type'       => 'Cmis\Controller\TypeController',
            'cmis-object'     => 'Cmis\Controller\ObjectController',

            'cmis-users'      => 'Cmis\Controller\Specified\UsersController',
            'cmis-roles'      => 'Cmis\Controller\Specified\RolesController',
            'cmis-navigation' => 'Cmis\Controller\Specified\NavigationController',
            'cmis-pages'      => 'Cmis\Controller\Specified\PagesController',
        ),
    ),
    'view_manager'  => array(
        'template_path_stack'       => array(
            //'cmis'                    => __DIR__ . '/../view/',
        ),
    ),
);
