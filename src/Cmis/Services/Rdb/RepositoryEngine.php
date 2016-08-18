<?php
namespace Cmis\Cmis\Services\Rdb;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;

use Zend\Db\Sql\Ddl\Column;
use Cmis\Cmis\Capabilities\RepositoryInfo;
use Zend\Db\Sql\Predicate as SqlPredicates;

use Cmis\Cmis\Exception as CmisExceptions;

class RepositoryEngine extends AbstractEngine
{
    protected $commonTypeProperties   = array(
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
    );
    protected $commonTypeAttributes   = array(
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
    );
    
    public function __construct(Adapter $adapter, Sql\Builder\Builder $sqlBuilder, array $config)
    {
        parent::__construct($adapter, $sqlBuilder);
    }

    protected function getAllProperties()
    {
        return array_column(
            $this->executeSql(new Sql\Select('cmis:meta_properties'))->toArray(),
            'localNamespace',
            'propertyId'
        );
    }

    public function createType($type)
    {
        $parentType = $this->getTypeDefinition($type['parentId']);
        
        
        $queries = array();
        //get only new properties for new type
        $propertyDefinitions = array_diff_key($type['propertyDefinitions'], $parentType['propertyDefinitions']);
        unset($type['propertyDefinitions']);
        $typeSpecific = array_diff_key($type, $this->commonTypeAttributes);
        $type         = array_diff_key($type, $typeSpecific);

        $type['typeSpecific'] = json_encode($typeSpecific);
        
        if (!isset($type['queryName'])) {
            $type['queryName'] = $type['id'];
        }
        if (!isset($type['displayName'])) {
            $type['displayName'] = $type['queryName'];
        }

        // Check type attributes
        foreach($this->commonTypeAttributes as $metaKey => $metaAttribute) {
            if (!array_key_exists($metaKey, $type)) {
                if ($metaAttribute['required']) {
                    if ($metaAttribute['defaultValueInParent'] && array_key_exists($metaKey, $parentType)) {
                        $type[$metaKey] = $parentType[$metaKey];
                    } else {
                        throw new \Exception("'$metaKey' attribute is required");
                    }
                } elseif ($metaAttribute['defaultValueInParent'] && array_key_exists($metaKey, $parentType)) {
                    $type[$metaKey] = $parentType[$metaKey];
                }
            }
        }
        
        
        if (!isset($type['localName'])) {
            $type['localName'] = str_replace(':', '_', $type['queryName']);
        }
        if (!isset($type['localNamespace'])) {
            $type['localNamespace'] = str_replace(':', '_', $type['queryName']);
        }
        
        
        $queries['insertType'] = new Sql\Insert('cmis:meta_types');
        $queries['insertType']->values($type);
        
        $queries['createTypeTable'] = new Sql\Ddl\CreateTable($type['localName']);
        $queries['createTypeTable']->addColumn(new Sql\Ddl\Column\Integer ('cmis_objectId'));
        
        // map inherited properies
        foreach($parentType['propertyDefinitions'] as $propertyId => $property) {
            if (is_numeric($propertyId) && isset($property['propertyId'])) {
                $propertyId = $property['propertyId'];
            }
            $property['inherited'] = true;
            $map = $this->mapProperty($type, $property);
            $queries['mapProperty_' . $propertyId] = $map['link'];
        }
        // map new properies and create new properties
        $allProperties = $this->getAllProperties();
        foreach($propertyDefinitions as $propertyId => $property) {
            if (is_numeric($propertyId) && isset($property['propertyId'])) {
                $propertyId = $property['propertyId'];
            }
            $property['propertyId'] = $propertyId;
            $property = $this->normalizeProperty($type, $property);
            if (!isset($allProperties[$propertyId])) {
                $queries['newProperty_' . $propertyId] = $this->createProperty($property);
            }
            $property['inherited'] = false;
            $map = $this->mapProperty($type, $property);
            $queries['mapProperty_' . $propertyId] = $map['link'];
            $queries['createTypeTable']->addColumn($map['column']);
        }
        
        
        try {
            $this->adapter->beginTransaction();
            foreach($queries as $name => $query) {
                $this->executeSql($query);
            }
            $this->adapter->commitTransaction();
            return $this->getTypeDefinition($type['id']);
        } catch (\Exception $ex) {
            $this->adapter->rollbackTransaction();
            throw $ex;
        }
    }
    
