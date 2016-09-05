<?php
namespace Turrsis\Cmis\Services\Rdb;

use Zend\Db\ResultSet\ResultSet;
use Turrsis\Cmis\Utils\ArrayUtils;
use Zend\Stdlib\ArrayTreeIterator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;
use Turrsis\Cmis\Exception as CmisExceptions;
use Zend\Db\Sql\Helper\Tree\NestedSet;

class ObjectsEngine extends AbstractEngine
{
    /**
     * @var RepositoryEngine */
    protected $repositoryEngine;

    /**
     * @var SequrityEngine */
    protected $sequrityEngine;

    protected $commonObjectTable = 'cmis_object';
    protected $commonObjectIdField = 'cmis_objectId';
    protected $commonObjectProperties;

    protected $folderTreeHelper;

    public function __construct(Adapter $adapter, array $config)
    {
        parent::__construct($adapter);

        if (isset($config['common_table'])) {
            $this->commonObjectTable      = $config['common_table'];
        }
        // common_object_properties
        /*$select = new Sql\Select('cmis:meta_properties');
        $select
            ->columns(array('propertyId', 'localName'))
            ->where(array('localNamespace'=>'cmis_object'));
        $this->commonObjectProperties = array_column(
            $this->executeSql($select)->toArray(),
            'localName', 
            'propertyId'
        );
        
        $this->commonObjectIdField = $this->commonObjectProperties['cmis:objectId'];*/
    }

    public function setRepositoryEngine($repositoryEngine)
    {
        $this->repositoryEngine = $repositoryEngine;
        return $this;
    }

    /**
     * @return RepositoryEngine */
    public function getRepositoryEngine()
    {
        return $this->repositoryEngine;
    }

    public function setSequrityEngine($sequrityEngine)
    {
        $this->sequrityEngine = $sequrityEngine;
        return $this;
    }
    
    /**
     * @return SequrityEngine */
    public function getSequrityEngine()
    {
        return $this->sequrityEngine;
    }

    protected function getFolderTreeHelper()
    {
        if ($this->folderTreeHelper) {
            return $this->folderTreeHelper;
        }
        $folderType = $this->repositoryEngine->getTypeDefinition('cmis:folder');
        return $this->folderTreeHelper = new NestedSet(
            $this->adapter,
            $folderType['localName'],
            [
                'id'     => $folderType['propertyDefinitions']['cmis:objectId']['localName'],
                'parent' => $folderType['propertyDefinitions']['cmis:parentId']['localName'],
                'left'   => $folderType['propertyDefinitions']['cmis:leftKey']['localName'],
                'right'  => $folderType['propertyDefinitions']['cmis:rightKey']['localName'],
                'level'  => $folderType['propertyDefinitions']['cmis:level']['localName'],
                'name'   => $folderType['propertyDefinitions']['cmis:name']['localName'],
                'path'   => $folderType['propertyDefinitions']['cmis:path']['localName'],
            ]
        );
    }

    protected function getCommonObjectProperties($property = null)
    {
        if ($this->commonObjectProperties === null) {
            $select = new Sql\Select('cmis:meta_properties');
            $select
                ->columns(array('propertyId', 'localName'))
                ->where(array('localNamespace'=>'cmis_object'));
            $this->commonObjectProperties = array_column(
                $this->executeSql($select)->toArray(),
                'localName', 
                'propertyId'
            );

            $this->commonObjectIdField = $this->commonObjectProperties['cmis:objectId'];
        }
        if (!$property) {
            return $this->commonObjectProperties;
        }
        return $this->commonObjectProperties[$property];
    }

    public function getObject($objectId, $optional = array())
    {
        $filter = isset($optional['filter']) ? $optional['filter'] : array();
        $object = array(
            'properties' => $this->getProperties($objectId, $filter, $optional)
        );
        if ($object['properties'] === null) {
            return null;
        }

        if (isset($optional['includeRelationships'])) {
            if (is_array($optional['includeRelationships'])) {
                $relationshipOptions = $optional['includeRelationships'];
            } else {
                $relationshipOptions = array(
                    'relationshipDirection' => $optional['includeRelationships'],
                );
            }
            $object['relationships'] = $this->getObjectRelationships($objectId, $relationshipOptions);
        }

        if (isset($optional['includePolicyIds']) && $optional['includePolicyIds']) {
            $object['policies'] = $this->sequrityEngine->getAppliedPolicies($objectId);
        }

        if (isset($optional['includeAllowableActions'])) {
            $object['allowableActions'] = $this->getAllowableActions($objectId);
        }
        if (isset($optional['includeACL'])) {
            $object['acl'] = $this->sequrityEngine->getACL($objectId);
        }
        if (isset($optional['renditionFilter'])) {
            $object['renditions'] = $this->getRenditions($objectId, array(
                'renditionFilter' => $optional['renditionFilter']
            ));
        }
        return $object;
    }

