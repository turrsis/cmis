<?php
namespace CmisTest\Cmis\Cmis\Services\Rdb;

class ObjectsEngineTest extends AbstractServiceTest
{
    /**
     * @var \Cmis\Cmis\Services\Rdb\QueryBuilder */
    protected static $objectsEngine;

    protected static $expectedItems = array();

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$objectsEngine = self::readAttribute(self::$repository->getObjectService(), 'objectsEngine');
        
        $typePattern = array(
            'parentId'                 => 'cmis:item',
            'id'                       => 'cmis:item01',
            'description'              => '',
            'creatable'                => true,
            'fileable'                 => true,
            'queryable'                => true,
            'controllablePolicy'       => false,
            'controllableACL'          => false,
            'fulltextIndexed'          => false,
            'includedInSupertypeQuery' => true,
            'typeMutability.create'    => true,
            'typeMutability.update'    => true,
            'typeMutability.delete'    => true,
            'versionable'              => true,
            'contentStreamAllowed'     => true,
            'propertyDefinitions'      => array(),
        );
        $propertyPattern = array(
            'propertyId'         => 'property_0',
            'defaultValue'       => null,
            'description'        => null,
            'propertyType'       => 'xs:string',
            'cardinality'        => 'single',
            'updatability'       => 'readwrite',
            'required'           => false,
            'queryable'          => true,
            'orderable'          => false,
            'choices'            => null,
            'openChoice'         => null,
        );
        
        $repService = self::$repository->getRepositoryService();
        
        $type0 = $repService->createType(array_replace_recursive($typePattern, array(
            'parentId'                 => 'cmis:item',
            'id'                       => 'cmis:item0',
            'propertyDefinitions'      => array(
                'property_0' => array_replace_recursive($propertyPattern, array(
                    'propertyId'         => 'property_0',
                    'description'        => 'type ->> cmis:item0',
                )),
            ),
        )));
            $type00 = $repService->createType(array_replace_recursive($typePattern, array(
                'parentId'                 => 'cmis:item0',
                'id'                       => 'cmis:item00',
                'propertyDefinitions'      => array(
                    'property_1' => array_replace_recursive($propertyPattern, array(
                        'propertyId'         => 'property_1',
                        'description'        => 'type ->> cmis:item00',
                    )),
                ),
            )));
            $type01 = $repService->createType(array_replace_recursive($typePattern, array(
                'parentId'                 => 'cmis:item0',
                'id'                       => 'cmis:item01',
                'propertyDefinitions'      => array(
                    'property_2' => array_replace_recursive($propertyPattern, array(
                        'propertyId'         => 'property_2',
                        'description'        => 'type ->> cmis:item01',
                    )),
                ),
            )));
        $type1 = $repService->createType(array_replace_recursive($typePattern, array(
            'parentId'                 => 'cmis:item',
            'id'                       => 'cmis:item1',
            'propertyDefinitions'      => array(
                'property_1' => array_replace_recursive($propertyPattern, array(
                    'propertyId'         => 'property_1',
                    'description'        => 'type ->> cmis:item1',
                )),
                'property_2' => array_replace_recursive($propertyPattern, array(
                    'propertyId'         => 'property_2',
                    'description'        => 'type ->> cmis:item1',
                )),
            ),
        )));
        