    public function createProperty($property)
    {
        $insertProperty = new Sql\Insert('cmis:meta_properties');
        return $insertProperty->values($property);
    }
    
    public function mapProperty($type, $property)
    {
        $linkQuery = new Sql\Insert('cmis:meta_types_properties');
        $linkQuery->values(array(
            'typeId'         => $type['id'],
            'propertyId'     => $property['propertyId'],
            'inherited'      => $property['inherited'],
            'localName'      => isset($property['localName']) ? $property['localName'] : str_replace(':', '_', $property['queryName']),
            'localNamespace' => isset($property['localNamespace']) ? $property['localNamespace'] : $type['localName'],
            'defaultValue'   => isset($property['defaultValue']) ? $property['defaultValue'] : null,
        ));
        return array(
            'link'   => $linkQuery,
            'column' => $this->factoryDdlColumn($property),
        );
    }
    
    public function updateType($updatedType)
    {
        $existedType = $this->getTypeDefinition($updatedType['id']);
        
        $queries = array();
        $attributes = array();
        foreach($this->commonTypeAttributes as $metaKey => $metaAttribute) {
            if ($metaAttribute['updatability'] == 'readwrite') {
                if (isset($updatedType[$metaKey]) && $updatedType[$metaKey] != $existedType[$metaKey]) {
                    $attributes[$metaKey] = $updatedType[$metaKey];
                }
            }
        }
        if ($attributes) {
            $queries['updateAttributes'] = new Sql\Update('cmis:meta_types');
            $queries['updateAttributes']->set($attributes);
        }
        
        $updateProperties = $this->updateTypeProperties($updatedType, $existedType);
        if ($updateProperties) {
            $queries = array_merge($queries, $updateProperties);
        }
        

        
        try {
            $this->adapter->beginTransaction();
            foreach($queries as $name => $query) {
                $this->executeSql($query);
            }
            $this->adapter->commitTransaction();
        } catch (\Exception $ex) {
            $this->adapter->rollbackTransaction();
            throw $ex;
        }
        return $this->getTypeDefinition($updatedType['id']);
    }
    
    protected function updateTypeProperties($updatedType, $existedType)
    {
        $allProperties = $this->getAllProperties();
        $queries = array();
        
        $deletedProperties = array();
        $updatedProperties = [];
        foreach($updatedType['propertyDefinitions'] as $pKey => $p) {
            if (isset($p['propertyId'])) {
                $pKey = $p['propertyId'];
            } else {
                $p['propertyId'] = $pKey;
            }
            $updatedProperties[$pKey] = $p;
        }
        $existedProperties = [];
        foreach($existedType['propertyDefinitions'] as $pKey => $p) {
            if (isset($p['propertyId'])) {
                $pKey = $p['propertyId'];
            } else {
                $p['propertyId'] = $pKey;
            }
            $existedProperties[$pKey] = $p;
        }
        //$updatedProperties = array_column($updatedType['propertyDefinitions'], null, 'propertyId');
        //$existedProperties = array_column($existedType['propertyDefinitions'], null, 'propertyId');
        $alterTable        = new Sql\Ddl\AlterTable($existedType['localName']);
        
        foreach($updatedProperties as $propertyId => $property) {
            if (isset($property['cmd.delete']) && $property['cmd.delete']) { // delete property
                $propertyId = isset($property['propertyId'])
                    ? $property['propertyId']
                    : $property['id'];
                $alterTable->dropColumn($existedProperties[$propertyId]['localName']);
                $deletedProperties[] = $propertyId;
                continue;
            }

            if (isset($existedProperties[$propertyId])) {
                // updateProperty
                $propertySet = array(); 
                if (array_key_exists('defaultValue', $property)) {
                    $propertySet['defaultValue'] = $property['defaultValue'];
                }
                if (!$propertySet) {
                    continue;
                }

                $updateMapProperty = new Sql\Update('cmis:meta_types_properties');
                $queries['updateMapProperty_' . $propertyId] = $updateMapProperty
                    ->set($propertySet)
                    ->where(array(
                        'typeId'     => $updatedType['id'],
                        'propertyId' => $propertyId
                    ));
                continue;
            }
            // new Property
            $property = $this->normalizeProperty($existedType, $property);
            if (!isset($allProperties[$propertyId])) {
                $queries['newProperty_' . $propertyId] = $this->createProperty($property);
            }
            $property['inherited'] = false;
            $map = $this->mapProperty($existedType, $property);
            $queries['mapProperty_' . $propertyId] = $map['link'];
            $alterTable->addColumn($map['column']);
            continue;
        }
        
        if ($deletedProperties) {
            $queries['unlinkProperties'] = new Sql\Delete('cmis:meta_types_properties');
            $queries['unlinkProperties']->where(array(
                'typeId'=>$updatedType['id'],
                new SqlPredicates\In('propertyId', $deletedProperties)
            ));
        }

        if ($alterTable->addColumns || $alterTable->dropColumns) {
            $queries['alterTable'] = $alterTable;
        }
        return $queries;
    }
    
