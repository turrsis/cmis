<?php
namespace CmisTest\Cmis\Cmis\Services;

use Cmis\Cmis\Repository;
use Zend\Db\Adapter\Adapter;
//use CmisTest\Cmis\InstallCmis;
use Zend\Db\Sql\Platform\Platform as SqlPlatform;

abstract class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    public static $dbConfig = 'Cmis';
    /**
     * @var Adapter
     */
    public static $dbAdapter = 'mysqli';
    
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

    public function setUp()
    {
        static::$repository = new Repository(array(
            'adapter'           => static::$dbAdapter,
            'sql_builder'       => new \Zend\Db\Sql\Builder\Builder(),
            'common_table'      => 'cmis_object',
            'repository_engine' => 'Cmis\Cmis\Services\Rdb\RepositoryEngine',
            'sequrity_engine'   => 'Cmis\Cmis\Services\Rdb\SequrityEngine',
            'objects_engine'    => 'Cmis\Cmis\Services\Rdb\ObjectsEngine',
        ));
    }
    /*public static function setUpBeforeClass()
    {   
        include_once './module/Cmis/install/InstallDB.php';
        
        $installCmis = new \InstallCMIS\InstallDB();
        $dbConfig = array(
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'pass'   => '',
            'driver' => 'pdo_mysql',
        );
        $dbConfig['dbname'] = $installCmis->createDataBase($dbConfig);
        self::$adapter = $installCmis->installDataBase($dbConfig);
        self::$adapter->setSqlPlatform(new SqlPlatform(self::$adapter));

        self::$repository = new Repository(array(
            'adapter'           => self::$adapter,
            'common_table'      => 'cmis_object',
            'repository_engine' => 'Cmis\Cmis\Services\Rdb\RepositoryEngine',
            'sequrity_engine'   => 'Cmis\Cmis\Services\Rdb\SequrityEngine',
            'objects_engine'    => 'Cmis\Cmis\Services\Rdb\ObjectsEngine',
        ));
        
    }*/
    
    /*public static function tearDownAfterClass()
    {
        $dbname = self::readAttribute(self::$adapter->getDriver()->getConnection(), 'connectionParameters')['dbname'];
        self::$adapter->query('DROP DATABASE ' . $dbname, 'execute');
    }*/
}