    public function getObjectByPath($path, $optional = array())
    {
        $forceNotFoundException = array_key_exists('forceObjectNotFoundException', $optional) && $optional['forceObjectNotFoundException'] == false;

        $folder = $this->executeSqlRow($this->_selectFolderByPartOfPath($path));
        
        if ($folder === null) {
            if ($forceNotFoundException) {
                return null;
            }
            throw new CmisExceptions\ObjectNotFound("object with path '$path' not found");
        }
        //======================================================================
        $folderPathLen = strlen($folder['path']);
        if (strlen($path) == $folderPathLen) {
            $objectId = $folder['id'];
        } else {
            $objectName = trim(substr($path, $folderPathLen), '/');
            if (stripos($objectName, '/') !== false) {
                if ($forceNotFoundException) {
                    return null;
                }
                throw new CmisExceptions\ObjectNotFound($path);
            }
            $objectId = $this->executeSqlScalar($this->_selectObjectIdByNameInFolder(
                $folder['id'],
                $objectName
            ));
            if (!$objectId) {
                throw new CmisExceptions\ObjectNotFound("object with path '$path' not found");
            }
        }
        return $this->getObject($objectId, $optional);
    }

    public function getProperties($objectId, $filter = null) 
    {
        $objectType = $this->_getTypeDefinitionByObject($objectId);
        
        $query = $this->_selectLeftObjectByType($objectType, $filter)
                ->where(array(
                    $this->commonObjectTable . '.' . $this->commonObjectIdField => $objectId
                ));

        $object = $this->executeSqlRow($query);
        if ($object !== null) {
            return $object->getArrayCopy();
        }
    }

    public function createObject($object, array $optional = array())
    {
        $objectType = $this->repositoryEngine->getTypeDefinition(
            isset($object['cmis:objectTypeId'])
                ? $object['cmis:objectTypeId']
                : $optional['cmis:objectTypeId']
        );
        if (!$objectType) {
            throw new CmisExceptions\InvalidArgument(sprintf(
                'type "%s" not exists',
                isset($object['cmis:objectTypeId']) ? $object['cmis:objectTypeId'] : $optional['cmis:objectTypeId']
            ));
        }
        $sysProperties = array();

        if (isset($optional['folderId']) && stripos($optional['folderId'], '/') === 0) {
            $optional['folderId'] = $this->getObjectByPath($optional['folderId'])['properties']['cmis:objectId'];
        }
        if ($objectType['id'] == 'cmis:relationship') {
            $object['cmis:sourceTypeId'] = $this->_getTypeDefinitionByObject($object['cmis:sourceId'])['id'];
            $object['cmis:targetTypeId'] = $this->_getTypeDefinitionByObject($object['cmis:targetId'])['id'];
        }
        if ($objectType['baseId'] == 'cmis:folder') {
            $parentFolder = $this->getProperties($optional['folderId']);
            $object['cmis:parentId'] = $optional['folderId'];            
            $object['cmis:path']     = rtrim($parentFolder['cmis:path'], '/') . '/' . $object['cmis:name'];
            
            $sysProperties[] = 'cmis:path';
        }
        
        $queries = $this->_insertObject($objectType, $object, $sysProperties);

        if ($objectType['baseId'] == 'cmis:folder') {
            $folderLocalName = 'cmis_folder'; // $objectType['baseId']
            $folderTreeQueries = $this->getFolderTreeHelper()->insertNode(
                $optional['folderId'],
                null,
                isset($queries[$folderLocalName]) ? $queries[$folderLocalName] : null
            );

            $queries[$folderLocalName] = $folderTreeQueries;
            unset($optional['folderId']);
        }
        
        try {
            $this->adapter->beginNestedTransaction();
            foreach($queries as $query) {
                $this->executeSql($query, 'execute');
            }
            //$this->adapter->query($queries, 'execute');
            $objectId = $this->executeSqlScalar($this->_selectLastInsertedId());

            if (isset($optional['folderId'])) {
                $this->createObject(array(
                    'cmis:objectTypeId' => 'cmis:relationship',
                    'cmis:name'      => "relation from {$optional['folderId']} to $objectId",
                    'cmis:sourceId' => $optional['folderId'],
                    'cmis:targetId' => $objectId,
                ));
            }
            
            $this->adapter->commitNestedTransaction();
            return $objectId;
        } catch (\Exception $ex) {
            $this->adapter->rollbackNestedTransaction();
            throw $ex;
        }
    }
    
