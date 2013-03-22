<?php
return array(
	'translator' => array(
		'locale' => 'fr_FR',
		//'cache' => array('adapter'=> 'Zend\Cache\Storage\Adapter\Memcached'),
		'translation_file_patterns' => array(
			array(
				'type' => 'phparray',
				'base_dir' => __DIR__.'/_files/translations/default',
				'pattern'  => '%s.php'
			)
		),
		'translation_files' => array(
			//PhpArray
			array(
				'type' => 'phparray',
				'filename' =>  __DIR__.'/_files/translations/other-domain/french-file.php',
				'locale'  => 'fr_FR',
				'text_domain' => 'other-domain'
			),
			array(
				'type' => 'phparray',
				'filename' =>  __DIR__.'/_files/translations/other-domain/english-file.php',
				'locale'  => 'en_US',
				'text_domain' => 'other-domain'
			),
			//Ini
			array(
				'type' => 'ini',
				'filename' =>  __DIR__.'/_files/translations/ini-domain/french-file.ini',
				'locale'  => 'fr_FR',
				'text_domain' => 'ini-domain'
			),
			array(
				'type' => 'ini',
				'filename' =>  __DIR__.'/_files/translations/ini-domain/english-file.ini',
				'locale'  => 'en_US',
				'text_domain' => 'ini-domain'
			),
		)
	)
);