    protected function normalizeProperty($type, $property)
    {
        $property['queryName']      = isset($property['queryName'])   ? $property['queryName']   : $property['propertyId'];
        $property['displayName']    = isset($property['displayName']) ? $property['displayName'] : $property['queryName'];
        $property['localName']      = isset($property['localName'])   ? $property['localName']   : str_replace(':', '_', $property['queryName']);
        $property['localNamespace'] = $type['localName'];
        // Check property
        foreach($this->commonTypeProperties as $metaKey=>$metaAttribute) {
            if (!array_key_exists($metaKey, $property) && $metaAttribute['required']) {
                throw new \Exception("'$metaKey' attribute is required");
            }
        }
        return array_diff_key($property, array_diff_key($property, $this->commonTypeProperties));
    }

    public function deleteType($typeId)
    {
        $type = $this->getTypeDefinition($typeId);
        $typeLocalName = $type['localName'];

        $instancesCount = new Sql\Select($typeLocalName);
        $instancesCount
                ->from($typeLocalName)
                ->columns(['count' => new Sql\Predicate\Literal('COUNT(*)')])
                ->setPrefixColumnsWithTable(false);
        $instancesCount = $this->executeSqlScalar($instancesCount);
        if ($instancesCount != 0) {
            throw new CmisExceptions\Constraint(sprintf(
                '%s objects of "%s" type exist in the repository', 
                $instancesCount,
                $typeId
            ));
        }
        
        if ($this->getTypeChildren($typeId)) {
            throw new CmisExceptions\Constraint(sprintf(
                '"%s" type has a sub-type', 
                $typeId
            ));
        }
        
        $queries = [];
        $queries['meta_properties'] = new Sql\Delete();
        $queries['meta_properties']
                ->from('cmis:meta_properties')
                ->where(['localNamespace' => $typeLocalName]);
        
        $queries['meta_types_properties'] = new Sql\Delete();
        $queries['meta_types_properties']
                ->from('cmis:meta_types_properties')
                ->where(['typeId' => $typeId]);
        
        $queries['meta_types'] = new Sql\Delete();
        $queries['meta_types']
                ->from('cmis:meta_types')
                ->where(['id' => $typeId]);
        
        $queries['dropTable'] = new Sql\Ddl\DropTable($typeLocalName);
        
        $this->executeSql($queries);
    }

    public function getRepositories()
    {
        
    }