    public function addObjectToFolder($objectId, $folderId, $allVersions = true)
    {
        $folder = $this->getProperties($folderId);
        if ($folder['cmis:baseTypeId'] != 'cmis:folder') {
            throw new CmisExceptions\InvalidArgument(sprintf(
                '$folderId should be type of "cmis:folder" %s given.',
                $folder['cmis:baseTypeId'])
            );
        }
        return $this->createObject(array(
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:name'         => "relation from $folderId to $objectId",
            'cmis:sourceId'     => $folderId,
            'cmis:targetId'     => $objectId,
        ));
    }

    public function deleteObject($objectId, $allVersions = true)
    {
        $object     = $this->getProperties($objectId);
        $objectType = $this->repositoryEngine->getTypeDefinition($object['cmis:objectTypeId']);
        if ($objectType['queryName'] == 'cmis:folder') {
            throw new CmisExceptions\Constraint(__FUNCTION__ . "() function can't delete folders");
        }

        $queries     = array();
        foreach($objectType['propertyDefinitions'] as $property) {
            $propertyTable = $property['localNamespace'];
            if (isset($queries[$propertyTable])) {
                continue;
            }
            $queries[$propertyTable] = new Sql\Delete($propertyTable);
            $queries[$propertyTable]->where(array($this->commonObjectIdField => $objectId));
        }

        
        if ($objectType['id'] != 'cmis:relationship') {
            // delete relations
            $relationType = $this->repositoryEngine->getTypeDefinition('cmis:relationship');
            $relWhere = new Sql\Predicate\PredicateSet(array(
                new Sql\Predicate\Operator($relationType['propertyDefinitions']['cmis:sourceId']['localName'], '=', $objectId),
                new Sql\Predicate\Operator($relationType['propertyDefinitions']['cmis:targetId']['localName'], '=', $objectId),
            ), 'OR');

            $selectRelations = new Sql\Select($relationType['localName']);
            $selectRelations->columns(array($this->commonObjectIdField))->where($relWhere);

            $queries[$this->commonObjectTable]->where(
                new Sql\Predicate\In(
                    $relationType['propertyDefinitions']['cmis:objectId']['localName'], 
                    $selectRelations
                ),
                'OR'
            );

            $queries['deleteObjectRelations'] = new Sql\Delete($relationType['localName']);
            $queries['deleteObjectRelations']->where($relWhere, 'OR');
        }
        
        try {
            $this->adapter->beginNestedTransaction();
            $this->executeSql($queries);
            $this->adapter->commitNestedTransaction();
            return $objectId;
        } catch (\Exception $ex) {
            $this->adapter->rollbackNestedTransaction();
            throw $ex;
        }
    }

    public function deleteTree($folderId, $optional = array())
    {
        $deletedIds = $this->executeSql($this->_selectIdsForDeleteTree(
            $folderId, 
            $optional
        ));
        $objectsIdByType = array();
        foreach($deletedIds as $id) {
            $objectsIdByType[$id['cmis_objectTypeId']][$id['cmis_objectId']] = true;
        }
        
        $tablesAndIds = array();
        foreach($objectsIdByType as $type=>$ids) {
            $type = $this->repositoryEngine->getTypeDefinition($type);
            foreach($type['propertyDefinitions'] as $property) {
                $propertyTable = $property['localNamespace'];
                $tablesAndIds[$propertyTable] = array_replace(
                    isset($tablesAndIds[$propertyTable]) ? $tablesAndIds[$propertyTable] : array(), 
                    $ids
                );
            }
        }
        
        $failedToDelete = array();
        foreach($tablesAndIds as $table => $ids) {
            $delete = new \Zend\Db\Sql\Delete($table);
            $delete->where(new \Zend\Db\Sql\Predicate\In('cmis_objectId', array_keys($ids)));
            try {
                $this->executeSql($delete);
            } catch (\Exception $ex) {
                $failedToDelete[] = $ids;
            }
        }
        
        return $failedToDelete;
    }
    
