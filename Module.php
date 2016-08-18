<?php
namespace Cmis;

use Zend\ModuleManager\AbstractModule;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $autoloadConfig = true;

    public function onBootstrap(MvcEvent $e)
    {
        return;
        $serviceManager = $e->getApplication()->getServiceManager();
        $cmis = $serviceManager->get('cmis');
        $cmisAware = function ($instance, $sm) use ($cmis) {
            if ($instance instanceof Cmis\CmisAwareInterface) {
                $instance->setCmis($cmis);
            }
        };
        $serviceManager->addInitializer($cmisAware);
        $formElementManager = $serviceManager->get('formElementManager');
        $formElementManager->addInitializer($cmisAware);
    }

    public function getAdminMenu()
    {
        return array(
            array(
                'label' => 'cmis',
                'uri' => '#cmis',
                'pages' => array(
                    array(
                        'label' => 'types',
                        'controller' => 'cmis-type',
                        'action'     => 'index',
                    ),
                    array(
                        'label' => 'folders',
                        'controller' => 'cmis-folder',
                        'action'     => 'index',
                    ),
                ),
            ),
            array(
                'label' => 'Settings',
                'uri' => '#Settings',
                'pages' => array(
                    array(
                        'label' => 'navigation',
                        'controller' => 'cmis-navigation',
                        'action'     => 'index',
                    ),
                    array(
                        'label' => 'pages',
                        'controller' => 'cmis-pages',
                        'action'     => 'index',
                    ),
                ),
            ),
            array(
                'label' => 'Sequrity',
                'uri' => '#Sequrity',
                'pages' => array(
                    array(
                        'label' => 'users',
                        'controller' => 'cmis-users',
                        'action'     => 'index',
                    ),
                    array(
                        'label' => 'roles',
                        'controller' => 'cmis-roles',
                        'action'     => 'index',
                        'params' => array(
                            'folder'     => 19,
                        ),
                    ),
                ),
            ),
        );
    }
}
