<?php
namespace CmisTest\Cmis\Cmis\Services\Rdb;

class RepositoryEngineIntegrationTest extends AbstractEngineTest
{
    /**
     * @var \Cmis\Cmis\Services\Rdb\ObjectsEngine
     */
    protected $objectEngine;

    /**
     * @var \Cmis\Cmis\Services\Rdb\RepositoryEngine
     */
    protected $repositoryEngine;
    
    public static function setUpBeforeClass() {
        static::$repository = self::createRepository();
    }
    
    public function setUp()
    {
        $this->objectEngine     = $this->readAttribute(static::$repository, 'objectsEngine');
        $this->repositoryEngine = $this->readAttribute(static::$repository, 'repositoryEngine');
    }

    public function testCreateType()
    {
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
        $object1 = $this->repositoryEngine->createType($type1);
        
        $this->assertArraySubset($type1, $object1);
        
        $metadata = new \Zend\Db\Metadata\Metadata(self::$dbAdapter);
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
        $object2 = $this->repositoryEngine->createType($type2);
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
        $object3 = $this->repositoryEngine->createType($type3);
        $this->assertArraySubset($type3, $object3);
    }
    
    public function testGetTypeChildren()
    {
        $children = $this->repositoryEngine->getTypeChildren('cmis:item');
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
        $descendants = $this->repositoryEngine->getTypeDescendants('cmis:item');
        
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
            $this->repositoryEngine->getTypeDescendants('cmis:item1')
        );
    }

    public function testUpdateType()
    {
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
        $updatedType = $this->repositoryEngine->updateType($type);
        $this->assertArraySubset($type, $updatedType);
    }

    public function testDeleteType()
    {
        $this->repositoryEngine->deleteType('cmis:item1');
        $this->assertNull($this->repositoryEngine->getTypeDefinition('cmis:item1'));
        $this->markTestIncomplete('incomplete');
    }

    public function testDeleteType_WithSubtypes()
    {
        $this->setExpectedException(
            'Cmis\Cmis\Exception\Constraint',
            '"cmis:item2" type has a sub-type'
        );
        $this->repositoryEngine->deleteType('cmis:item2');
    }
    
    public function testDeleteType_WithObjectExist()
    {
        self::$repository->getObjectService()->createItem([
            'cmis:objectTypeId' => 'cmis:item3',
            'cmis:name'         => 'testDeleteType_WithObjectExist',
            'cmis:description'  => 'testDeleteType_WithObjectExist',
        ]);
        $this->setExpectedException(
            'Cmis\Cmis\Exception\Constraint',
            '1 objects of "cmis:item2" type exist in the repository'
        );
        $this->repositoryEngine->deleteType('cmis:item2');
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
