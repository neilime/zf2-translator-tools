<?php
return array(
	'translator' => array(
		'locale' => 'fr_FR',
		//'cache' => array('adapter'=> 'Zend\Cache\Storage\Adapter\Memcached'),
		'translation_file_patterns' => array(
			array(
				'type' => 'phparray',
				'base_dir' => __DIR__.'/_files/default',
				'pattern'  => '%s.php'
			)
		),
		'translation_files' => array(
			array(
				'type' => 'phparray',
				'filename' =>  __DIR__.'/_files/other-domain/french-file.php',
				'locale'  => 'fr_FR',
				'text_domain' => 'other-domain'
			),
			array(
				'type' => 'phparray',
				'filename' =>  __DIR__.'/_files/other-domain/english-file.php',
				'locale'  => 'en_US',
				'text_domain' => 'other-domain'
			)
		)
	)
);