    public function getRepositoryInfo()
    {
        return new RepositoryInfo(array(
            'repositoryId'          => 0,
            'repositoryName'        => 'tgs_admin_site',
            'repositoryDescription' => 'A display description for the repository.',
            'rootFolderId'          => '1',

            'vendorName'            => 'A display name for the vendor of the repository’s underlying application.',
            'productName'           => 'A display name for the repository’s underlying application.',
            'productVersion'        => 'A display name for the version number of the repository’s underlying application.',
            'capabilities'          => array(
                'The set of values for the repository-optional capabilities speciﬁed in section 2.1.1.1 Optional Capabilities.',
            ),
            'latestChangeLogToken'  => 'The change log token corresponding to the most recent change event for any object in the repository. See section 2.1.15 Change Log.',
            'cmisVersionSupported'  => 'A Decimal as String that indicates what version of the CMIS speciﬁcation this repository supports as speciﬁed in section 2.1.1.2 Implementation Information. This value MUST be "1.1".',
            'thinClientURI'         => 'A optional repository-speciﬁc URI pointing to the repository’s web interface. MAY be not set.',
            'changesIncomplete'     => 'Indicates whether or not the repository’s change log can return all changes ever made to any object in the repository or only changes made after a particular point in time. Applicable when the repository’s optional capability capabilityChanges is not none. If FALSE, then the change log can return all changes ever made to every object. If TRUE, then the change log includes all changes made since a particular point in time, but not all changes ever made.',
            'changesOnType'         => array(
                'Indicates whether changes are available for base types in the repository. Valid values are from enumBaseObjectTypeIds. See section 2.1.15 Change Log. Note: The base type cmis:secondary MUST NOT be used here. Only primary base types can be in this list.',
                'cmis:document',
                'cmis:folder',
                'cmis:policy',
                'cmis:relationship',
                'cmis:item',
            ),
            'supportedPermissions'  => 'Enum : Speciﬁes which types of permissions are supported.',
            'propagation'           => 'Enum : The allowed value(s) for applyACL, which control how non-direct ACEs are handled by the repository. See section 2.1.12.3 ACL Capabilities.',
            'permissions'           => array(
                'The list of repository-speciﬁc permissions the repository supports for managing ACEs. See section 2.1.12 Access Control.'
             ),
            'mapping'               => array(
                'PermissionMapping : The list of mappings for the CMIS basic permissions to allowable actions. See section 2.1.12 Access Control.'
             ),
            'principalAnonymous'    => 'If set, this ﬁeld holds the principal who is used for anonymous access. This principal can then be passed to the ACL services to specify what permissions anonymous users should have.',
            'principalAnyone'       => 'If set, this ﬁeld holds the principal who is used to indicate any authenticated user. This principal can then be passed to the ACL services to specify what permissions any authenticated user should have.',
            'RepositoryFeatures'    => array(
                'Optional list of additional repository features. See section 2.1.1.3 Repository Features.'
             ),
        ));
    }

    public function getTypeDefinition($typeId)
    {
        $typeDefinition = new Sql\Select('cmis:meta_types');
        $typeDefinition->where(array('id' => $typeId));

        $typeDefinition = $this->executeSqlRow($typeDefinition);
        if (!$typeDefinition) {
            return null;
            throw new CmisExceptions\ObjectNotFound('type definition for "' . $typeId . '" not found');
        }
        $typeDefinition = $this->normalizeTypeAttributes($typeDefinition->getArrayCopy());
        
        $propertyDefinitions = new Sql\Select(array('p1' => 'cmis:meta_types_properties'));
        $propertyDefinitions
            ->columns(array(
                'propertyId',
                'inherited',                
                'inherited_internal' => new Sql\Predicate\IfPredicate(
                    new Sql\Predicate\Operator(
                        ['p2.localNamespace', 'identifier'],
                        "=", 
                        ['cmis_object', 'value']
                    ), 
                    [true, 'literal'], 
                    ['p1.inherited', 'identifier']
                ),
                'localName'          => new Sql\Predicate\IfPredicate(
                    new Sql\Predicate\IsNotNull('p1.localName'), 
                    array('p1.localName', 'identifier'), 
                    array('p2.localName', 'identifier')
                ),
                'localNamespace'     => new Sql\Predicate\IfPredicate(
                    new Sql\Predicate\IsNotNull('p1.localNamespace'), 
                    array('p1.localNamespace','identifier'), 
                    array('p2.localNamespace','identifier')
                ),
                'defaultValue'       => new Sql\Predicate\IfPredicate(
                    new Sql\Predicate\IsNotNull('p1.defaultValue'), 
                    array('p1.defaultValue','identifier'), 
                    array('p2.defaultValue','identifier')
                ),
            ))
            ->join(
                ['p2'=>'cmis:meta_properties'],
                new Sql\Predicate\Operator(
                    ['p1.propertyId', 'identifier'], 
                    "=", 
                    ['p2.propertyId', 'identifier']
                ),
                array(
                    'queryName',
                    'displayName',
                    'description',
                    'propertyType',
                    'cardinality',
                    'updatability',
                    'required',
                    'queryable',
                    'orderable',
                    'choices',
                    'openChoice',
                    'typeSpecific',
                 ),
                'left'
            )
            ->where(['p1.typeId' => $typeId]);
        
        $propertyDefinitions = $this->executeSql($propertyDefinitions)->toArray();
        $typeDefinition['propertyDefinitions'] = $this->normalizePropertyDefinitions($propertyDefinitions);
        return $typeDefinition;
    }