    public function updateProperties($properties, $changeToken = null) 
    {
        $queries = array();
        $objectId = $properties['cmis:objectId'];
        $type = $this->_getTypeDefinitionByObject($objectId);
        
        switch($type['baseId']) {
            case 'cmis:folder' :
                $treeHelper = $this->getFolderTreeHelper();
                if (isset($properties['cmis:parentId'])) {
                    $moveQueries = $treeHelper->moveNode($objectId, $properties['cmis:parentId'], null);
                    unset($properties['cmis:parentId']);
                } elseif (isset($properties['cmis:beforeId'])) {
                    $moveQueries = $treeHelper->moveNode($objectId, null, $properties['cmis:beforeId']);
                    unset($properties['cmis:beforeId']);
                }
                if ($moveQueries) {
                    $queries['moveFolder'] = $moveQueries;
                }
                if (isset($properties['cmis:path'])) {
                    unset($properties['cmis:path']);
                }
                break;
            case 'cmis:relationship' :
                if (isset($properties['cmis:sourceId'])) {
                    $properties['cmis:sourceTypeId'] = $this->_getTypeDefinitionByObject($properties['cmis:sourceId'])['id'];
                }
                if (isset($properties['cmis:targetId'])) {
                    $properties['cmis:targetTypeId'] = $this->_getTypeDefinitionByObject($properties['cmis:targetId'])['id'];
                }
                break;
        }

        $propertiesByTables = array();
        foreach($type['propertyDefinitions'] as $name=>$property) {
            if (isset($properties[$name]) && $property['updatability'] == 'readwrite') {
                $propertiesByTables[$property['localNamespace']][$property['localName']] = $properties[$name];
            }
        }

        foreach($propertiesByTables as $table=>$fields) {
            $queries[$table] = new Sql\Update();
            $queries[$table]
                    ->table($table)
                    ->set($fields)
                    ->where(array($this->commonObjectIdField => $objectId));
        }
        try {
            $this->adapter->beginNestedTransaction();
            $this->executeSql($queries);
            $this->adapter->commitNestedTransaction();
            return $objectId;
        } catch (\Exception $ex) {
            $this->adapter->rollbackNestedTransaction();
            throw $ex;
        }
    }
    
    protected function bulkUpdateProperties($objectIdAndChangeToken, $optional = array())
    {
        
    }

    public function moveObject($objectId, $targetFolderId, $sourceFolderId)
    {
        $relationType = $this->repositoryEngine->getTypeDefinition('cmis:relationship');
        $targetFolder = $this->getObject($targetFolderId);
        $query = new Sql\Update($relationType['localName']);
        $query
            ->set(array(
                $relationType['propertyDefinitions']['cmis:sourceId']['localName'] => $targetFolder['properties']['cmis:objectId'],
                $relationType['propertyDefinitions']['cmis:sourceTypeId']['localName'] => $targetFolder['properties']['cmis:objectTypeId'],
            ))
            ->where(array(
                $relationType['propertyDefinitions']['cmis:sourceId']['localName'] => $sourceFolderId,
                $relationType['propertyDefinitions']['cmis:targetId']['localName'] => $objectId,
            ));
        
        $this->executeSql($query);
        
    }
    
    public function getObjectRelationships($objectId, $optional = array())
    {
        $typeId    = isset($optional['typeId']) ? (array)$optional['typeId'] : null;
        $filter    = isset($optional['filter']) ? $optional['filter'] : null;
        
        $relationType = $this->repositoryEngine->getTypeDefinition('cmis:relationship');

        $sourceIdField     = $relationType['localName'] . '.' . $relationType['propertyDefinitions']['cmis:sourceId']['localName'];
        $targetIdField     = $relationType['localName'] . '.' . $relationType['propertyDefinitions']['cmis:targetId']['localName'];
        $sourceTypeIdField = $relationType['localName'] . '.' . $relationType['propertyDefinitions']['cmis:sourceTypeId']['localName'];
        $targetTypeIdField = $relationType['localName'] . '.' . $relationType['propertyDefinitions']['cmis:targetTypeId']['localName'];

        
        $select = $this->_selectLeftObjectByType($relationType, $filter);
        $relationshipDirection = isset($optional['relationshipDirection'])
                ? $optional['relationshipDirection']
                : 'source';
        switch($relationshipDirection) {
            case 'target' :
                $select->where(new Sql\Predicate\Operator($targetIdField, '=', $objectId), 'AND');
                if ($typeId) {
                    $select->where->in($sourceTypeIdField, $typeId);
                }
                break;
            case 'either' :
                $target = new Sql\Predicate\PredicateSet(array(
                    new Sql\Predicate\Operator($targetIdField, '=', $objectId),
                ), 'AND');
                $source = new Sql\Predicate\PredicateSet(array(
                    new Sql\Predicate\Operator($sourceIdField, '=', $objectId),
                ), 'AND');

                if ($typeId) {
                    $target->addPredicate(new Sql\Predicate\In($sourceTypeIdField, $typeId), 'AND');
                    $source->addPredicate(new Sql\Predicate\In($targetTypeIdField, $typeId), 'AND');
                }
                $select->where(array(
                    $target,
                    $source,
                ), 'OR');
                break;
            case 'source' :
                $select->where(new Sql\Predicate\Operator($sourceIdField, '=', $objectId), 'AND');
                if ($typeId) {
                    $select->where->in($targetTypeIdField, $typeId);
                }
                break;
            default :
                throw new CmisExceptions\InvalidArgument(sprintf(
                    'value %s of $relationshipDirection is not allowed',
                    $relationshipDirection
                ));
        }
        return $this->executeSql($select)->toArray();
    }

