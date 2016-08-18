<?php
return array(
    'route_manager' => array(
        'invokables' => array(
            'controller_params_config' => array(
                'options'  => array(
                    'routes' => array(
                        'cmis-type' => array(
                            'edit'      => '/:type',
                        ),
                        'cmis-object' => array(
                            'edit'      => '/:object',
                        ),
                        'cmis-users' => array(
                            'edit'      => '/:object',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
