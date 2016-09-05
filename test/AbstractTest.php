<?php
/**
 * @link      http://github.com/zendframework/zend-router for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace TurrsisTest\Cmis;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Db;
use Turrsis\ComponentInstaller\Scripts\DbInstaller;
use Composer\IO\IOInterface;

class AbstractTest extends TestCase
{
    protected static $scripts = [
        'default' => __DIR__ . '\..\db\config.php'
    ];
    protected static $currentAdapter;
    protected static $adapters = [
        'mysql' => [
            'driver'   => 'Mysqli',
            'database' => 'test',
            'username' => 'root',
            'password' => '',
            'hostname' => '127.0.0.1',
            'port'     => '3306',
        ],
    ];

    public static function deleteDir($path)
    {
        //@mkdir($this->cachePath, 0777, true);
        return is_file($path) ?
                @unlink($path) :
                array_map(__CLASS__ . '::' . __FUNCTION__, glob($path.'/*')) == @rmdir($path);
    }

    /**
     * @param string $adapter
     * @return Db\Adapter\Adapter
     */
    protected static function getAdapter($adapter = 'mysql')
    {
        if (!self::$currentAdapter) {
            self::$currentAdapter = new Db\Adapter\Adapter(self::$adapters[$adapter]);
            self::$currentAdapter->setSqlBuilder(new Db\Sql\Builder\Builder());
        }
        return self::$currentAdapter;
    }

    protected static function initDatabase($adapter = 'mysql', $script = 'default')
    {
        $installer = new DbInstaller();

        $package = 'cmis';
        $installer->setApplicationConfig($package, [
            'application_path' => __DIR__ . '/../',
            'module_paths' => [
                __DIR__ . '/../../../'
            ],
            'db' => self::getAdapter($adapter),
        ]);
        $installer->uninstall($package);
        $installer->upgrade($package);
    }

    protected static function initRepositoryItems(\Turrsis\Cmis\Repository $repository)
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

        $itemId = $repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item0',
            'cmis:name'         => 'cmis:item0->0',
            'property_0'        => 'cmis:item0->0->property_0->0',
        ));
            $itemId = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis:item00->0',
                'property_0'        => 'cmis:item00->0->property_0->0',
                'property_1'        => 'cmis:item00->0->property_1->0',
            ));

            $itemId = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item00',
                'cmis:name'         => 'cmis:item00->1',
                'property_0'        => 'cmis:item00->1->property_0->0',
                'property_1'        => 'cmis:item00->1->property_1->1',
            ));

            $itemId = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item01',
                'cmis:name'         => 'cmis:item01->0',
                'property_0'        => 'cmis:item01->0->property_0->0',
                'property_2'        => 'cmis:item01->0->property_2->0',
            ));

            $itemId = $repository->getObjectService()->createItem(array(
                'cmis:objectTypeId' => 'cmis:item01',
                'cmis:name'         => 'cmis:item01->1',
                'property_0'        => 'cmis:item01->1->property_0->0',
                'property_2'        => 'cmis:item01->1->property_2->1',
            ));

        $itemId = $repository->getObjectService()->createItem(array(
            'cmis:objectTypeId' => 'cmis:item1',
            'cmis:name'         => 'cmis:item1->0',
            'property_1'        => 'cmis:item1->0->property_1->1',
            'property_2'        => 'cmis:item1->0->property_2->1',
        ));
    }

    /**
     * @param string $repositoryName
     * @param array $config
     * @return \Turrsis\Cmis\Repository
     */
    protected function getRepository($repositoryName = 'cmis:repo:repo1', $config = [])
    {
        $config = ArrayUtils::merge(['services' => [
            'db:test' => self::$currentAdapter,
        ]], $config);
        return $this->getServiceManager($config)->get($repositoryName);
    }

    protected function getServiceManager($config = [])
    {
        $defaultConfig = ArrayUtils::merge(
            ArrayUtils::merge(
                (new \Zend\Db\ConfigProvider)->getDependencyConfig(),
                (new \Turrsis\Cmis\ConfigProvider)->getDependencyConfig()
            ),
            [
                'services' => [
                    'Config' => [
                        'db' => [
                            'adapters' => [
                                'db:test' => [
                                    'driver' => 'mysqli',
                                ],
                            ]
                        ],
                        'cmis_repositories' => [
                            'repo1' => [
                                'adapter' => 'db:test',
                                'repositoryEngine' => 'Turrsis\Cmis\Services\Rdb\RepositoryEngine',
                                'sequrityEngine'   => 'Turrsis\Cmis\Services\Rdb\SequrityEngine',
                                'objectsEngine'    => 'Turrsis\Cmis\Services\Rdb\ObjectsEngine',
                            ],
                        ],
                    ],
                ],
            ]
        );
        return new ServiceManager(ArrayUtils::merge($defaultConfig, $config));
    }

    protected function _getObjectsIdSnapshot(array $excludeIds = [])
    {
        $meta = new Db\Sql\Select(array('p1' => 'cmis:meta_types_properties'));
        $meta
            ->quantifier('DISTINCT')
            ->columns(array(
                'localNamespace' => new Db\Sql\Predicate\IfPredicate(
                    new Db\Sql\Predicate\IsNotNull('p1.localNamespace'),
                    ['p1.localNamespace', 'identifier'],
                    ['p2.localNamespace', 'identifier']
                ),
            ))
            ->join(
                ['p2'=>'cmis:meta_properties'],
                new Db\Sql\Predicate\Operator(
                    ['p1.propertyId','identifier'],
                    "=", 
                    ['p2.propertyId','identifier']
                ),
                array(),
                'left'
            );

        $snapshot = [];
        $builder = new Db\Sql\Builder\Builder(self::$currentAdapter);
        foreach(self::$currentAdapter->query($builder->buildSqlString($meta), 'execute') as $object) {
            $localNS = $object['localNamespace'];
            $localNSIds = new Db\Sql\Select(['ns' => $localNS]);
            $localNSIds->join(
                ['o'=>'cmis_object'],
                new Db\Sql\Predicate\Operator(
                    ['o.cmis_objectId','identifier'],
                    "=", 
                    ['ns.cmis_objectId','identifier']
                ),
                ['*'],
                'left'
            );
            $localNSIds = self::$currentAdapter->query($builder->buildSqlString($localNSIds), 'execute')->toArray();
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