    public function getFolderParent($folderId, $filter = array())
    {
        $folder = $this->getProperties($folderId);
        return $this->getObject(
            $folder['cmis:parentId'],
            array(
                'filter' => $filter
            )
        );
    }

    public function getChildren($folderId, $optional = array())
    {
        if (stripos($folderId, '/') !== false) {
            $folderId = $this->getObjectByPath($folderId)['properties']['cmis:objectId'];
        }
        
        $relations = $this->getObjectRelationships($folderId, array(
            'relationshipDirection' => 'source'
        ));

        foreach($relations as &$relation) {
            $relation = $this->getObject($relation['cmis:targetId'], $optional);
        }

        return array(
            'objects'      => $relations,
            'hasMoreItems' => false,
            'numItems'     => count($relations),
        );
    }

    public function getDescendants($folderId, $optional = array())
    {
        if (stripos($folderId, '/') !== false) {
            $folderId = $this->repository->getObjectService()->getObjectByPath($folderId)['properties']['cmis:objectId'];
        }
        throw new \Exception('not Implemented');
    }

    public function getFolderTree($folderId, $optional = array())
    {
        if (stripos($folderId, '/') !== false) {
            $folderId = $this->getObjectByPath($folderId)['properties']['cmis:objectId'];
        }
        $folderType = $this->repositoryEngine->getTypeDefinition('cmis:folder');
        $folderType['children'] = $this->repositoryEngine->getTypeDescendants('cmis:folder');
        
        $query = $this->getFolderTreeHelper()->selectChilds($folderId, array(
            'depth'        => (isset($optional['depth']) ? $optional['depth'] : 2),
            'exclude_root' => true,
            'select'       => $this->_selectFullObject($folderType),
        ));

        $resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSetPrototype->buffer();
        $folderTree = $this->adapter->query($query, 'execute', $resultSetPrototype);
        if (!$folderTree->count()) {
            return array();
        }
        return ArrayUtils::iteratorToNestedArray(
            $folderTree,
            'cmis:level',
            'childs'
        );
    }

    public function removeObjectFromFolder($objectId, $folderId = null)
    {
        $relationType = $this->repositoryEngine->getTypeDefinition('cmis:relationship');
        $relationTable = $relationType['localName'];
        $relationSourceId = $relationType['propertyDefinitions']['cmis:sourceId']['localName'];
        $relationSourceTypeId = $relationType['propertyDefinitions']['cmis:sourceTypeId']['localName'];
        $relationTargetId = $relationType['propertyDefinitions']['cmis:targetId']['localName'];
        
        $folderTypes = new Sql\Select();
        $folderTypes
            ->from('cmis:meta_types')
            ->columns(['id'])
            ->where(['baseId'=>'cmis:folder']);

        $relationsWhere = new Sql\Where();
        $ppp = [
            new Sql\Predicate\In($relationSourceTypeId, $folderTypes),
            $relationTargetId => $objectId,
        ];
        $relationsWhere->addPredicates($ppp);
        if ($folderId !== null) {
            $relationsWhere->addPredicates([
                $relationSourceId => $folderId
            ]);
        }

        $relations = new Sql\Select();
        $relations
            ->columns([$this->commonObjectIdField])
            ->from($relationTable)
            ->where($relationsWhere);
        
        
        $deleteObject = new Sql\Delete($this->commonObjectTable);
        $deleteObject->where(new Sql\Predicate\In($this->commonObjectIdField, $relations));
        
        
        $deleteRelation = new Sql\Delete($relationTable);
        $deleteRelation->where($relationsWhere);
        
        
        $this->executeSql([
            $deleteObject,
            $deleteRelation
        ]);
    }

