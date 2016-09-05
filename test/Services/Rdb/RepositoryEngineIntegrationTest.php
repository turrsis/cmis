<?php
namespace TurrsisTest\Cmis\Cmis\Services\Rdb;

use Zend\Stdlib\ArrayUtils;
use TurrsisTest\Cmis\AbstractTest;

class RepositoryEngineIntegrationTest extends AbstractTest
{
    /**
     * @var \Turrsis\Cmis\Repository
     */
    protected $repository;

    /**
     * @param string $repositoryName
     * @param array $config
     * @return \Turrsis\Cmis\Services\Rdb\RepositoryEngine
     */
    public function getRepositoryEngine($repositoryName = 'cmis:repo:repo1', $config = [])
    {
        $config = ArrayUtils::merge(['services' => [
            'db:test' => self::$currentAdapter,
        ]], $config);
        $this->repository = $this->getServiceManager($config)->get($repositoryName);
        $repositoryService = $this->repository->getRepositoryService();
        return $this->readAttribute($repositoryService, 'repositoryEngine');
    }

    public function testCreateType()
    {
        //$this->initDb('mysql', null);
        self::initDatabase();
        $repositoryEngine = $this->getRepositoryEngine();

        $type1 = [
            'id'                       => 'cmis:item1',
            'parentId'                 => 'cmis:item',
            'queryName'                => 'cmis:item1',
            'displayName'              => 'cmis:item1',
            'description'              => '',
            'propertyDefinitions'      => [
                'cmis:property_11' => [
                    'defaultValue'       => null,
                    'description'        => null,
                    'propertyType'       => 'xs:id',
                    'cardinality'        => 'single',
                    'updatability'       => 'oncreate',
                    'required'           => true,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ],
            ]
        ];

        $object1 = $repositoryEngine->createType($type1);

        $this->assertArraySubset($type1, $object1);
        
        $metadata = new \Zend\Db\Metadata\Metadata(self::$currentAdapter);
        $typeTable = $metadata->getTable('cmis_item1');
        $typeTableColumns = $typeTable->getColumns();
        
        $this->assertCount(2, $typeTableColumns);
        $this->assertEquals('cmis_objectId',   $typeTableColumns[0]->getName());
        $this->assertEquals('cmis_property_11', $typeTableColumns[1]->getName());
        
        
        $type2 = [
            'id'                       => 'cmis:item2',
            'parentId'                 => 'cmis:item',
            'queryName'                => 'cmis:item2',
            'displayName'              => 'cmis:item2',
            'description'              => '',
            'propertyDefinitions'      => [
                'cmis:property_21' => [
                    'defaultValue'       => null,
                    'description'        => null,
                    'propertyType'       => 'xs:id',
                    'cardinality'        => 'single',
                    'updatability'       => 'oncreate',
                    'required'           => true,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ],
            ]
        ];
        $object2 = $repositoryEngine->createType($type2);
        $this->assertArraySubset($type2, $object2);
        
        $type3 = [
            'id'                       => 'cmis:item3',
            'parentId'                 => 'cmis:item2',
            'queryName'                => 'cmis:item3',
            'displayName'              => 'cmis:item3',
            'description'              => '',
            'propertyDefinitions'      => [
                'cmis:property_31' => [
                    'defaultValue'       => null,
                    'description'        => null,
                    'propertyType'       => 'xs:id',
                    'cardinality'        => 'single',
                    'updatability'       => 'oncreate',
                    'required'           => true,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ],
            ]
        ];
        $object3 = $repositoryEngine->createType($type3);
        $this->assertArraySubset($type3, $object3);
    }
    
    public function testGetTypeChildren()
    {
        $repositoryEngine = $this->getRepositoryEngine();

        $children = $repositoryEngine->getTypeChildren('cmis:item');
        $this->assertArraySubset(
            [
                'cmis:item1' => ['id' => 'cmis:item1'],
                'cmis:item2' => ['id' => 'cmis:item2'],
            ],
            $children
        );
    }

    public function testGetTypeDescendants()
    {
        $repositoryEngine = $this->getRepositoryEngine();

        $descendants = $repositoryEngine->getTypeDescendants('cmis:item');
        
        $this->assertCount(2, $descendants);
        $this->assertCount(1, $descendants['cmis:item2']['children']);
        $this->assertArrayNotHasKey('children', $descendants['cmis:item1']);
        $this->assertArraySubset(
            [
                'cmis:item1' => ['id' => 'cmis:item1'],
                'cmis:item2' => [
                    'id' => 'cmis:item2',
                    'children' => [
                        'cmis:item3' => ['id' => 'cmis:item3'],
                    ],
                ],
            ],
            $descendants
        );
        $this->assertEquals(
            [], 
            $repositoryEngine->getTypeDescendants('cmis:item1')
        );
    }

    public function testUpdateType()
    {
        $repositoryEngine = $this->getRepositoryEngine();

        $type = [
            'id'                       => 'cmis:item1',
            'parentId'                 => 'cmis:item',
            'queryName'                => 'cmis:item1',
            'displayName'              => 'cmis:item1_Updated',
            'description'              => '',
            'propertyDefinitions'      => [
                'cmis:property_11' => [ // existed property
                    'defaultValue'       => 'defaultValue_Updated',
                    'description'        => null,
                    'propertyType'       => 'xs:id',
                    'cardinality'        => 'single',
                    'updatability'       => 'oncreate',
                    'required'           => true,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ],
                'cmis:newUpdatedProperty' => [
                    'defaultValue'       => 'xxx',
                    'description'        => null,
                    'propertyType'       => 'xs:id',
                    'cardinality'        => 'single',
                    'updatability'       => 'oncreate',
                    'required'           => true,
                    'queryable'          => true,
                    'orderable'          => false,
                    'choices'            => null,
                    'openChoice'         => null,
                ],
            ]
        ];
        $updatedType = $repositoryEngine->updateType($type);
        $this->assertArraySubset($type, $updatedType);
    }

    public function testDeleteType()
    {
        $repositoryEngine = $this->getRepositoryEngine();

        $repositoryEngine->deleteType('cmis:item1');
        $this->assertNull($repositoryEngine->getTypeDefinition('cmis:item1'));
        $this->markTestIncomplete('incomplete');
    }

    public function testDeleteType_WithSubtypes()
    {
        $this->setExpectedException(
            'Turrsis\Cmis\Exception\Constraint',
            '"cmis:item2" type has a sub-type'
        );
        $repositoryEngine = $this->getRepositoryEngine();
        $repositoryEngine->deleteType('cmis:item2');
    }
    
    public function testDeleteType_WithObjectExist()
    {
        $repository = $this->getRepository();
        $repositoryEngine = $this->readAttribute($repository->getRepositoryService(), 'repositoryEngine');

        $repository->getObjectService()->createItem([
            'cmis:objectTypeId' => 'cmis:item3',
            'cmis:name'         => 'testDeleteType_WithObjectExist',
            'cmis:description'  => 'testDeleteType_WithObjectExist',
        ]);
        $this->setExpectedException(
            'Turrsis\Cmis\Exception\Constraint',
            '1 objects of "cmis:item2" type exist in the repository'
        );
        $repositoryEngine->deleteType('cmis:item2');
    }

    public function testGetTypeDefinition()
    {
        $this->markTestIncomplete('incomplete');
    }

    public function testGetRepositories()
    {
        $this->markTestIncomplete('incomplete');
    }

    public function testGetRepositoryInfo()
    {
        $this->markTestIncomplete('incomplete');
    }
}
