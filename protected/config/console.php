<?php
 
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	// application components
    'import'=>array(
        'application.commands.*',
        'application.models.*',
        'application.components.*',
    ),

    'commandMap'=>array(
        'migrate'=>array(
            'class'=>'system.cli.commands.MigrateCommand',
            'migrationPath'=>'application.migrations',
            'migrationTable'=>'tbl_migration',
            'connectionID'=>'test_db',
            'interactive' => false,
        ),

    ),


	'components'=>array(
        'db'=>array(
            'class'=>'CDbConnection',

            'connectionString'=>'mysql:host=localhost;dbname=mini_bank_dev',
            'emulatePrepare'=>true,
            'username'=>'[your_user_name]',
            'password'=>'[your_password]',

            'charset'=>'utf8',
            'tablePrefix'=>'',
            'enableProfiling'=>true,
            'schemaCachingDuration'=>0,
            'enableParamLogging'=>true,
        ),

        'test_db'=>array(
            'class'=>'system.db.CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=mini_bank_test',
            'emulatePrepare' => true,
            'username' => 'test',
            'password' => 'test',
            'charset' => 'utf8',
        ),

    ),
	'params' => array(
		//add parameters will be used in commands here

	),

);