    public function query($statement, $optional = array())
    {
        $useFullViews  = isset($optional['useFullViews']) ? (bool)$optional['useFullViews'] : true;
        $adapterPlatform = $this->adapter->getPlatform();

        $statement = preg_replace_callback(
            "/(cmis:\w*)(?:\s[aA][sS]\s(\w*)\s)*/",
            function ($cmisKey) use ($adapterPlatform, $useFullViews) {
                $key = $cmisKey[1];
                $alias = isset($cmisKey[2]) ? $cmisKey[2] : $cmisKey[1];
                $res = '';
                if ($useFullViews && ($type = $this->repositoryEngine->getTypeDefinition($key)) !== null) {
                    $typeQuery = $this->_selectLeftObjectByType($type);
                    $typeQuery = $this->adapter->getSqlBuilder()->buildSqlString($typeQuery, $this->adapter);
                    $typeQuery = '(' . $typeQuery . ') as ' . $adapterPlatform->quoteIdentifier($alias);
                    return $typeQuery;
                }
                return $adapterPlatform->quoteIdentifier($key);
            },
            $statement
        );

        $resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $rst = $this->adapter->query($statement, 'execute', $resultSetPrototype);
        $showDataOnly  = isset($optional['showDataOnly']) ? (bool)$optional['showDataOnly'] : false;
        if ($showDataOnly) {
            return $rst->toArray();
        } else {
            $queryResults = array();
            foreach($rst as $row) {
                $queryResults[] = array(
                    'properties'       => $row,
                    'allowableActions' => array(),
                    'relationships'    => array(),
                    'renditions'       => array(),
                );
            }
            return array(
                'queryResults' => $queryResults,
                'hasMoreItems' => false,
                'numItems'     => count($queryResults),
            );
        }
    }

    public function getCheckedOutDocs($optional = array())
    {
        
    }

    public function getObjectParents($objectId, $optional = array())
    {
        
    }

    public function getContentStream($objectId, $streamId = null) {
        
    }

    public function appendContentStream($objectId, $contentStream, $optional = array()) {
        
    }

    public function setContentStream($objectId, $contentStream, $optional = array()) {
        
    }
    
    public function deleteContentStream($objectId, $changeToken = null) {
        
    }
    
    public function getRenditions($objectId, $optional = array()) {
        
    }
    
    public function getAllowableActions($objectId) {
        
    }
    
    public function _selectObject($filter = null)
    {
        $select = new Sql\Select($this->commonObjectTable);
        if (!$filter) {
            return $select->columns($this->getCommonObjectProperties());
        }
        if (is_string($filter)) {
            $filter = explode(';', $filter);
        }

        $filter = array_flip($filter);
        foreach($filter as $alias => &$field) {
            $field = $this->getCommonObjectProperties($alias);
        }
        return $select->columns($filter);
    }
    
    public function _selectLeftObjectByType($type, $filter = array())
    {
        $tables = array(
            $this->commonObjectTable => array(),
        );
        foreach($type['propertyDefinitions'] as $property) {
            if ($filter && array_search($property['queryName'], $filter) === false) {
                continue;
            }
            $tables[$property['localNamespace']][$property['queryName']] = $property['localName'];
        }

        $query = $this->_selectObject(array_keys(array_shift($tables)));
        
        foreach($tables as $tableName => $tableColumns) {
            $query->join(
                $tableName,
                new Sql\Predicate\Operator(
                        [$this->commonObjectTable . '.' . $this->commonObjectIdField, 'identifier'],
                        "=", 
                        [$tableName . '.' . $this->commonObjectIdField, 'identifier']),
                $tableColumns,
                '' //'inner' //'left'
            );
        }
        return $query;
    }

    public function _selectRightObject($type, $options = array())
    {
        $typeQueryName = $type['queryName'];
        $typeLocalName = $type['localName'];
        if (isset($options['skip_types']) && array_search($typeQueryName, $options['skip_types']) !== false) {
            return null;
        }

        $typeQuery = null;
        if (!isset($options['left_type']) || $options['left_type'] != $typeQueryName) {
            $columns = array();
            foreach($type['propertyDefinitions'] as $property) {
                if ($typeLocalName != $property['localNamespace']) {
                    continue;
                }
                $columns[$property['queryName']] = $property['localName'];
            }
            if ($columns) {
                $columns['cmis:objectId'] = $this->commonObjectIdField;
                foreach($columns as $alias => $column) {
                    $columns[$alias] = $typeQueryName . '.' . $column;
                }
                $typeQuery = new Sql\Select([$typeQueryName => $type['localName']]);
                $typeQuery
                        ->columns($columns)
                        ->setPrefixColumnsWithTable(false);
            }
        }
        if (!isset($type['children']) || !$type['children']) {
            return $typeQuery;
        }

        
        $childrenCombine = new Sql\Combine();
        foreach($type['children'] as $child) {
            if ($child = $this->_selectRightObject($child, $options)) {
                $childrenCombine->union($child);
            }
        }
        $childrenCombine->alignColumns();

        if ($typeQuery == null) {
            return $childrenCombine;
        }

        $childrenAlias     = str_replace(':', '_', $typeQueryName) . '_Childs';
        $typeQuery->join(
            array($childrenAlias => $childrenCombine),
            new Sql\Predicate\Operator(
                ["$childrenAlias.cmis:objectId", 'identifier'],
                "=", 
                [$typeQueryName . "." . $this->commonObjectIdField, 'identifier']
            ),
            array(),
            'left'
        );

        $childsColumns = $childrenCombine->columns;
        foreach($childsColumns as $alias => $column) {
            $childsColumns[$alias] = $childrenAlias . '.' . $alias;
        }
        $childsColumns = array_replace($childsColumns, $typeQuery->columns);

        $typeQuery->columns($childsColumns, false);

        return $typeQuery;
    }

