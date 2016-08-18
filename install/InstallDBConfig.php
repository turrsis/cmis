<?php
use Zend\Db\Sql;
use Zend\Db\Sql\Ddl\Column;
use Zend\Db\Sql\Ddl\Constraint;

$sql = new Sql\Sql();
$cmisMetaTypes           = new Sql\TableIdentifier('cmis:meta_types');
$cmisMetaProperties      = new Sql\TableIdentifier('cmis:meta_properties');
$cmisMetaTypesProperties = new Sql\TableIdentifier('cmis:meta_types_properties');
$cmisObject              = new Sql\TableIdentifier('cmis_object');
$cmisDocument            = new Sql\TableIdentifier('cmis_document');
$cmisPolicy              = new Sql\TableIdentifier('cmis_policy');
$cmisRelationship        = new Sql\TableIdentifier('cmis_relationship');
$cmisFolder              = new Sql\TableIdentifier('cmis_folder');

$queries = ['tables' => [
    $cmisMetaTypes,
    $cmisMetaProperties,
    $cmisMetaTypesProperties,
    $cmisObject,
    $cmisDocument,
    $cmisPolicy,
    $cmisRelationship,
    $cmisFolder,
]];
$queries['queries'] = [        
        $sql->dropTable($cmisMetaTypes)->ifExists(true),
        $sql->createTable($cmisMetaTypes)
            ->addColumn(new Column\Varchar('id',             255, false))
            ->addColumn(new Column\Varchar('localName',      255, false))
            ->addColumn(new Column\Varchar('localNamespace', 255, true))
            ->addColumn(new Column\Varchar('queryName',      255, false))
            ->addColumn(new Column\Varchar('displayName',    255, false))
            ->addColumn(new Column\Varchar('baseId',         255, false))
            ->addColumn(new Column\Varchar('parentId',       255, true))
            ->addColumn(new Column\Varchar('description',    255, true))
            ->addColumn(new Column\Boolean('creatable',                false))
            ->addColumn(new Column\Boolean('fileable',                 false))
            ->addColumn(new Column\Boolean('queryable',                false))
            ->addColumn(new Column\Boolean('controllablePolicy',       false))
            ->addColumn(new Column\Boolean('controllableACL',          false))
            ->addColumn(new Column\Boolean('fulltextIndexed',          false))
            ->addColumn(new Column\Boolean('includedInSupertypeQuery', false))
            ->addColumn(new Column\Boolean('typeMutability_create',    false))
            ->addColumn(new Column\Boolean('typeMutability_update',    false))
            ->addColumn(new Column\Boolean('typeMutability_delete',    false))
            ->addColumn(new Column\Varchar('typeSpecific',    2048, false))
            ->addConstraint(new Constraint\PrimaryKey(['id'], 'PK')),

        $sql->dropTable($cmisMetaProperties)->ifExists(true),
        $sql->createTable($cmisMetaProperties)
            ->addColumn(new Column\Varchar('propertyId',     255, false))
            ->addColumn(new Column\Varchar('localName',      255, false))
            ->addColumn(new Column\Varchar('localNamespace', 255, false))
            ->addColumn(new Column\Varchar('queryName',      255, false))
            ->addColumn(new Column\Varchar('displayName',    255, false))
            ->addColumn(new Column\Varchar('description',    255, true))
            ->addColumn(new Column\Varchar('propertyType',   255, false))
            ->addColumn(new Column\Varchar('cardinality',    255, false))
            ->addColumn(new Column\Varchar('updatability',   255, false))
            ->addColumn(new Column\Boolean('required',       false))
            ->addColumn(new Column\Boolean('queryable',      false))
            ->addColumn(new Column\Boolean('orderable',      false))
            ->addColumn(new Column\Boolean('choices',        true))
            ->addColumn(new Column\Boolean('openChoice',     true))
            ->addColumn(new Column\Varchar('defaultValue',   255, true))
            ->addColumn(new Column\Varchar('typeSpecific',   2048,false))
            ->addConstraint(new Constraint\PrimaryKey(['propertyId'], 'PK')),

        $sql->dropTable($cmisMetaTypesProperties)->ifExists(true),
        $sql->createTable($cmisMetaTypesProperties)
            ->addColumn(new Column\Varchar('propertyId',     255, false))
            ->addColumn(new Column\Varchar('typeId',         255, false))
            ->addColumn(new Column\Boolean('inherited',      false))
            ->addColumn(new Column\Varchar('localName',      255, true))
            ->addColumn(new Column\Varchar('localNamespace', 255, true))
            ->addColumn(new Column\Varchar('defaultValue',   255, true))
            ->addConstraint(new Constraint\PrimaryKey(['propertyId', 'typeId'], 'PK')),

        $sql->dropTable($cmisObject)->ifExists(true),
        $sql->createTable($cmisObject)
            ->addColumn(new Column\Integer   ('cmis_objectId',               false, null,['identity' => true]))
            ->addColumn(new Column\Varchar   ('cmis_baseTypeId',             255,  false))
            ->addColumn(new Column\Varchar   ('cmis_objectTypeId',           255,  false))
            ->addColumn(new Column\Varchar   ('cmis_secondaryObjectTypeIds', 2048, true))
            ->addColumn(new Column\Varchar   ('cmis_name',                   255,  false))
            ->addColumn(new Column\Varchar   ('cmis_description',            255,  true))
            ->addColumn(new Column\Varchar   ('cmis_createdBy',              255,  true))
            ->addColumn(new Column\Time      ('cmis_creationDate',                 true))
            ->addColumn(new Column\Varchar   ('cmis_lastModifiedBy',         255,  true))
            ->addColumn(new Column\Date      ('cmis_lastModificationDate',         true))
            ->addColumn(new Column\BigInteger('cmis_changeToken',            true))
            ->addConstraint(new Constraint\PrimaryKey(['cmis_objectId'], 'PK')),

        $sql->dropTable($cmisDocument)->ifExists(true),
        $sql->createTable($cmisDocument)
            ->addColumn(new Column\Integer ('cmis_objectId', false))
            ->addColumn(new Column\Boolean ('cmis_isImmutable', true))
            ->addColumn(new Column\Boolean ('cmis_isLatestVersion', true))
            ->addColumn(new Column\Boolean ('cmis_isMajorVersion', true))
            ->addColumn(new Column\Boolean ('cmis_isLatestMajorVersion', true))
            ->addColumn(new Column\Boolean ('cmis_isPrivateWorkingCopy', true))
            ->addColumn(new Column\Varchar ('cmis_versionLabel', 255, true))
            ->addColumn(new Column\Integer ('cmis_versionSeriesId', true))
            ->addColumn(new Column\Integer ('cmis_isVersionSeriesCheckedOut', true))
            ->addColumn(new Column\Varchar ('cmis_versionSeriesCheckedOutBy', 255, true))
            ->addColumn(new Column\Varchar ('cmis_versionSeriesCheckedOutId', 255, true))
            ->addColumn(new Column\Varchar ('cmis_checkinComment', 255, true))
            ->addColumn(new Column\Varchar ('cmis_contentStreamLength', 255, true))
            ->addColumn(new Column\Varchar ('cmis_contentStreamMimeType', 255, true))
            ->addColumn(new Column\Varchar ('cmis_contentStreamFileName', 255, true))
            ->addColumn(new Column\Integer ('cmis_contentStreamId', true))
            ->addConstraint(new Constraint\PrimaryKey(['cmis_objectId'], 'PK')),

        $sql->dropTable($cmisFolder)->ifExists(true),
        $sql->createTable($cmisFolder)
            ->addColumn(new Column\Integer ('cmis_objectId', false))
            ->addColumn(new Column\Integer ('cmis_parentId', true))
            ->addColumn(new Column\Integer ('cmis_level', false))
            ->addColumn(new Column\Integer ('cmis_leftKey',false))
            ->addColumn(new Column\Integer ('cmis_rightKey',false))
            ->addColumn(new Column\Varchar ('cmis_path', 2048, true))
            ->addColumn(new Column\Varchar ('cmis_allowedChildObjectTypeIds', 255, false))
        ->addConstraint(new Constraint\PrimaryKey(['cmis_objectId'], 'PK')),

        $sql->dropTable($cmisRelationship)->ifExists(true),
        $sql->createTable($cmisRelationship)
            ->addColumn(new Column\Integer ('cmis_objectId', false))
            ->addColumn(new Column\Integer ('cmis_sourceId', false))
            ->addColumn(new Column\Integer ('cmis_targetId', false))
            ->addColumn(new Column\Varchar ('cmis_sourceTypeId', 255, false))
            ->addColumn(new Column\Varchar ('cmis_targetTypeId', 255, false))
            ->addConstraint(new Constraint\PrimaryKey(['cmis_objectId'], 'PK')),

        $sql->dropTable($cmisPolicy)->ifExists(true),
        $sql->createTable($cmisPolicy)
            ->addColumn(new Column\Integer ('cmis_objectId', false))
            ->addColumn(new Column\Varchar ('cmis_policyText', 255, false))
            ->addConstraint(new Constraint\PrimaryKey(['cmis_objectId'], 'PK')),

        $sql->insert($cmisMetaTypes)
            ->columns(
                ['id',               'parentId', 'queryName',         'displayName',       'localName',         'localNamespace',    'baseId',            'description', 'creatable', 'fileable', 'queryable', 'controllablePolicy', 'controllableACL', 'fulltextIndexed', 'includedInSupertypeQuery', 'typeMutability_create', 'typeMutability_update', 'typeMutability_delete', 'typeSpecific']
            )
            ->values([
                ['cmis:document',     null,      'cmis:document',     'cmis:document',     'cmis_document',     'cmis_document',     'cmis:document',     '',             true,        true,       true,        false,                false,             false,             true,                       true,                    true,                    true,                   '{"versionable":null,"contentStreamAllowed":null}'],
                ['cmis:folder',       null,      'cmis:folder',       'cmis:folder',       'cmis_folder',       'cmis_folder',       'cmis:folder',       '',             true,        true,       true,        false,                false,             false,             true,                       true,                    true,                    true,                   '{}'],
                ['cmis:relationship', null,      'cmis:relationship', 'cmis:relationship', 'cmis_relationship', 'cmis_relationship', 'cmis:relationship', '',             true,        false,      true,        false,                false,             false,             true,                       true,                    true,                    true,                   '{"allowedSourceTypes":null,"allowedTargetTypes":null}'],
                ['cmis:policy',       null,      'cmis:policy',       'cmis:policy',       'cmis_policy',       'cmis_policy',       'cmis:policy',       '',             true,        true,       true,        false,                false,             false,             true,                       true,                    true,                    true,                   '{}'],
                ['cmis:item',         null,      'cmis:item',         'cmis:item',         'cmis_item',         'cmis_item',         'cmis:item',         '',             false,       true,       true,        false,                false,             false,             true,                       true,                    true,                    true,                   '{}'],
            ]),

        $sql->insert($cmisMetaProperties)
            ->columns(
                ['propertyId',                     'propertyType', 'localName',                      'localNamespace',    'queryName',                      'displayName',                   'description', 'cardinality', 'updatability', 'required', 'queryable', 'orderable', 'choices', 'openChoice', 'defaultValue']
            )
            ->values([
                // Common
                ['cmis:objectId',                  'xs:id',        'cmis_objectId',                  'cmis_object',       'cmis:objectId',                  'cmis:objectId',                  null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:baseTypeId',                'xs:id',        'cmis_baseTypeId',                'cmis_object',       'cmis:baseTypeId',                'cmis:baseTypeId',                null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:objectTypeId',              'xs:id',        'cmis_objectTypeId',              'cmis_object',       'cmis:objectTypeId',              'cmis:objectTypeId',              null,         'single',      'oncreate',      false,      true,        false,       null,      null,         null],
                ['cmis:secondaryObjectTypeIds',    'xs:id',        'cmis_secondaryObjectTypeIds',    'cmis_object',       'cmis:secondaryObjectTypeIds',    'cmis:secondaryObjectTypeIds',    null,         'multi',       'readwrite',     false,      true,        false,       null,      null,         null],
                ['cmis:name',                      'xs:string',    'cmis_name',                      'cmis_object',       'cmis:name',                      'cmis:name',                      null,         'single',      'readwrite',     true,       true,        false,       null,      null,         null],
                ['cmis:description',               'xs:string',    'cmis_description',               'cmis_object',       'cmis:description',               'cmis:description',               null,         'single',      'readwrite',     false,      true,        false,       null,      null,         null],
                ['cmis:createdBy',                 'xs:string',    'cmis_createdBy',                 'cmis_object',       'cmis:createdBy',                 'cmis:createdBy',                 null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:creationDate',              'xs:dateTime',  'cmis_creationDate',              'cmis_object',       'cmis:creationDate',              'cmis:creationDate',              null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:lastModifiedBy',            'xs:string',    'cmis_lastModifiedBy',            'cmis_object',       'cmis:lastModifiedBy',            'cmis:lastModifiedBy',            null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:lastModificationDate',      'xs:dateTime',  'cmis_lastModificationDate',      'cmis_object',       'cmis:lastModificationDate',      'cmis:lastModificationDate',      null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:changeToken',               'xs:string',    'cmis_changeToken',               'cmis_object',       'cmis:changeToken',               'cmis:changeToken',               null,         'single',      'readonly',      false,      false,       false,       null,      null,         null],
                // Document
                ['cmis:isImmutable',               'xs:boolean',   'cmis_isImmutable',               'cmis_document',     'cmis:isImmutable',               'cmis:isImmutable',               null,         'single',      'oncreate',      false,      true,        false,       null,      null,         null],
                ['cmis:isLatestVersion',           'xs:boolean',   'cmis_isLatestVersion',           'cmis_document',     'cmis:isLatestVersion',           'cmis:isLatestVersion',           null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:isMajorVersion',            'xs:boolean',   'cmis_isMajorVersion',            'cmis_document',     'cmis:isMajorVersion',            'cmis:isMajorVersion',            null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:isLatestMajorVersion',      'xs:boolean',   'cmis_isLatestMajorVersion',      'cmis_document',     'cmis:isLatestMajorVersion',      'cmis:isLatestMajorVersion',      null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:isPrivateWorkingCopy',      'xs:boolean',   'cmis_isPrivateWorkingCopy',      'cmis_document',     'cmis:isPrivateWorkingCopy',      'cmis:isPrivateWorkingCopy',      null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:versionLabel',              'xs:string',    'cmis_versionLabel',              'cmis_document',     'cmis:versionLabel',              'cmis:versionLabel',              null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:versionSeriesId',           'xs:id',        'cmis_versionSeriesId',           'cmis_document',     'cmis:versionSeriesId',           'cmis:versionSeriesId',           null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:isVersionSeriesCheckedOut', 'xs:id',        'cmis_isVersionSeriesCheckedOut', 'cmis_document',     'cmis:isVersionSeriesCheckedOut', 'cmis:isVersionSeriesCheckedOut', null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:versionSeriesCheckedOutBy', 'xs:string',    'cmis_versionSeriesCheckedOutBy', 'cmis_document',     'cmis:versionSeriesCheckedOutBy', 'cmis:versionSeriesCheckedOutBy', null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:versionSeriesCheckedOutId', 'xs:id',        'cmis_versionSeriesCheckedOutId', 'cmis_document',     'cmis:versionSeriesCheckedOutId', 'cmis:versionSeriesCheckedOutId', null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:checkinComment',            'xs:string',    'cmis_checkinComment',            'cmis_document',     'cmis:checkinComment',            'cmis:checkinComment',            null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:contentStreamLength',       'xs:integer',   'cmis_contentStreamLength',       'cmis_document',     'cmis:contentStreamLength',       'cmis:contentStreamLength',       null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:contentStreamMimeType',     'xs:string',    'cmis_contentStreamMimeType',     'cmis_document',     'cmis:contentStreamMimeType',     'cmis:contentStreamMimeType',     null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:contentStreamFileName',     'xs:string',    'cmis_contentStreamFileName',     'cmis_document',     'cmis:contentStreamFileName',     'cmis:contentStreamFileName',     null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:contentStreamId',           'xs:id',        'cmis_contentStreamId',           'cmis_document',     'cmis:contentStreamId',           'cmis:contentStreamId',           null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                // Folder
                ['cmis:parentId',                  'xs:id',        'cmis_parentId',                  'cmis_folder',       'cmis:parentId',                  'cmis:parentId',                  null,         'single',      'oncreate',      true,       true,        false,       null,      null,         null],
                ['cmis:path',                      'xs:string',    'cmis_path',                      'cmis_folder',       'cmis:path',                      'cmis:path',                      null,         'single',      'readonly',      false,      true,        false,       null,      null,         null],
                ['cmis:allowedChildObjectTypeIds', 'xs:id',        'cmis_allowedChildObjectTypeIds', 'cmis_folder',       'cmis:allowedChildObjectTypeIds', 'cmis:allowedChildObjectTypeIds', null,         'multi',       'readonly',      false,      false,       false,       null,      null,         null],
                //Folder Repository Specific
                ['cmis:level',                     'xs:integer',   'cmis_level',                     'cmis_folder',       'cmis:level',                     'cmis:level',                     null,         'single',      'readonly',      false,      false,       false,       null,      null,         null],
                ['cmis:leftKey',                   'xs:integer',   'cmis_leftKey',                   'cmis_folder',       'cmis:leftKey',                   'cmis:leftKey',                   null,         'single',      'readonly',      false,      false,       false,       null,      null,         null],
                ['cmis:rightKey',                  'xs:integer',   'cmis_rightKey',                  'cmis_folder',       'cmis:rightKey',                  'cmis:rightKey',                  null,         'single',      'readonly',      false,      false,       false,       null,      null,         null],
                // Relationship
                ['cmis:sourceId',                  'xs:id',        'cmis_sourceId',                  'cmis_relationship', 'cmis:sourceId',                  'cmis:sourceId',                  null,         'single',      'oncreate',      true,       true,        false,       null,      null,         null],
                ['cmis:targetId',                  'xs:id',        'cmis_targetId',                  'cmis_relationship', 'cmis:targetId',                  'cmis:targetId',                  null,         'single',      'oncreate',      true,       true,        false,       null,      null,         null],
                ['cmis:sourceTypeId',              'xs:id',        'cmis_sourceTypeId',              'cmis_relationship', 'cmis:sourceTypeId',              'cmis:sourceTypeId',              null,         'single',      'oncreate',      false,      true,        false,       null,      null,         null],
                ['cmis:targetTypeId',              'xs:id',        'cmis_targetTypeId',              'cmis_relationship', 'cmis:targetTypeId',              'cmis:targetTypeId',              null,         'single',      'oncreate',      false,      true,        false,       null,      null,         null],
                // Policy
                ['cmis:policyText',                'xs:string',    'cmis_policyText',                'cmis_policy',       'cmis:policyText',                'cmis:policyText',                null,         'single',      'oncreate',      false,      true,        false,       null,      null,         null],
            ]),
        $sql->insert($cmisMetaTypesProperties)
            ->columns(
                ['typeId',            'propertyId',                    'inherited', 'localName', 'localNamespace', 'defaultValue']
            )
            ->values([
                ['cmis:document',     'cmis:objectId',                  false,       null,        null,             null],
                ['cmis:document',     'cmis:baseTypeId',                false,       null,        null,             'cmis:document'],
                ['cmis:document',     'cmis:objectTypeId',              false,       null,        null,             'cmis:document'],
                ['cmis:document',     'cmis:secondaryObjectTypeIds',    false,       null,        null,             null],
                ['cmis:document',     'cmis:name',                      false,       null,        null,             null],
                ['cmis:document',     'cmis:description',               false,       null,        null,             null],
                ['cmis:document',     'cmis:createdBy',                 false,       null,        null,             null],
                ['cmis:document',     'cmis:creationDate',              false,       null,        null,             null],
                ['cmis:document',     'cmis:lastModifiedBy',            false,       null,        null,             null],
                ['cmis:document',     'cmis:lastModificationDate',      false,       null,        null,             null],
                ['cmis:document',     'cmis:changeToken',               false,       null,        null,             null],
                ['cmis:document',     'cmis:isImmutable',               false,       null,        null,             null],
                ['cmis:document',     'cmis:isLatestVersion',           false,       null,        null,             null],
                ['cmis:document',     'cmis:isMajorVersion',            false,       null,        null,             null],
                ['cmis:document',     'cmis:isLatestMajorVersion',      false,       null,        null,             null],
                ['cmis:document',     'cmis:isPrivateWorkingCopy',      false,       null,        null,             null],
                ['cmis:document',     'cmis:versionLabel',              false,       null,        null,             null],
                ['cmis:document',     'cmis:versionSeriesId',           false,       null,        null,             null],
                ['cmis:document',     'cmis:isVersionSeriesCheckedOut', false,       null,        null,             null],
                ['cmis:document',     'cmis:versionSeriesCheckedOutBy', false,       null,        null,             null],
                ['cmis:document',     'cmis:versionSeriesCheckedOutId', false,       null,        null,             null],
                ['cmis:document',     'cmis:checkinComment',            false,       null,        null,             null],
                ['cmis:document',     'cmis:contentStreamLength',       false,       null,        null,             null],
                ['cmis:document',     'cmis:contentStreamMimeType',     false,       null,        null,             null],
                ['cmis:document',     'cmis:contentStreamFileName',     false,       null,        null,             null],
                ['cmis:document',     'cmis:contentStreamId',           false,       null,        null,             null],
                // Folder
                ['cmis:folder',       'cmis:objectId',                  false,       null,        null,             null],
                ['cmis:folder',       'cmis:baseTypeId',                false,       null,        null,             'cmis:folder'],
                ['cmis:folder',       'cmis:objectTypeId',              false,       null,        null,             'cmis:folder'],
                ['cmis:folder',       'cmis:secondaryObjectTypeIds',    false,       null,        null,             null],
                ['cmis:folder',       'cmis:name',                      false,       null,        null,             null],
                ['cmis:folder',       'cmis:description',               false,       null,        null,             null],
                ['cmis:folder',       'cmis:createdBy',                 false,       null,        null,             null],
                ['cmis:folder',       'cmis:creationDate',              false,       null,        null,             null],
                ['cmis:folder',       'cmis:lastModifiedBy',            false,       null,        null,             null],
                ['cmis:folder',       'cmis:lastModificationDate',      false,       null,        null,             null],
                ['cmis:folder',       'cmis:changeToken',               false,       null,        null,             null],
                ['cmis:folder',       'cmis:parentId',                  false,       null,        null,             null],
                ['cmis:folder',       'cmis:path',                      false,       null,        null,             null],
                ['cmis:folder',       'cmis:allowedChildObjectTypeIds', false,       null,        null,             null],
                // Folder Repository Specific
                ['cmis:folder',       'cmis:level',                     false,       null,        null,             null],
                ['cmis:folder',       'cmis:leftKey',                   false,       null,        null,             null],
                ['cmis:folder',       'cmis:rightKey',                  false,       null,        null,             null],
                // Relationship
                ['cmis:relationship', 'cmis:objectId',                  false,       null,        null,             null],
                ['cmis:relationship', 'cmis:baseTypeId',                false,       null,        null,             'cmis:relationship'],
                ['cmis:relationship', 'cmis:objectTypeId',              false,       null,        null,             'cmis:relationship'],
                ['cmis:relationship', 'cmis:secondaryObjectTypeIds',    false,       null,        null,             null],
                ['cmis:relationship', 'cmis:name',                      false,       null,        null,             null],
                ['cmis:relationship', 'cmis:description',               false,       null,        null,             null],
                ['cmis:relationship', 'cmis:createdBy',                 false,       null,        null,             null],
                ['cmis:relationship', 'cmis:creationDate',              false,       null,        null,             null],
                ['cmis:relationship', 'cmis:lastModifiedBy',            false,       null,        null,             null],
                ['cmis:relationship', 'cmis:lastModificationDate',      false,       null,        null,             null],
                ['cmis:relationship', 'cmis:changeToken',               false,       null,        null,             null],
                ['cmis:relationship', 'cmis:sourceId',                  false,       null,        null,             null],
                ['cmis:relationship', 'cmis:targetId',                  false,       null,        null,             null],
                ['cmis:relationship', 'cmis:sourceTypeId',              false,       null,        null,             null],
                ['cmis:relationship', 'cmis:targetTypeId',              false,       null,        null,             null],
                // Policy
                ['cmis:policy',       'cmis:objectId',                  false,       null,        null,             null],
                ['cmis:policy',       'cmis:baseTypeId',                false,       null,        null,             'cmis:policy'],
                ['cmis:policy',       'cmis:objectTypeId',              false,       null,        null,             'cmis:policy'],
                ['cmis:policy',       'cmis:secondaryObjectTypeIds',    false,       null,        null,             null],
                ['cmis:policy',       'cmis:name',                      false,       null,        null,             null],
                ['cmis:policy',       'cmis:description',               false,       null,        null,             null],
                ['cmis:policy',       'cmis:createdBy',                 false,       null,        null,             null],
                ['cmis:policy',       'cmis:creationDate',              false,       null,        null,             null],
                ['cmis:policy',       'cmis:lastModifiedBy',            false,       null,        null,             null],
                ['cmis:policy',       'cmis:lastModificationDate',      false,       null,        null,             null],
                ['cmis:policy',       'cmis:changeToken',               false,       null,        null,             null],
                ['cmis:policy',       'cmis:policyText',                false,       null,        null,             null],
                // Item
                ['cmis:item',         'cmis:objectId',                  false,       null,        null,             null],
                ['cmis:item',         'cmis:baseTypeId',                false,       null,        null,             'cmis:item'],
                ['cmis:item',         'cmis:objectTypeId',              false,       null,        null,             'cmis:item'],
                ['cmis:item',         'cmis:secondaryObjectTypeIds',    false,       null,        null,             null],
                ['cmis:item',         'cmis:name',                      false,       null,        null,             null],
                ['cmis:item',         'cmis:description',               false,       null,        null,             null],
                ['cmis:item',         'cmis:createdBy',                 false,       null,        null,             null],
                ['cmis:item',         'cmis:creationDate',              false,       null,        null,             null],
                ['cmis:item',         'cmis:lastModifiedBy',            false,       null,        null,             null],
                ['cmis:item',         'cmis:lastModificationDate',      false,       null,        null,             null],
                ['cmis:item',         'cmis:changeToken',               false,       null,        null,             null],
            ]),

        //===   InsertRoot folder   ================================================
        $sql->insert($cmisObject)->values([
                'cmis_objectId'             => 1,
                'cmis_baseTypeId'           => 'cmis:folder',
                'cmis_objectTypeId'         => 'cmis:folder',
                'cmis_name'                 => '/',
                'cmis_description'          => 'root folder',
                'cmis_createdBy'            => 'robot',
                'cmis_creationDate'         => null,
                'cmis_lastModifiedBy'       => 'robot',
                'cmis_lastModificationDate' => null,
            ]),
        $sql->insert($cmisFolder)->values([
                'cmis_objectId' => 1,
                'cmis_parentId' => null,
                'cmis_path'     => '/',
                'cmis_level'    => 0,
                'cmis_leftKey'  => 1,
                'cmis_rightKey' => 2,
            ]),
];

$cmisRepositoryInfo      = new Sql\TableIdentifier('cmis:repository_info');

$queries['tables'][] = $cmisRepositoryInfo;

$queries['queries'][] = $sql->dropTable($cmisRepositoryInfo)->ifExists(true);
$queries['queries'][] = $sql->createTable($cmisRepositoryInfo)
            ->addColumn(new Column\Varchar('repositoryId',   255,  false))
            ->addColumn(new Column\Varchar('key',            255,  false))
            ->addColumn(new Column\Text   ('value',          2048, true));

$queries['queries'][] = $sql->insert($cmisRepositoryInfo)
            ->columns(
                ['repositoryId', 'key',                  'value']
            )
            ->values([
                ['0',            'name',                 'cmis_repository'],
                ['0',            'description',          'cmis_repository'],
                ['0',            'vendorName',           'turrsis'],
                ['0',            'productName',          ''],
                ['0',            'productVersion',       '0.0'],
                ['0',            'rootFolderId',         1],
                ['0',            'latestChangeLogToken', ''],
                ['0',            'cmisVersionSupported', '1.1'],
                ['0',            'thinClientURI',        ''],
                ['0',            'changesIncomplete',    ''],
                ['0',            'changesOnType',        ''],
                ['0',            'supportedPermissions', ''],
                ['0',            'propagation',          ''],
                ['0',            'permissions',          ''],
                ['0',            'mapping',              ''],
                ['0',            'principalAnonymous',   ''],
                ['0',            'principalAnyone',      ''],
                ['0',            'extendedFeatures',     ''],
                ['0',            'capabilities',         json_encode([
                    'getDescendants' => false,
                    'getFolderTree'  => false,
                    'orderBy'        => 'none',
                    //Object
                    'contentStreamUpdatability'   => 'none',
                    'changes'                     => 'none',
                    'renditions'                  => 'none',
                    // Filing
                    'multiﬁling'              => false,
                    'unﬁling'                 => false,
                    'versionSpeciﬁcFiling'    => false,
                    //Versioning
                    'PWCUpdatable'            => false,
                    'PWCSearchable'           => false,
                    'AllVersionsSearchable'   => false,
                    //Query
                    'query'            => 'none',
                    'join'           => 'none',
                    //Type
                    'creatablePropertyTypes'      => false,
                    'newTypeSettableAttributes'   => array(
                        'id'                        => false,
                        'localName'                 => false,
                        'localNamespace'            => false,
                        'displayName'               => false,
                        'queryName'                 => false,
                        'description'               => false,
                        'creatable'                 => false,
                        'fileable'                  => false,
                        'queryable'                 => false,
                        'fulltextIndexed'           => false,
                        'includedInSupertypeQuery'  => false,
                        'controllablePolicy'        => false,
                        'controllableACL'           => false,
                    ),
                    //ACL
                    'ACL'                     => 'none',
                ])],
            ]);

return $queries;