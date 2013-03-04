<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'TranslatorTools\Controller\Tools' => 'TranslatorTools\Controller\ToolsController'
		)
	),
	'console' => array(
		'router' => array(
			'routes' => array(
				'remove-translations' => array(
					'options' => array(
						'route'    => 'removeTranslations',
						'defaults' => array(
							'controller' => 'TranslatorTools\Controller\Tools',
							'action' => 'removeTranslations'
						)
					)
				)
			)
		)
	),
	'service_manager' => array(
        'factories' => array(
        	'TranslatorToolsService' => '\TranslatorTools\Factory\ServiceFactory'
        )
    ),
    'translator_tools' => array()
);