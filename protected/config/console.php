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

    ),


	'components'=>array(
//        'db'=>array(
//            'class'=>'CDbConnection',
//
//            'connectionString'=>'mysql:host=localhost;dbname=ztcms',
//            'emulatePrepare'=>true,
//            'username'=>'ztcms',
//            'password'=>'ztcms+0205',
//
//            'charset'=>'utf8',
//            'tablePrefix'=>'',
//            'enableProfiling'=>true,
//            'schemaCachingDuration'=>0,
//            'enableParamLogging'=>true,
//        ),
	),
	'params' => array(
		//add parameters will be used in commands here

	),

);