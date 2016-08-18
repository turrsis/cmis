<?php
namespace CmisTest\Cmis\Cmis\Services\Rdb;

class ObjectsEngineIntegrationTest extends AbstractEngineTest
{
    /**
     * @var \Cmis\Cmis\Services\Rdb\ObjectsEngine
     */
    protected $objectEngine;

    public static function setUpBeforeClass() {
        static::$repository = self::createRepository();
        //self::createRepositoryTypes(static::$repository);
    }
    
    public function setUp()
    {
        $this->objectEngine = $this->readAttribute(static::$repository, 'objectsEngine');
    }

    public function testCreateObject()
    {
        $folder0Id = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name'         => 'folder_0',
        ], ['folderId' => 1]);
            $folder00Id = $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => 'folder_00',
            ], ['folderId' => $folder0Id]);
                $this->objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => 'folder_000',
                ], ['folderId' => $folder00Id]);
                $this->objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => 'folder_001',
                ], ['folderId' => $folder00Id]);
            $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => 'folder_01',
            ], ['folderId' => $folder0Id]);
        $folder1Id = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name'         => 'folder_1',
        ], ['folderId' => 1]);

        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:folder',
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:objectId'     => $folder0Id,
                'cmis:name'         => 'folder_0',
                'cmis:path'         => '/folder_0',
                'cmis:level'        => 1,
            ],
            $this->objectEngine->getProperties($folder0Id)
        );
        
        $documentId = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => 'document_1',
            'cmis:description'  => 'document_1_descr',
        ]);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:document',
                'cmis:objectTypeId' => 'cmis:document',
                'cmis:objectId'     => $documentId,
                'cmis:name'         => 'document_1',
                'cmis:description'  => 'document_1_descr',
            ],
            $this->objectEngine->getProperties($documentId)
        );
        
        $policyId = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:policy',
            'cmis:name'         => 'policy_1',
            'cmis:description'  => 'policy_1_descr',
            'cmis:policyText'   => 'policy_1_text',
        ]);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:policy',
                'cmis:objectTypeId' => 'cmis:policy',
                'cmis:objectId'     => $policyId,
                'cmis:name'         => 'policy_1',
                'cmis:description'  => 'policy_1_descr',
                'cmis:policyText'   => 'policy_1_text',
            ],
            $this->objectEngine->getProperties($policyId)
        );

        $itemId = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item',
            'cmis:name'         => 'item_1',
            'cmis:description'  => 'item_1_descr',
        ]);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:objectId'     => $itemId,
                'cmis:name'         => 'item_1',
                'cmis:description'  => 'item_1_descr',
            ],
            $this->objectEngine->getProperties($itemId)
        );
        
        $relationshipId = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:sourceId'     => $documentId,
            'cmis:targetId'     => $itemId,
        ]);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:relationship',
                'cmis:objectTypeId' => 'cmis:relationship',
                'cmis:objectId'     => $relationshipId,
                'cmis:name'         => '',
                'cmis:sourceId'     => $documentId,
                'cmis:sourceTypeId' => 'cmis:document',
                'cmis:targetId'     => $itemId,
                'cmis:targetTypeId' => 'cmis:item',
            ],
            $this->objectEngine->getProperties($relationshipId)
        );
    }
    
    public function testCreateObjectForNotExistsType()
    {
        $this->setExpectedException(
            'Cmis\Cmis\Exception\InvalidArgument',
            'type "cmis:foo" not exists'
        );
        $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:foo',
            'cmis:name'         => 'item_1',
            'cmis:description'  => 'item_1_descr',
        ]);
    }

    public function testGetObject_and_GetProperties()
    {
        $object = $this->objectEngine->getObject('1', [
            'includeRelationships'    => 'either',
            //'includePolicyIds'        => true,
            //'includeAllowableActions' => true,
            //'includeACL'              => true,
            //'renditionFilter'         => [],
        ]);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:folder',
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:objectId'     => '1',
                'cmis:name'         => '/',
                'cmis:description'  => 'root folder',
                'cmis:path'         => '/',
                'cmis:level'        => 0,
            ],
            $object['properties']
        );
        $this->assertEquals(
            [],
            $object['relationships']
        );

        $this->assertEquals(
            $object['properties'],
            $this->objectEngine->getProperties(1)
        );
    }
    
    public function testGetFolderParent()
    {
        $folder = $this->objectEngine->getFolderParent(2);
        $this->assertArraySubset(
            [
                'cmis:baseTypeId'   => 'cmis:folder',
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:objectId'     => '1',
                'cmis:name'         => '/',
                'cmis:description'  => 'root folder',
                'cmis:path'         => '/',
                'cmis:level'        => 0,
            ],
            $folder['properties']
        );
    }
    
    public function testGetFolderTree()
    {
        $tree = $this->objectEngine->getFolderTree('/', array('depth'=> 100));
        
        $this->assertCount(2, $tree);

        $folder0 = $tree[0];
        $this->assertCount(2, $folder0['childs']);
        $this->assertEquals('folder_0', $folder0['cmis:name']);
        
            $folder00 = $folder0['childs'][0];
            $this->assertCount(2, $folder00['childs']);
            $this->assertEquals('folder_00', $folder00['cmis:name']);
                
                $folder000 = $folder00['childs'][0];
                $this->assertArrayNotHasKey('childs', $folder000);
                $this->assertEquals('folder_000', $folder000['cmis:name']);
                
                $folder001 = $folder00['childs'][1];
                $this->assertArrayNotHasKey('childs', $folder001);
                $this->assertEquals('folder_001', $folder001['cmis:name']);
            
            $folder01 = $folder0['childs'][1];
            $this->assertArrayNotHasKey('childs', $folder01);
            $this->assertEquals('folder_01', $folder01['cmis:name']);
                
        $folder1 = $tree[1];
        $this->assertArrayNotHasKey('childs', $folder1);
        $this->assertEquals('folder_1', $folder1['cmis:name']);

        // EmptyTree
        $this->assertEquals(
            array(),
            $this->objectEngine->getFolderTree('/folder_1', array('depth'=> 100))
        );
        return;
    }
    
    public function testGetObjectByPath()
    {
        $folder0 = $this->objectEngine->getObjectByPath('/folder_0');
        $this->assertArraySubset(
            [
                'cmis:objectId'                  => '2',
                'cmis:objectTypeId'              => 'cmis:folder',
                'cmis:baseTypeId'                => 'cmis:folder',
                'cmis:name'                      => 'folder_0',
                'cmis:path'                      => '/folder_0',
                'cmis:parentId'                  => '1',
                'cmis:level'                     => '1',
            ],
            $folder0['properties']
        );
        
        $folder00 = $this->objectEngine->getObjectByPath('/folder_0/folder_00');
        $this->assertArraySubset(
            [
                'cmis:objectId'                  => '3',
                'cmis:objectTypeId'              => 'cmis:folder',
                'cmis:baseTypeId'                => 'cmis:folder',
                'cmis:name'                      => 'folder_00',
                'cmis:path'                      => '/folder_0/folder_00',
                'cmis:parentId'                  => $folder0['properties']['cmis:objectId'],
                'cmis:level'                     => '2',
            ],
            $folder00['properties']
        );
    }
    
    public function testGetObjectByPath_notFoundFolderException()
    {
        $this->setExpectedException(
            'Cmis\Cmis\Exception\ObjectNotFound',
            "object with path '/notExistFolder' not found"
        );
        $this->objectEngine->getObjectByPath('/notExistFolder');
    }
    
    public function testGetObjectByPath_notFoundObjectException()
    {
        $this->setExpectedException(
            'Cmis\Cmis\Exception\ObjectNotFound',
            "object with path '/folder_1/notExistObject' not found"
        );
        $this->objectEngine->getObjectByPath('/folder_1/notExistObject');
    }

    public function testAddObjectToFolder()
    {
        $this->objectEngine->addObjectToFolder(
            $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_0_in_folder3',
            ]),
            3
        );
        $this->objectEngine->addObjectToFolder(
            $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_1_in_folder3',
            ]),
            3
        );

        $relationships = $this->objectEngine->getObjectRelationships(3, array('relationshipDirection'=>'source'));
        
        $this->assertArraySubset(
            array(
                'cmis:objectId'     => '13',
                'cmis:baseTypeId'   => 'cmis:relationship',
                'cmis:name'         => 'relation from 3 to 12',
                'cmis:targetId'     => '12',
                'cmis:sourceId'     => '3',
            ),
            $relationships[0]
        );
        $this->assertArraySubset(
            array(
                'cmis:objectId'     => '15',
                'cmis:baseTypeId'   => 'cmis:relationship',
                'cmis:name'         => 'relation from 3 to 14',
                'cmis:targetId'     => '14',
                'cmis:sourceId'     => '3',
                
            ),
            $relationships[1]
        );
        
        $this->assertEquals(
            [],
            $this->objectEngine->getObjectRelationships('/folder_1')
        );
    }
    
    public function testRemoveObjectFromFolder()
    {
        $item = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item',
            'cmis:name'         => 'item_in_many_folders',
        ]);
        $this->objectEngine->addObjectToFolder($item, 3);
        $this->objectEngine->addObjectToFolder($item, 4);
        $this->objectEngine->addObjectToFolder($item, 5);
        $this->objectEngine->addObjectToFolder($item, 6);


        $snapshot = $this->_getObjectsIdSnapshot([17]);
        $this->objectEngine->removeObjectFromFolder(16, 3);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
        
        $snapshot = $this->_getObjectsIdSnapshot([18, 19, 20]);
        $this->objectEngine->removeObjectFromFolder(16);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
    }

    public function testGetObjectRelationships()
    {
        $this->markTestIncomplete();
    }

    public function testGetChildren()
    {
        $children = $this->objectEngine->getChildren(3);
        
        $this->assertCount(2, $children['objects']);        
        $this->assertFalse($children['hasMoreItems']);
        $this->assertEquals(2, $children['numItems']);

        $this->assertArraySubset(
            [
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_0_in_folder3',
            ], 
            $children['objects'][0]['properties']
        );
        $this->assertArraySubset(
            [
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_1_in_folder3',
            ], 
            $children['objects'][1]['properties']
        );

        // EmptyChildrens
        $this->assertEquals(
            array(
                'objects'      => array(),
                'hasMoreItems' => false,
                'numItems'     => 0,
            ),
            $this->objectEngine->getChildren('/')
        );
    }

    public function testGetDescendants()
    {
        $this->markTestIncomplete();
    }

    public function testGetObjectParents()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetCheckedOutDocs()
    {
        $this->markTestIncomplete();
    }

    public function testQuery()
    {
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
            'typeMutability_create'    => true,
            'typeMutability_update'    => true,
            'typeMutability_delete'    => true,
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
        
        $repService = static::$repository->getRepositoryService();
        
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
        
        $item0Id = static::$repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'cmis_item0->0',
            'property_0'        => 'cmis_item0->0->property_0->0',
        ));
            $item000Id = static::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->0',
                'property_0'        => 'cmis_item00->0->property_0->0',
                'property_1'        => 'cmis_item00->0->property_1->0',
            ));

            $item001Id = static::$repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->1',
                'property_0'        => 'cmis_item00->1->property_0->0',
                'property_1'        => 'cmis_item00->1->property_1->1',
            ));
            
        $item10Id = static::$repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item1',
            'cmis:name'         => 'cmis_item1->0',
            'property_1'        => 'cmis_item1->0->property_1->1',
            'property_2'        => 'cmis_item1->0->property_2->1',
        ));
        
        $result = $this->objectEngine->query('SELECT * FROM cmis:item1', array('showDataOnly' => true));
        $this->assertCount(1, $result);
        $this->assertArraySubset(
            [
                'cmis:objectId'     => $item10Id,
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item1',
                'cmis:name'         => 'cmis_item1->0',
                'property_1'        => 'cmis_item1->0->property_1->1',
                'property_2'        => 'cmis_item1->0->property_2->1',
            ],
            $result[0]
        );
        
        $result = $this->objectEngine->query('SELECT * FROM cmis:item00', array('showDataOnly' => true));
        $this->assertCount(2, $result);
        $this->assertArraySubset(
            [
                'cmis:objectId'     => $item000Id,
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->0',
                'property_0'        => 'cmis_item00->0->property_0->0',
                'property_1'        => 'cmis_item00->0->property_1->0',
            ],
            $result[0]
        );
        $this->assertArraySubset(
            [
                'cmis:objectId'     => $item001Id,
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->1',
                'property_0'        => 'cmis_item00->1->property_0->0',
                'property_1'        => 'cmis_item00->1->property_1->1',
            ],
            $result[1]
        );

        $result = $this->objectEngine->query("SELECT * FROM cmis:item00 WHERE cmis:name = 'cmis_item00->0'", array('showDataOnly' => true));
        $this->assertCount(1, $result);
        $this->assertArraySubset(
            [
                'cmis:objectId'     => $item000Id,
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->0',
                'property_0'        => 'cmis_item00->0->property_0->0',
                'property_1'        => 'cmis_item00->0->property_1->0',
            ],
            $result[0]
        );
    }

    public function testDeleteObject()
    {
        $item0 = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_for_delete_objects_0',
        ]);
        $item1 = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_for_delete_objects_1',
        ]);

        $rel1 = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:sourceId'     => $item0,
            'cmis:targetId'     => $item1,
        ]);
        $rel2 = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:sourceId'     => $item1,
            'cmis:targetId'     => $item0,
        ]);
        
        
        $rel3 = $this->objectEngine->addObjectToFolder($item0, 1);
        $rel4 = $this->objectEngine->addObjectToFolder($item0, 2);
        $rel5 = $this->objectEngine->addObjectToFolder($item0, 3);
        
        $rel6 = $this->objectEngine->addObjectToFolder($item1, 1);
        $rel7 = $this->objectEngine->addObjectToFolder($item1, 4);
        $rel8 = $this->objectEngine->addObjectToFolder($item1, 5);
        
        $snapshot = $this->_getObjectsIdSnapshot([$item0, $rel1, $rel2, $rel3, $rel4, $rel5]);
        $this->objectEngine->deleteObject($item0);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );

        $snapshot = $this->_getObjectsIdSnapshot([$item1, $rel6, $rel7, $rel8]);
        $this->objectEngine->deleteObject($item1);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
    }
    
    public function testDeleteObjectFolder()
    {
        $this->setExpectedException(
            'Cmis\Cmis\Exception\Constraint',
            'deleteObject() function can\'t delete folders'
        );
        $this->objectEngine->deleteObject(2);
    }

    public function testUpdateProperties()
    {
        $objectId = $this->objectEngine->createObject(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_2_BeforeUpdate',
            'cmis:description'  => 'item_2_descr_BeforeUpdate',
        ));
        
        $updatedObjectId = $this->objectEngine->updateProperties([
            'cmis:objectId'     => $objectId,
            'cmis:name'         => 'item_2',
            'cmis:description'  => 'item_2_descr',
        ]);

        $this->assertArraySubset(
            [
                'cmis:objectId'     => $updatedObjectId,
                'cmis:baseTypeId'   => 'cmis:item',
                'cmis:objectTypeId' => 'cmis:item0',
                'cmis:name'         => 'item_2',
                'cmis:description'  => 'item_2_descr',
            ],
            $this->objectEngine->getProperties($updatedObjectId)
        );
        $this->assertEquals($objectId, $updatedObjectId);
    }
    
    public function createObjectForDeleteTree($namePrefix)
    {
        $objects = [];

        $objects['folder_0'] = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name'         => $namePrefix . '_folder_0'
        ], ['folderId' => 1]);
        
            $objects['folder_00'] = $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => $namePrefix . '_folder_00'
            ], ['folderId' => $objects['folder_0']]);
                $objects['folder_000'] = $this->objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => $namePrefix . '_folder_000'
                ], ['folderId' => $objects['folder_00']]);
            $objects['folder_01'] = $this->objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => $namePrefix . '_folder_01'
            ], ['folderId' => $objects['folder_0']]);

        $objects['document_0'] = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_0'
        ], ['folderId' => $objects['folder_0']]);
        $objects['relationship_0'] = $this->objectEngine->getObjectRelationships($objects['document_0'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];
        
        $objects['document_000'] = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_000'
        ], ['folderId' => $objects['folder_000']]);
        $objects['relationship_000'] = $this->objectEngine->getObjectRelationships($objects['document_000'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];

        $document_Link = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_Link'
        ]);
        $objects['document_Link_Relation'] = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:name'         => $namePrefix . '_document_Link_Relation',
            'cmis:sourceId' => $document_Link,
            'cmis:targetId' => $objects['document_0'],
        ]);

        $objects['item1_000'] = $this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => $namePrefix . '_item1_000',
        ], ['folderId' => $objects['folder_000']]);
        $objects['relationship_000_item1_000'] = $this->objectEngine->getObjectRelationships($objects['item1_000'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];
        
        
        return $objects;
    }
    
    public function testDeleteTree()
    {
        $objects = $this->createObjectForDeleteTree('deleteTree_unfileObjects_DELETE');
        
        $snapshot = $this->_getObjectsIdSnapshot($objects);
        $this->objectEngine->deleteTree($objects['folder_0']);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
        
        $objects = $this->createObjectForDeleteTree('deleteTree_unfileObjects_UNFILED');
        unset(
            $objects['document_0'],
            $objects['document_000'],
            $objects['item1_000'],
            $objects['document_Link_Relation']
        );
        $snapshot = $this->_getObjectsIdSnapshot($objects);
        $this->objectEngine->deleteTree($objects['folder_0'], ['unfileObjects' => 'unfile']);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );

        $objects = $this->createObjectForDeleteTree('deleteTree_unfileObjects_DELETESINGLEFILED');
        unset(
            $objects['document_000'],
            $objects['item1_000']
        );
        $snapshot = $this->_getObjectsIdSnapshot($objects);
        $this->objectEngine->deleteTree($objects['folder_0'], ['unfileObjects' => 'deletesinglefiled']);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
    }

    public function testMoveObject()
    {
        $sourceFolder  = $this->objectEngine->getProperties($this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name' => 'sourceFolder'
        ], ['folderId' => 1]));
        $targetFolder  = $this->objectEngine->getProperties($this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name' => 'targetFolder'
        ], ['folderId' => 1]));
        $movedObject   = $this->objectEngine->getProperties($this->objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name' => 'movedObject'
        ], ['folderId' => $sourceFolder['cmis:objectId']]));
        $relationship  = $this->objectEngine->getObjectRelationships(
            $movedObject['cmis:objectId'], 
            ['relationshipDirection' => 'either']
        )[0];
        
        $this->objectEngine->moveObject(
            $movedObject['cmis:objectId'], 
            $targetFolder['cmis:objectId'], 
            $sourceFolder['cmis:objectId']
        );
        
        $movedRelationship = $this->objectEngine->getProperties($relationship['cmis:objectId']);
        
        $relationship['cmis:sourceId'] = $targetFolder['cmis:objectId'];
        $relationship['cmis:sourceTypeId'] = $targetFolder['cmis:objectTypeId'];
        
        $this->assertEquals($relationship, $movedRelationship);
    }
}
