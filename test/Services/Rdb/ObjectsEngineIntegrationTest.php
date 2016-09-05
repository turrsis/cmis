<?php
namespace TurrsisTest\Cmis\Cmis\Services\Rdb;

use Zend\Stdlib\ArrayUtils;
use TurrsisTest\Cmis\AbstractTest;

class ObjectsEngineIntegrationTest extends AbstractTest
{
    /**
     * @var \Turrsis\Cmis\Repository
     */
    protected $repository;

    /**
     * @param string $repositoryName
     * @param array $config
     * @return \Turrsis\Cmis\Services\Rdb\ObjectsEngine
     */
    public function getObjectsEngine($repositoryName = 'cmis:repo:repo1', $config = [])
    {
        $config = ArrayUtils::merge(['services' => [
            'db:test' => self::$currentAdapter,
        ]], $config);
        $this->repository = $this->getServiceManager($config)->get($repositoryName);
        return $this->readAttribute($this->repository->getObjectService(), 'objectsEngine');
    }

    public function testCreateObject()
    {
        self::initDatabase();
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $folder0Id = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name'         => 'folder_0',
        ], ['folderId' => 1]);
            $folder00Id = $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => 'folder_00',
            ], ['folderId' => $folder0Id]);
                $objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => 'folder_000',
                ], ['folderId' => $folder00Id]);
                $objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => 'folder_001',
                ], ['folderId' => $folder00Id]);
            $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => 'folder_01',
            ], ['folderId' => $folder0Id]);
        $folder1Id = $objectEngine->createObject([
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
            $objectEngine->getProperties($folder0Id)
        );
        
        $documentId = $objectEngine->createObject([
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
            $objectEngine->getProperties($documentId)
        );
        
        $policyId = $objectEngine->createObject([
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
            $objectEngine->getProperties($policyId)
        );

        $itemId = $objectEngine->createObject([
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
            $objectEngine->getProperties($itemId)
        );
        
        $relationshipId = $objectEngine->createObject([
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
            $objectEngine->getProperties($relationshipId)
        );
    }
    
    public function testCreateObjectForNotExistsType()
    {
        $this->setExpectedException(
            'Turrsis\Cmis\Exception\InvalidArgument',
            'type "cmis:foo" not exists'
        );
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:foo',
            'cmis:name'         => 'item_1',
            'cmis:description'  => 'item_1_descr',
        ]);
    }

    public function testGetObject_and_GetProperties()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $object = $objectEngine->getObject('1', [
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
            $objectEngine->getProperties(1)
        );
    }
    
    public function testGetFolderParent()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $folder = $objectEngine->getFolderParent(2);
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
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $tree = $objectEngine->getFolderTree('/', array('depth'=> 100));
        
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
            $objectEngine->getFolderTree('/folder_1', array('depth'=> 100))
        );
    }
    
    public function testGetObjectByPath()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        
        $folder0 = $objectEngine->getObjectByPath('/folder_0');
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
        
        $folder00 = $objectEngine->getObjectByPath('/folder_0/folder_00');
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
            'Turrsis\Cmis\Exception\ObjectNotFound',
            "object with path '/notExistFolder' not found"
        );
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $objectEngine->getObjectByPath('/notExistFolder');
    }
    
    public function testGetObjectByPath_notFoundObjectException()
    {
        $this->setExpectedException(
            'Turrsis\Cmis\Exception\ObjectNotFound',
            "object with path '/folder_1/notExistObject' not found"
        );
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $objectEngine->getObjectByPath('/folder_1/notExistObject');
    }

    public function testAddObjectToFolder()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $objectEngine->addObjectToFolder(
            $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_0_in_folder3',
            ]),
            3
        );
        $objectEngine->addObjectToFolder(
            $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:item',
                'cmis:name'         => 'item_1_in_folder3',
            ]),
            3
        );

        $relationships = $objectEngine->getObjectRelationships(3, array('relationshipDirection'=>'source'));
        
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
            $objectEngine->getObjectRelationships('/folder_1')
        );
    }
    
    public function testRemoveObjectFromFolder()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $item = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item',
            'cmis:name'         => 'item_in_many_folders',
        ]);
        $objectEngine->addObjectToFolder($item, 3);
        $objectEngine->addObjectToFolder($item, 4);
        $objectEngine->addObjectToFolder($item, 5);
        $objectEngine->addObjectToFolder($item, 6);


        $snapshot = $this->_getObjectsIdSnapshot([17]);
        $objectEngine->removeObjectFromFolder(16, 3);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
        
        $snapshot = $this->_getObjectsIdSnapshot([18, 19, 20]);
        $objectEngine->removeObjectFromFolder(16);
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
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $children = $objectEngine->getChildren(3);
        
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
            $objectEngine->getChildren('/')
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
        $repository   = $this->getRepository('cmis:repo:repo1');
        $objectEngine = $this->readAttribute($repository->getObjectService(), 'objectsEngine');

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
        
        $repService = $repository->getRepositoryService();
        
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
        
        $item0Id = $repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'cmis_item0->0',
            'property_0'        => 'cmis_item0->0->property_0->0',
        ));
            $item000Id = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->0',
                'property_0'        => 'cmis_item00->0->property_0->0',
                'property_1'        => 'cmis_item00->0->property_1->0',
            ));

            $item001Id = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis_item00->1',
                'property_0'        => 'cmis_item00->1->property_0->0',
                'property_1'        => 'cmis_item00->1->property_1->1',
            ));
            
        $item10Id = $repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item1',
            'cmis:name'         => 'cmis_item1->0',
            'property_1'        => 'cmis_item1->0->property_1->1',
            'property_2'        => 'cmis_item1->0->property_2->1',
        ));
        
        $result = $objectEngine->query('SELECT * FROM cmis:item1', array('showDataOnly' => true));
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
        
        $result = $objectEngine->query('SELECT * FROM cmis:item00', array('showDataOnly' => true));
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

        $result = $objectEngine->query("SELECT * FROM cmis:item00 WHERE cmis:name = 'cmis_item00->0'", array('showDataOnly' => true));
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
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $item0 = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_for_delete_objects_0',
        ]);
        $item1 = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_for_delete_objects_1',
        ]);

        $rel1 = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:sourceId'     => $item0,
            'cmis:targetId'     => $item1,
        ]);
        $rel2 = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:sourceId'     => $item1,
            'cmis:targetId'     => $item0,
        ]);
        
        
        $rel3 = $objectEngine->addObjectToFolder($item0, 1);
        $rel4 = $objectEngine->addObjectToFolder($item0, 2);
        $rel5 = $objectEngine->addObjectToFolder($item0, 3);
        
        $rel6 = $objectEngine->addObjectToFolder($item1, 1);
        $rel7 = $objectEngine->addObjectToFolder($item1, 4);
        $rel8 = $objectEngine->addObjectToFolder($item1, 5);
        
        $snapshot = $this->_getObjectsIdSnapshot([$item0, $rel1, $rel2, $rel3, $rel4, $rel5]);
        $objectEngine->deleteObject($item0);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );

        $snapshot = $this->_getObjectsIdSnapshot([$item1, $rel6, $rel7, $rel8]);
        $objectEngine->deleteObject($item1);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
    }
    
    public function testDeleteObjectFolder()
    {
        $this->setExpectedException(
            'Turrsis\Cmis\Exception\Constraint',
            'deleteObject() function can\'t delete folders'
        );
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');
        $objectEngine->deleteObject(2);
    }

    public function testUpdateProperties()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $objectId = $objectEngine->createObject(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'item_2_BeforeUpdate',
            'cmis:description'  => 'item_2_descr_BeforeUpdate',
        ));
        
        $updatedObjectId = $objectEngine->updateProperties([
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
            $objectEngine->getProperties($updatedObjectId)
        );
        $this->assertEquals($objectId, $updatedObjectId);
    }
    
    public function createObjectForDeleteTree($namePrefix)
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $objects = [];

        $objects['folder_0'] = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name'         => $namePrefix . '_folder_0'
        ], ['folderId' => 1]);
        
            $objects['folder_00'] = $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => $namePrefix . '_folder_00'
            ], ['folderId' => $objects['folder_0']]);
                $objects['folder_000'] = $objectEngine->createObject([
                    'cmis:objectTypeId' => 'cmis:folder',
                    'cmis:name'         => $namePrefix . '_folder_000'
                ], ['folderId' => $objects['folder_00']]);
            $objects['folder_01'] = $objectEngine->createObject([
                'cmis:objectTypeId' => 'cmis:folder',
                'cmis:name'         => $namePrefix . '_folder_01'
            ], ['folderId' => $objects['folder_0']]);

        $objects['document_0'] = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_0'
        ], ['folderId' => $objects['folder_0']]);
        $objects['relationship_0'] = $objectEngine->getObjectRelationships($objects['document_0'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];
        
        $objects['document_000'] = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_000'
        ], ['folderId' => $objects['folder_000']]);
        $objects['relationship_000'] = $objectEngine->getObjectRelationships($objects['document_000'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];

        $document_Link = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name'         => $namePrefix . '_document_Link'
        ]);
        $objects['document_Link_Relation'] = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:relationship',
            'cmis:name'         => $namePrefix . '_document_Link_Relation',
            'cmis:sourceId' => $document_Link,
            'cmis:targetId' => $objects['document_0'],
        ]);

        $objects['item1_000'] = $objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => $namePrefix . '_item1_000',
        ], ['folderId' => $objects['folder_000']]);
        $objects['relationship_000_item1_000'] = $objectEngine->getObjectRelationships($objects['item1_000'], array('relationshipDirection' => 'either'))[0]['cmis:objectId'];
        
        
        return $objects;
    }
    
    public function testDeleteTree()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $objects = $this->createObjectForDeleteTree('deleteTree_unfileObjects_DELETE');
        
        $snapshot = $this->_getObjectsIdSnapshot($objects);
        $objectEngine->deleteTree($objects['folder_0']);
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
        $objectEngine->deleteTree($objects['folder_0'], ['unfileObjects' => 'unfile']);
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
        $objectEngine->deleteTree($objects['folder_0'], ['unfileObjects' => 'deletesinglefiled']);
        $this->assertEquals(
            $snapshot,
            $this->_getObjectsIdSnapshot()
        );
    }

    public function testMoveObject()
    {
        $objectEngine = $this->getObjectsEngine('cmis:repo:repo1');

        $sourceFolder  = $objectEngine->getProperties($objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name' => 'sourceFolder'
        ], ['folderId' => 1]));
        $targetFolder  = $objectEngine->getProperties($objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:folder',
            'cmis:name' => 'targetFolder'
        ], ['folderId' => 1]));
        $movedObject   = $objectEngine->getProperties($objectEngine->createObject([
            'cmis:objectTypeId' => 'cmis:document',
            'cmis:name' => 'movedObject'
        ], ['folderId' => $sourceFolder['cmis:objectId']]));
        $relationship  = $objectEngine->getObjectRelationships(
            $movedObject['cmis:objectId'], 
            ['relationshipDirection' => 'either']
        )[0];
        
        $objectEngine->moveObject(
            $movedObject['cmis:objectId'], 
            $targetFolder['cmis:objectId'], 
            $sourceFolder['cmis:objectId']
        );
        
        $movedRelationship = $objectEngine->getProperties($relationship['cmis:objectId']);
        
        $relationship['cmis:sourceId'] = $targetFolder['cmis:objectId'];
        $relationship['cmis:sourceTypeId'] = $targetFolder['cmis:objectTypeId'];
        
        $this->assertEquals($relationship, $movedRelationship);
    }
    
    
    protected $expectedPredefined = array(
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

    public function testSelectLeftObjectByType()
    {
        $this->initDatabase();
        $repository = $this->getRepository();
        $this->initRepositoryItems($repository);

        $sql     = new \Zend\Db\Sql\Sql(self::$currentAdapter);

        $objectsEngine = $this->readAttribute($repository->getObjectService(), 'objectsEngine');
        
        $this->assertEquals(
            array(
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:objectId'     => '3',
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_0'        => 'cmis:item00->0->property_0->0',
                    'property_1'        => 'cmis:item00->0->property_1->0',
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:objectId'     => '4',
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_0'        => 'cmis:item00->1->property_0->0',
                    'property_1'        => 'cmis:item00->1->property_1->1',
                )),
            ), 
            self::$currentAdapter->query(
                $sql->buildSqlString($objectsEngine->_selectLeftObjectByType(
                    $repository->getRepositoryService()->getTypeDefinition('cmis:item00')
                )),
                'execute'
            )->toArray()
        );
        
        $this->assertEquals(
            array(
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'cmis:objectId'     => '2',
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'cmis:objectId'     => '3',
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'cmis:objectId'     => '4',
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'cmis:objectId'     => '5',
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'cmis:objectId'     => '6',
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
            ), 
            self::$currentAdapter->query(
                $sql->buildSqlString($objectsEngine->_selectLeftObjectByType(
                    $repository->getRepositoryService()->getTypeDefinition('cmis:item0')
                )),
                'execute'
            )->toArray()
        );
        
        $this->assertEquals(
            array(
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item1',
                    'cmis:name'         => 'cmis:item1->0',
                    'cmis:objectId'     => '7',
                    'property_1'        => 'cmis:item1->0->property_1->1',
                    'property_2'        => 'cmis:item1->0->property_2->1',
                )),
            ), 
            self::$currentAdapter->query(
                $sql->buildSqlString($objectsEngine->_selectLeftObjectByType(
                    $repository->getRepositoryService()->getTypeDefinition('cmis:item1')
                )),
                'execute'
            )->toArray()
        );
    }
    
    public function testSelectRightObject()
    {
        $repository = $this->getRepository();
        $objectsEngine = $this->readAttribute($repository->getObjectService(), 'objectsEngine');
        $sql     = new \Zend\Db\Sql\Sql(self::$currentAdapter);
        
        $type = $repository->getRepositoryService()->getTypeDefinition('cmis:item');
        $type['children'] = $repository->getRepositoryService()->getTypeDescendants('cmis:item');

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
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectRightObject($type)), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                $expectedFull[2],
                $expectedFull[3],
                $expectedFull[4],
                $expectedFull[5],
                $expectedFull[6],
            ),
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectRightObject($type['children']['cmis:item0'])), 'execute')->toArray()
        );

        self::assertEquals(
            array(
                array(
                    'property_1'    => "cmis:item1->0->property_1->1",
                    'cmis:objectId' => '7',
                    'property_2'    => "cmis:item1->0->property_2->1",
                ),
            ),
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectRightObject($type['children']['cmis:item1'])), 'execute')->toArray()
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
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectRightObject($type['children']['cmis:item0']['children']['cmis:item00'])), 'execute')->toArray()
        );
    }
    
    public function testSelectFullObject()
    {
        $repository = $this->getRepository();
        $objectsEngine = $this->readAttribute($repository->getObjectService(), 'objectsEngine');
        $sql     = new \Zend\Db\Sql\Sql(self::$currentAdapter);

        $type = $repository->getRepositoryService()->getTypeDefinition('cmis:item');
        $type['children'] = $repository->getRepositoryService()->getTypeDescendants('cmis:item');
        
        $this->assertEquals(
            array(
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '2',
                    'property_2'        => null,
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_1'        => "cmis:item00->0->property_1->0",
                    'cmis:objectId'     => '3',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_1'        => "cmis:item00->1->property_1->1",
                    'cmis:objectId'     => '4',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '5',
                    'property_2'        => "cmis:item01->0->property_2->0",
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'property_1'        => null,
                    'cmis:objectId'     => '6',
                    'property_2'        => "cmis:item01->1->property_2->1",
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item1',
                    'cmis:name'         => 'cmis:item1->0',
                    'property_1'        => "cmis:item1->0->property_1->1",
                    'cmis:objectId'     => '7',
                    'property_2'        => "cmis:item1->0->property_2->1",
                    'property_0'        => null,
                )),
            ), 
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectFullObject($type)), 'execute')->toArray()
        );

        $this->assertEquals(
            array(
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item0',
                    'cmis:name'         => 'cmis:item0->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '2',
                    'property_2'        => null,
                    'property_0'        => 'cmis:item0->0->property_0->0',
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->0',
                    'property_1'        => "cmis:item00->0->property_1->0",
                    'cmis:objectId'     => '3',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item00',
                    'cmis:name'         => 'cmis:item00->1',
                    'property_1'        => "cmis:item00->1->property_1->1",
                    'cmis:objectId'     => '4',
                    'property_2'        => null,
                    'property_0'        => "cmis:item00->1->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->0',
                    'property_1'        => null,
                    'cmis:objectId'     => '5',
                    'property_2'        => "cmis:item01->0->property_2->0",
                    'property_0'        => "cmis:item01->0->property_0->0",
                )),
                array_replace($this->expectedPredefined['commonProperties'], array(
                    'cmis:baseTypeId'   => 'cmis:item',
                    'cmis:objectTypeId' => 'cmis:item01',
                    'cmis:name'         => 'cmis:item01->1',
                    'property_1'        => null,
                    'cmis:objectId'     => '6',
                    'property_2'        => "cmis:item01->1->property_2->1",
                    'property_0'        => "cmis:item01->1->property_0->0",
                )),
            ), 
            self::$currentAdapter->query($sql->buildSqlString($objectsEngine->_selectFullObject($type['children']['cmis:item0'])), 'execute')->toArray()
        );
    }
}
