<?php

return array(
    'controllers' => array(
        'invokables' => array(
        	'CitOrder\Controller\Site' => 'CitOrder\Controller\SiteController',
        	'CitOrder\Controller\SiteContact' => 'CitOrder\Controller\SiteContactController',
        	'CitOrder\Controller\Stock' => 'CitOrder\Controller\StockController',
        	'CitOrder\Controller\Order' => 'CitOrder\Controller\OrderController',
        	'CitOrder\Controller\OrderProduct' => 'CitOrder\Controller\OrderProductController',
        	'CitOrder\Controller\OrderProductOption' => 'CitOrder\Controller\OrderProductOptionController',
        	'CitOrder\Controller\OrderWithdrawal' => 'CitOrder\Controller\OrderWithdrawalController',
        ),
    ),
 
    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'CitOrder\Controller\Site',
                        'action'     => 'index',
                    ),
                ),
          		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       		),
            ),
        	'order' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/order',
        						'defaults' => array(
        								'controller' => 'CitOrder\Controller\Order',
        								'action'     => 'index',
        						),
        				),
        				'may_terminate' => true,
        				'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index',
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'add' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/add',
        										'defaults' => array(
        												'action' => 'add',
        										),
        								),
        						),
        						'detail' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/detail[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'detail',
        										),
        								),
        						),
        						'update' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/update[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'update',
        										),
        								),
        						),
        						'generate' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/generate[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'generate',
        										),
        								),
        						),
        						'validationRequest' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/validation-request[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'validationRequest',
        										),
        								),
        						),
        						'validate' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/validate[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'validate',
        										),
        								),
        						),
        						'retraction' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/retraction[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'retraction',
        										),
        								),
        						),
        						'pdf' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/pdf[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'pdf',
        										),
        								),
        						),
        						'delete' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/delete[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'delete',
        										),
        								),
        						),
        						'import' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/import[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'import',
        										),
        								),
        						),
        						'start' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/start[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'start',
        										),
        								),
        						),
        				),
        		),
        	'orderProduct' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/order-product',
        						'defaults' => array(
        								'controller' => 'CitOrder\Controller\OrderProduct',
        								'action'     => 'index',
        						),
        				),
        				'may_terminate' => true,
        				'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'add' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/add[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'add',
        										),
        								),
        						),
        						'update' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/update[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'update',
        										),
        								),
        						),
        						'delete' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/delete[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'delete',
        										),
        								),
        						),
        				),
        		),
        	'orderProductOption' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/order-product-option',
        						'defaults' => array(
        								'controller' => 'CitOrder\Controller\OrderProductOption',
        								'action'     => 'index',
        						),
        				),
        				'may_terminate' => true,
        				'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        				),
        		),
        	'orderWithdrawal' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/order-withdrawal',
        						'defaults' => array(
        								'controller' => 'CitOrder\Controller\OrderWithdrawal',
        								'action'     => 'index',
        						),
        				),
        				'may_terminate' => true,
        				'child_routes' => array(
        						'index' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/index[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'index',
        										),
        								),
        						),
        						'add' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/add[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'add',
        										),
        								),
        						),
        						'update' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/update[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'update',
        										),
        								),
        						),
        						'delete' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/delete[/:id]',
        										'constraints' => array(
        												'id'     => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'delete',
        										),
        								),
        						),
        				),
        		),
        	'site' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/site',
                    'defaults' => array(
                        'controller' => 'CitOrder\Controller\Site',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'index' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/index',
            								'defaults' => array(
            										'action' => 'index',
            								),
            						),
            				),
            				'add' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add',
            								'defaults' => array(
            										'action' => 'add',
            								),
            						),
            				),
            				'detail' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/detail[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'detail',
            								),
            						),
            				),
            				'update' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            				'import' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/import[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'import',
            								),
            						),
            				),
            				'edit' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/edit[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'edit',
            								),
            						),
            				),
            				'delete' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/delete[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'delete',
            								),
            						),
            				),
            			),
            ),
            'siteContact' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/site-contact',
                    'defaults' => array(
                        'controller' => 'CitOrder\Controller\SiteContact',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'index' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/index[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'index',
            								),
            						),
            				),
            				'add' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add[/:id]',
            								'defaults' => array(
            										'action' => 'add',
            								),
            						),
            				),
            				'update' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            				'delete' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/delete[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'delete',
            								),
            						),
            				),
            			),
            ),
        	'stock' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/stock',
                    'defaults' => array(
                        'controller' => 'CitOrder\Controller\Stock',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'index' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/index',
            								'defaults' => array(
            										'action' => 'index',
            								),
            						),
            				),
            				'add' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add',
            								'defaults' => array(
            										'action' => 'add',
            								),
            						),
            				),
            				'update' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
             				'import' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/import[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'import',
            								),
            						),
            				),
            				'delete' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/delete[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'delete',
            								),
            						),
            				),
            			),
            		),
        ),
    ),
		
    'view_manager' => array(
    	'strategies' => array(
    			'ViewJsonStrategy',
    	),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'master' => __DIR__ . '/../view',
        ),
    ),
/*	'service_manager' => array(
		'factories' => array(
				'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
		),
	),*/
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),
);