    public function _selectFullObject($type, $options = array())
    {
        $selectLeft = $this->_selectLeftObjectByType($type);

        $options['left_type'] = $type['queryName'];
        $selectRight = $this->_selectRightObject($type, $options);

        if (!$selectRight) {
            return $selectLeft;
        }

        $rightColumns = $selectRight->columns;
        if (isset($rightColumns['cmis:objectId'])) {
            unset($rightColumns['cmis:objectId']);
        }

        $selectLeft->join(
            array('rightQuery' => $selectRight),
            new Sql\Predicate\Operator(
                [$selectLeft->table->getSource()->getTable() . '.' . $this->commonObjectIdField, 'identifier'],
                "=",
                ['rightQuery.cmis:objectId', 'identifier']
            ),
            array_keys($rightColumns),
            $type['creatable'] ? 'left' : ''
        );
        return $selectLeft;
    }

    public function _selectFolderByPartOfPath($path)
    {
        $folderType = $this->repositoryEngine->getTypeDefinition('cmis:folder');

        $cmisPathLocalName     = $folderType['propertyDefinitions']['cmis:path']['localName'];
        $cmisObjectIdLocalName = $folderType['propertyDefinitions']['cmis:objectId']['localName'];
        
        $pathDir = implode('/', array_slice(explode('/', $path), 0, -1));
        
        $select = new Sql\Select($folderType['localName']);
        $select
            ->columns(array(
                'id'   => $cmisObjectIdLocalName,
                'path' => $cmisPathLocalName,
            ))
            ->where(new Sql\Predicate\PredicateSet(array(
                new Sql\Predicate\Operator($cmisPathLocalName, '=', $path),
                new Sql\Predicate\Operator($cmisPathLocalName, '=', $pathDir),
            ), 'OR'))
            ->order($cmisPathLocalName . ' DESC')
            ->limit(1);
        return $select;
    }

    public function _selectObjectIdByNameInFolder($folderId, $objectName)
    {
        $relationshipType = $this->repositoryEngine->getTypeDefinition('cmis:relationship');

        $targetIdLocalName   = $relationshipType['propertyDefinitions']['cmis:targetId']['localName'];
        $sourceIdLocalName   = $relationshipType['propertyDefinitions']['cmis:sourceId']['localName'];

        $select = new Sql\Select(array('obj' => $this->commonObjectTable));
        $select
            ->columns(array())
            ->join(
                array('rel' => $relationshipType['localName']),
                new Sql\Predicate\Operator(
                        ['rel.'.$targetIdLocalName, 'identifier'],
                        '=', 
                        ['obj.'.$this->commonObjectIdField, 'identifier']
                ),
                array($targetIdLocalName)
            )
            ->where(array(
                'rel.'.$sourceIdLocalName                   => $folderId,
                //'rel.'.$targetIdLocalName                   => $folderId,
                'obj.'.$this->getCommonObjectProperties('cmis:name') => $objectName,
            ));
        return $select;
    }
    