    public function getTypeChildren($typeId = null, $includePropertyDefinitions = false, $maxItems = null, $skipCount = null)
    {
        $children = new Sql\Select('cmis:meta_types');
        $children->columns(array('id'))->where(array('parentId' => $typeId));
        $children = $this->executeSql($children);

        $result = array();
        foreach($children as $child) {
            $child = $this->getTypeDefinition($child['id']);
            $result[$child['queryName']] = $child;
        }
        return $result;
    }

    public function getTypeDescendants($typeId = null, $depth = null, $includePropertyDefinitions = false)
    {
        $children = $this->getTypeChildren($typeId, $includePropertyDefinitions);
        foreach($children as &$child) {
            if ($childs = $this->getTypeDescendants($child['id'], $depth, $includePropertyDefinitions)) {
                $child['children'] = $childs;
            }
        }
        return $children;
    }
    
    protected function factoryDdlColumn($property)
    {
        $column = null;
        switch($property['propertyType']) {
            case 'xs:boolean'  : $column = new Column\Boolean   ($property['localName']);      break;
            case 'xs:dateTime' : $column = new Column\Integer   ($property['localName']);      break;
            case 'xs:decimal'  : $column = new Column\Decimal   ($property['localName'], 10);  break;
            case 'xs:html'     : $column = new Column\Varchar   ($property['localName'], 255); break;
            case 'xs:id'       : $column = new Column\Varchar   ($property['localName'], 255); break;
            case 'xs:integer'  : $column = new Column\BigInteger($property['localName']);      break;
            case 'xs:string'   : $column = new Column\Varchar   ($property['localName'], 255); break;
            case 'xs:uri'      : $column = new Column\Varchar   ($property['localName'], 255); break;
            default :
                throw new \Exception('unknown property type');
        }
        return $column;
    }
    
    protected function normalizeTypeAttributes($type)
    {
        if (array_key_exists('typeSpecific', $type)) {
            if ($type['typeSpecific']) {
                $type = array_merge($type, json_decode($type['typeSpecific'], true));
            }
            unset($type['typeSpecific']);
        }
        foreach($type as $attribute => &$value) {
            if (false !== array_search($attribute, array(
                'creatable',
                'fileable',
                'queryable',
                'controllablePolicy',
                'controllableACL',
                'fulltextIndexed',
                'includedInSupertypeQuery',
                'typeMutability.create',
                'typeMutability.update',
                'typeMutability.delete',
                'versionable',
                'contentStreamAllowed'))) {
                $value = $value !== null ? (bool)$value : $value;
            }
        }
        return $type;
    }

    protected function normalizePropertyDefinitions($propertyDefinitions) 
    {
        $result = array();
        foreach($propertyDefinitions as $k => $property) {
            if (array_key_exists('typeSpecific', $property)) {
                if ($property['typeSpecific']) {
                    $property = array_merge($property, json_decode($property['typeSpecific'], true));
                }
                unset($property['typeSpecific']);
            }
            foreach($property as $attribute => &$value) {
                if (false !== array_search($attribute, array(
                    'required',
                    'queryable',
                    'orderable',
                    'inherited',
                    'inherited_internal',
                    ))) {
                    $value = $value !== null ? (bool)$value : $value;
                }
            }
            $result[$property['queryName']] = $property;
        }
        return $result;
    }

    protected function normalizePropertyValues($property)
    {
        $meta = array('localName', 'localNamespace', 'queryName', 'displayName', 'description', 'propertyType', 'cardinality', 'updatability', 'required', 'queryable', 'orderable', 'choices', 'openChoice', 'defaultValue', 'typeSpecific');
        $res = array();
        foreach($meta as $k) {
            if (isset($property[$k])) {
                $res[$k] = $property[$k];
            } else {
                $res[$k] = null;
            }
        }
        if (!$res['localName']) {
            $res['localName'] = $res['queryName'];
        }
        if (!$res['displayName']) {
            $res['localName'] = $res['queryName'];
        }
        return $res;
    }
}
