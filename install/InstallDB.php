<?php
namespace InstallCMIS;

use Zend\Db\Sql;
use Zend\Db\Adapter\Adapter;

class InstallDB
{
        /**
     * @var Adapter */
    protected $adapter;
    
    public function installDataBase($adapterParams)
    {
        /*$adapterParams = array(
            'host'   => $host,
            'user'   => $user,
            'pass'   => $pass,
            'driver' => $driver,
            'dbname' => $dbname,
        );*/
        /*if (!isset($adapterParams['dbname'])) {
            $adapterParams['dbname'] = $this->createDataBase($adapterParams);
        }*/
        
        $this->adapter = new Adapter($adapterParams);
        $sql = new Sql\Sql($this->adapter);
        $this->adapter->setSqlPlatform($sql->getSqlPlatform());
        
        $config = include 'InstallDBConfig.php';
        $this->createTables($config['createTables']);
        $this->insertTablesData($config['insertTablesData']);
        
        return $this->adapter;
    }
    
    public function createDataBase($params)
    {
        if (isset($params['dbname'])) {
            $dbName = $params['dbname'];
            unset($params['dbname']);
        } else {
            $dbName = 'tgs_UnitTest_Cmis_' . rand(1000, 9000);
        }
        
        $adapter = new Adapter($params);
        $quotedDbName = $adapter->getPlatform()->quoteIdentifier($dbName);
        $adapter->query(array(
            'DROP DATABASE IF EXISTS ' . $quotedDbName,
            'CREATE DATABASE ' . $quotedDbName,
        ), Adapter::QUERY_MODE_EXECUTE);

        return $dbName;
    }
    
    protected function getTables($config)
    {
        $tablesList = array();
        foreach($config as $key => $keyConfig) {
            $tablesList[$key] = isset($keyConfig['localName'])
                    ? $keyConfig['localName']
                    : $key;
        }
        if (isset($config['cmis:meta_types'])) {
            $typesTable = isset($config['cmis:meta_types']['localName'])
                    ? $config['cmis:meta_types']['localName']
                    : 'cmis:meta_types';
            $typesList = new Sql\Select($typesTable);
            $typesList->columns(array('id', 'localName'));
            $typesList = $this->adapter->query($typesList, 'execute')->toArray();
            $tablesList = array_replace(
                $tablesList,
                array_column($typesList, 'localName', 'id')
            );
        }
        return $tablesList;
    }
    
    protected function dropTables($config)
    {
        $tablesList = $this->getTables($config);
        foreach($tablesList as $alias => $localName) {
            $tablesList[$alias] = new Sql\Ddl\DropTable($localName);
            $tablesList[$alias] = 'DROP TABLE ' . $this->adapter->getPlatform()->quoteIdentifier($localName) . ' IF EXISTS;';
        }
        $this->adapter->query($tablesList, 'execute');
    }
    
    protected function clearTables($config)
    {
        $tablesList = $this->getTables($config);
        $errors = array();
        foreach($tablesList as $alias => $localName) {
            try {
                $this->adapter->query(new Sql\Delete($localName), 'execute');
            } catch (\Exception $ex) {
                $errors[$alias] = $ex->getMessage();
            }
        }
        return $errors;
    }
    
    protected function createTables($config)
    {
        foreach($config as $tableName => $tableConfig) {
            $tableName = isset($tableConfig['localName'])
                    ? $tableConfig['localName']
                    : $tableName;
            $query = new Sql\Ddl\CreateTable($tableName);

            foreach($tableConfig['column'] as $columnName => $columnConfig) {
                if ($columnConfig['type'] == 'Varchar') {
                    $column = 'Zend\Db\Sql\Ddl\Column\\' . $columnConfig['type'];
                    $column = new $column($columnName, $columnConfig['length']);
                    unset($columnConfig['length']);
                } else {
                    $column = 'Zend\Db\Sql\Ddl\Column\\' . $columnConfig['type'];
                    $column = new $column($columnName);
                }
                unset($columnConfig['type']);
                foreach($columnConfig as $k=>$v) {
                    $method = 'set' . ucfirst($k);
                    if ($method == 'setOptions') {
                        continue;
                    }
                    if (method_exists($column, $method)) {
                        $column->$method($v);
                        unset($columnConfig[$k]);
                    }
                }
                $column->setOptions($columnConfig);
                $query->addColumn($column);
            }
            
            foreach($tableConfig['constraint'] as $constraintConfig) {
                $constraint = 'Zend\Db\Sql\Ddl\Constraint\\' . $constraintConfig['type'];
                unset($constraintConfig['type']);
                $constraint = new $constraint();
                
                foreach($constraintConfig as $k=>$v) {
                    $method = 'set' . ucfirst($k);
                    if (method_exists($constraint, $method)) {
                        $constraint->$method($v);
                        unset($constraintConfig[$k]);
                    }
                }
                $query->addConstraint($constraint);
            }
            
            $this->adapter->query($query, 'execute');
        }
    }
    
    protected function insertTablesData($config)
    {
        foreach($config as $insertConfig) {
            $query = new Sql\Insert($insertConfig['table']);
            foreach($insertConfig['values'] as $values) {
                $query->values($values);
                $this->adapter->query($query, 'execute');
            }
        }
    }
}
