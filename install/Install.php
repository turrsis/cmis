<?php
namespace InstallCMIS;

use Zend\Installer\AbstractInstaller;

class Install extends AbstractInstaller
{
    public function install()
    {
        $this->getDbAdapter(array(
            'driver' => 'pdo_mysql',
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'pass'   => '',
        ));
        
        $dbScript = $this->getDbScript('InstallDBConfig.php');
        
        $this->adapter->query($dbScript, 'execute');
    }
}