        $itemId = self::$repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'cmis:item0->0',
            'property_0'        => 'cmis:item0->0->property_0->0',
        ));
            $itemId = self::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis:item00->0',
                'property_0'        => 'cmis:item00->0->property_0->0',
                'property_1'        => 'cmis:item00->0->property_1->0',
            ));

            $itemId = self::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis:item00->1',
                'property_0'        => 'cmis:item00->1->property_0->0',
                'property_1'        => 'cmis:item00->1->property_1->1',
            ));

            $itemId = self::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item01',
                'cmis:name'         => 'cmis:item01->0',
                'property_0'        => 'cmis:item01->0->property_0->0',
                'property_2'        => 'cmis:item01->0->property_2->0',
            ));

            $itemId = self::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item01',
                'cmis:name'         => 'cmis:item01->1',
                'property_0'        => 'cmis:item01->1->property_0->0',
                'property_2'        => 'cmis:item01->1->property_2->1',
            ));
            
        $itemId = self::$repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item1',
            'cmis:name'         => 'cmis:item1->0',
            'property_1'        => 'cmis:item1->0->property_1->1',
            'property_2'        => 'cmis:item1->0->property_2->1',
        ));
    }
    
    public static function testSelectLeftObjectByType()
    {
        self::assertEquals(
            array(
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:objectId'     => '3',
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_0'        => 'cmis:item00->0->property_0->0',
                    'property_1'        => 'cmis:item00->0->property_1->0',
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:objectId'     => '4',
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_0'        => 'cmis:item00->1->property_0->0',
                    'property_1'        => 'cmis:item00->1->property_1->1',
                )),
            ), 
            self::$adapter->query(
                self::$objectsEngine->_selectLeftObjectByType(
                    self::$repository->getRepositoryService()->getTypeDefinition('cmis:item00')
                ),
                'execute'
            )->toArray()
        );
        
        self::assertEquals(
            array(
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'cmis:objectId'     => '2',
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'cmis:objectId'     => '3',
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'cmis:objectId'     => '4',
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'cmis:objectId'     => '5',
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'cmis:objectId'     => '6',
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
            ), 
            self::$adapter->query(
                self::$objectsEngine->_selectLeftObjectByType(
                    self::$repository->getRepositoryService()->getTypeDefinition('cmis:item0')
                ),
                'execute'
            )->toArray()
        );
        
        self::assertEquals(
            array(
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item1',
                    'cmis:name'         => 'cmis:item1->0',
                    'cmis:objectId'     => '7',
                    'property_1'        => 'cmis:item1->0->property_1->1',
                    'property_2'        => 'cmis:item1->0->property_2->1',
                )),
            ), 
            self::$adapter->query(
                self::$objectsEngine->_selectLeftObjectByType(
                    self::$repository->getRepositoryService()->getTypeDefinition('cmis:item1')
                ),
                'execute'
            )->toArray()
        );
        return;
    }
    
    public static function testSelectRightObject()
    {
        $type = self::$repository->getRepositoryService()->getTypeDefinition('cmis:item');
        $type['children'] = self::$repository->getRepositoryService()->getTypeDescendants('cmis:item');

        $expectedFull = array(
            '2' => array(
                'property_1'    => null,
                'cmis:objectId' => '2',
                'property_2'    => null,
                'property_0'    => 'cmis:item0->0->property_0->0',
            ),
            '3' => array(
                'property_1'    => "cmis:item00->0->property_1->0",
                'cmis:objectId' => '3',
                'property_2'    => null,
                'property_0'    => "cmis:item00->0->property_0->0",
            ),
            '4' => array(
                'property_1'    => "cmis:item00->1->property_1->1",
                'cmis:objectId' => '4',
                'property_2'    => null,
                'property_0'    => "cmis:item00->1->property_0->0",
            ),
            '5' => array(
                'property_1'    => null,
                'cmis:objectId' => '5',
                'property_2'    => "cmis:item01->0->property_2->0",
                'property_0'    => "cmis:item01->0->property_0->0",
            ),
            '6' => array(
                'property_1'    => null,
                'cmis:objectId' => '6',
                'property_2'    => "cmis:item01->1->property_2->1",
                'property_0'    => "cmis:item01->1->property_0->0",
            ),
            '7' => array(
                'property_1'    => "cmis:item1->0->property_1->1",
                'cmis:objectId' => '7',
                'property_2'    => "cmis:item1->0->property_2->1",
                'property_0'    => null,
            ),
        );
        
        self::assertEquals(
            array(
                $expectedFull[2],
                $expectedFull[3],
                $expectedFull[4],
                $expectedFull[5],
                $expectedFull[6],
                $expectedFull[7],
            ),
            self::$adapter->query(self::$objectsEngine->_selectRightObject($type), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                $expectedFull[2],
                $expectedFull[3],
                $expectedFull[4],
                $expectedFull[5],
                $expectedFull[6],
            ),
            self::$adapter->query(self::$objectsEngine->_selectRightObject($type['children']['cmis:item0']), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                array(
                    'property_1'    => "cmis:item1->0->property_1->1",
                    'cmis:objectId' => '7',
                    'property_2'    => "cmis:item1->0->property_2->1",
                ),
            ),
            self::$adapter->query(self::$objectsEngine->_selectRightObject($type['children']['cmis:item1']), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                array(
                    'property_1'    => "cmis:item00->0->property_1->0",
                    'cmis:objectId' => '3',
                ),
                array(
                    'property_1'    => "cmis:item00->1->property_1->1",
                    'cmis:objectId' => '4',
                ),
            ),
            self::$adapter->query(self::$objectsEngine->_selectRightObject($type['children']['cmis:item0']['children']['cmis:item00']), 'execute')->toArray()
        );
    }
    
    public static function testSelectFullObject()
    {
        $type = self::$repository->getRepositoryService()->getTypeDefinition('cmis:item');
        $type['children'] = self::$repository->getRepositoryService()->getTypeDescendants('cmis:item');
        
        self::assertEquals(
            array(
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '2',
                    'property_2'        => null,
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_1'        => "cmis:item00->0->property_1->0",
                    'cmis:objectId'     => '3',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_1'        => "cmis:item00->1->property_1->1",
                    'cmis:objectId'     => '4',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '5',
                    'property_2'        => "cmis:item01->0->property_2->0",
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'property_1'        => null,
                    'cmis:objectId'     => '6',
                    'property_2'        => "cmis:item01->1->property_2->1",
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item1',
                    'cmis:name'         => 'cmis:item1->0',
                    'property_1'        => "cmis:item1->0->property_1->1",
                    'cmis:objectId'     => '7',
                    'property_2'        => "cmis:item1->0->property_2->1",
                    'property_0'        => null,
                )),
            ), 
            self::$adapter->query(self::$objectsEngine->_selectFullObject($type), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '2',
                    'property_2'        => null,
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_1'        => "cmis:item00->0->property_1->0",
                    'cmis:objectId'     => '3',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_1'        => "cmis:item00->1->property_1->1",
                    'cmis:objectId'     => '4',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '5',
                    'property_2'        => "cmis:item01->0->property_2->0",
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace(self::$expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'property_1'        => null,
                    'cmis:objectId'     => '6',
                    'property_2'        => "cmis:item01->1->property_2->1",
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
            ), 
            self::$adapter->query(self::$objectsEngine->_selectFullObject($type['children']['cmis:item0']), 'execute')->toArray()
        );
    }
}