    protected function _selectIdsForDeleteTree($folderId, $optional = array())
    {
        $relationType  = $this->repositoryEngine->getTypeDefinition('cmis:relationship');
        $sourceIdField = $relationType['propertyDefinitions']['cmis:sourceId']['localName'];
        $targetIdField = $relationType['propertyDefinitions']['cmis:targetId']['localName'];        

        // Select folder and it subfolders
        $delete_Objects = $this->getFolderTreeHelper()
                ->selectChilds($folderId)
                ->columns(array($this->commonObjectIdField));

        switch(isset($optional['unfileObjects']) ? $optional['unfileObjects'] : 'delete') {
            case 'delete' :
                $delete_LinkedObjects = new Sql\Select($relationType['localName']);
                $delete_LinkedObjects
                    ->columns(array($this->commonObjectIdField => $targetIdField))
                    ->where(new Sql\Predicate\In(
                        $sourceIdField,
                        $delete_Objects
                    ));
                break;
            case 'deletesinglefiled' :
                $delete_LinkedObjects = new Sql\Select($relationType['localName']);
                $delete_LinkedObjects
                    ->columns(array($this->commonObjectIdField => $targetIdField))
                    ->where(new Sql\Predicate\In(
                        $sourceIdField,
                        $this->getFolderTreeHelper()
                            ->selectChilds($folderId, array('depth' => 1))
                            ->columns(array($this->commonObjectIdField))
                    ));
                break;
            case 'unfile' :
                $delete_LinkedObjects = null;
                break;
            default :
                throw new CmisExceptions\InvalidArgument('optional unfileObjects MUST be "delete" or "unfile" or "deletesinglefiled"');
        }
        
        if ($delete_LinkedObjects) {
            $delete_Objects = new Sql\Select(array('xxxxx' => new Sql\Combine(array(
                $delete_Objects,
                $delete_LinkedObjects,
            ))));
        }
        
        $delete_Relations = new Sql\Select($relationType['localName']);
        $delete_Relations
            ->columns(array($this->commonObjectIdField))
            ->where(new Sql\Predicate\PredicateSet(array(
                new Sql\Predicate\In($sourceIdField, $delete_Objects),
                new Sql\Predicate\In($targetIdField, $delete_Objects),
            ), 'OR'));

        $folderType             = $this->_getTypeDefinitionByObject($folderId);
        $delete_ObjectsWithType = new Sql\Select($this->commonObjectTable);
        $delete_ObjectsWithType
            ->quantifier('DISTINCT')
            ->columns(array(
                $this->commonObjectIdField,
                $folderType['propertyDefinitions']['cmis:objectTypeId']['localName']
            ))
            ->where(new Sql\Predicate\In(
                $this->commonObjectIdField,
                new Sql\Select(array(
                    'deletedIds' => new Sql\Combine(array(
                        $delete_Objects,
                        $delete_Relations,
                    ))
                ))
            ));
        return $delete_ObjectsWithType;
    }
    
    protected function _selectLastInsertedId($table = null)
    {
        $table = $table ?: $this->commonObjectTable;
        $query = new Sql\Select($table);
        return $query->columns(array(
            'id' => new Sql\Predicate\LastInsertedId($table),
        ));
    }

    protected function _insertObject($type, $properties, $sysProperties = array())
    {
        $propertiesByTables = array($this->commonObjectTable => array());
        $sysProperties = array_flip($sysProperties);
        foreach($type['propertyDefinitions'] as $name => $property) {
            if (isset($sysProperties[$name])) {
                $updatability = true;
            } else {
                $updatability = $property['updatability'] == 'readwrite' || $property['updatability'] == 'oncreate';
            }
            $propertyOwnerName = $property['localNamespace'];
            if (isset($properties[$name]) && $updatability) {
                $propertiesByTables[$propertyOwnerName][$property['localName']] = $properties[$name];
            } elseif($property['defaultValue'] !== null) {
                $propertiesByTables[$propertyOwnerName][$property['localName']] = $property['defaultValue'];
            } elseif (!isset($propertiesByTables[$propertyOwnerName])) {
                $propertiesByTables[$propertyOwnerName] = array();
            }
        }

        $queries = array();
        foreach($propertiesByTables as $table=>$fields) {
            if ($table != $this->commonObjectTable) {
                $fields[$this->commonObjectIdField] = new Sql\Predicate\LastInsertedId($this->commonObjectTable);
            }
            $query = new Sql\Insert($table);
            $queries[$table] = $query->values($fields);
        }
        return $queries;
    }
    
    public function _getTypeDefinitionByObject($object)
    {
        if (is_array($object)) {
            $typeId = $object['cmis:objectTypeId'];
        } elseif (stripos($object, '/') === 0) {
            $object = $this->getObjectByPath($object);
            $typeId = $object['properties']['cmis:objectId'];
        } else {
            $typeId = $object;
        }
        
        $object = $this->executeSqlRow($this->_selectObject()->where(array($this->commonObjectIdField => $typeId)));

        return $this->repositoryEngine->getTypeDefinition($object['cmis:objectTypeId']);
    }
}