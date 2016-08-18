<?php
namespace CmisTest\Cmis\Cmis\Services\Rdb;

use Cmis\Cmis\Repository;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;

abstract class AbstractEngineTest extends \PHPUnit_Framework_TestCase
{
    public static $dbConfig = 'Cmis';
    /**
     * @var Adapter
     */
    public static $dbAdapter = 'mysqli';
    
    protected static $commonRelationshipProperties = array();
    
    /**
     * @var Repository */
    protected static $repository;
    
    protected static $expectedPredefined = array(
        'commonProperties' => array(
            'cmis:objectId'               => null,
            'cmis:baseTypeId'             => null,
            'cmis:objectTypeId'           => null,
            'cmis:secondaryObjectTypeIds' => null,
            'cmis:name'                   => null,
            'cmis:description'            => null,

            'cmis:createdBy'              => null,
            'cmis:creationDate'           => null,
            'cmis:lastModificationDate'   => null,
            'cmis:lastModifiedBy'         => null,
            'cmis:changeToken'            => null,
        ),
        'documentProperties' => array(
            'cmis:checkinComment'            => null,
            'cmis:contentStreamFileName'     => null,
            'cmis:contentStreamId'           => null,
            'cmis:contentStreamLength'       => null,
            'cmis:contentStreamMimeType'     => null,
            'cmis:isImmutable'               => null,
            'cmis:isLatestMajorVersion'      => null,
            'cmis:isLatestVersion'           => null,
            'cmis:isMajorVersion'            => null,
            'cmis:isPrivateWorkingCopy'      => null,
            'cmis:isVersionSeriesCheckedOut' => null,
            'cmis:versionLabel'              => null,
            'cmis:versionSeriesCheckedOutBy' => null,
            'cmis:versionSeriesCheckedOutId' => null,
            'cmis:versionSeriesId'           => null,
        ),
    );

    protected static function createRepository()
    {
        return new Repository(array(
            'adapter'           => static::$dbAdapter,
            'sql_builder'       => new \Zend\Db\Sql\Builder\Builder(),
            'common_table'      => 'cmis_object',
            'repository_engine' => 'Cmis\Cmis\Services\Rdb\RepositoryEngine',
            'sequrity_engine'   => 'Cmis\Cmis\Services\Rdb\SequrityEngine',
            'objects_engine'    => 'Cmis\Cmis\Services\Rdb\ObjectsEngine',
        ));
    }
    
    protected static function createRepositoryTypes(Repository $repository)
    {
        $repository->getRepositoryService()->createType(array(
            'id'                       => 'cmis:folder1',
            'parentId'                 => 'cmis:folder',
            'queryName'                => 'cmis:folder1',
            'displayName'              => 'cmis:folder1',
            'description'              => '',
            'creatable'                => true,
            'fileable'                 => true,
            'queryable'                => true,
            'controllablePolicy'       => false,
            'controllableACL'          => false,
            'fulltextIndexed'          => false,
            'includedInSupertypeQuery' => true,
            'typeMutability_create'    => true,
            'typeMutability_update'    => true,
            'typeMutability_delete'    => true,
            'versionable'              => true,
            'contentStreamAllowed'     => true,
            'propertyDefinitions'      => array(
                'cmis:folder1Name' => array(
                    'propertyId'         => 'cmis:folder1Name',
                    'queryName'          => 'cmis:folder1Name',
                    'displayName'        => 'cmis:folder1Name',
                    'propertyType'       => 'xs:string',
                    'cardinality'        => 'single',
                    'updatability'       => 'readwrite',
                    'required'           => false,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ),
            ),
        ));
        $repository->getRepositoryService()->createType(array(
            'id'                       => 'cmis:folder2',
            'parentId'                 => 'cmis:folder1',
            'queryName'                => 'cmis:folder2',
            'displayName'              => 'cmis:folder2',
            'description'              => '',
            'creatable'                => true,
            'fileable'                 => true,
            'queryable'                => true,
            'controllablePolicy'       => false,
            'controllableACL'          => false,
            'fulltextIndexed'          => false,
            'includedInSupertypeQuery' => true,
            'typeMutability_create'    => true,
            'typeMutability_update'    => true,
            'typeMutability_delete'    => true,
            'versionable'              => true,
            'contentStreamAllowed'     => true,
            'propertyDefinitions'      => array(
                'cmis:folder2Name' => array(
                    'propertyId'         => 'cmis:folder2Name',
                    'queryName'          => 'cmis:folder2Name',
                    'displayName'        => 'cmis:folder2Name',
                    'propertyType'       => 'xs:string',
                    'cardinality'        => 'single',
                    'updatability'       => 'readwrite',
                    'required'           => false,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ),
            ),
        ));
        $repository->getObjectService()->createFolder(array(
            'cmis:name'         => 'folder_1',
        ), 1);
        $repository->getObjectService()->createFolder(array(
            'cmis:objectTypeId' => 'cmis:folder1',
            'cmis:name'         => 'folder_2',
        ), 1);
        $repository->getObjectService()->createFolder(array(
            'cmis:objectTypeId' => 'cmis:folder2',
            'cmis:name'        => 'folder_3',
        ), 1);
        
        $repository->getObjectService()->createDocument(array(
            'cmis:name'         => 'item_1',
        ));
        
        static::$commonRelationshipProperties = array_replace(self::$expectedPredefined['commonProperties'], array(
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:baseTypeId'   => 'cmis:relationship',
            'cmis:sourceTypeId' => 'cmis:folder',
            'cmis:targetTypeId' => 'cmis:document',
        ));
    }
    
    protected function _getObjectsIdSnapshot(array $excludeIds = [])
    {
        $meta = new Sql\Select(array('p1' => 'cmis:meta_types_properties'));
        $meta
            ->quantifier('DISTINCT')
            ->columns(array(
                'localNamespace' => new Sql\Predicate\IfPredicate(
                    new Sql\Predicate\IsNotNull('p1.localNamespace'),
                    ['p1.localNamespace', 'identifier'],
                    ['p2.localNamespace', 'identifier']
                ),
            ))
            ->join(
                ['p2'=>'cmis:meta_properties'],
                new Sql\Predicate\Operator(
                    ['p1.propertyId','identifier'],
                    "=", 
                    ['p2.propertyId','identifier']
                ),
                array(),
                'left'
            );

        $snapshot = [];
        $builder = new Sql\Builder\Builder(self::$dbAdapter);
        foreach(self::$dbAdapter->query($builder->buildSqlString($meta), 'execute') as $object) {
            $localNS = $object['localNamespace'];
            $localNSIds = new Sql\Select(['ns' => $localNS]);
            $localNSIds->join(
                ['o'=>'cmis_object'],
                new Sql\Predicate\Operator(
                    ['o.cmis_objectId','identifier'],
                    "=", 
                    ['ns.cmis_objectId','identifier']
                ),
                ['*'],
                'left'
            );
            $localNSIds = self::$dbAdapter->query($builder->buildSqlString($localNSIds), 'execute')->toArray();
            $snapshot[$localNS] = array_column(
                $localNSIds,
                'cmis_name',
                'cmis_objectId' 
            );
        }

        foreach($excludeIds as $id) {
            foreach($snapshot as &$expectedIds) {
                if (isset($expectedIds[$id])) {
                    unset($expectedIds[$id]);
                }
            }
        }
        
        return $snapshot;
    }
}