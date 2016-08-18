<?php
$NS = 'CmisInstall';
return array(
    /*'autoloader' => array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                $NS => __DIR__ . '/../../install',
            ),
        ),
    ),
    'view_manager' => array(
        'prefix_template_path_stack' => array(
            'cms-install' => __DIR__ . '/../../install/View',
            //$NS => __DIR__ . '/../../install/View',
        ),
        'template_map' => array(
            'layout/install'         => __DIR__ . '/../../install/View/layout.phtml',
        ),
    ),*/
    'controllers' => array(
        'invokables'    => array(
            'install_cmis' => 'CmisInstall\Controller\IndexController',
        ),
    ),
);
