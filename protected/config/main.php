<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'[Your application name]',

	'timezone'=>'PRC',

	'sourceLanguage'=>'en_us',
	'language'=>'zh_cn',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

        'application.modules.wechat.components.*',
        'application.modules.wechat.components.baseUtil.*',
        'application.modules.wechat.components.wxUtil.*',
        'application.modules.wechat.components.crmUtil.*',
	),
	// application components
	'components'=>array(
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info, trace',
					'maxFileSize'=>5000,
					'maxLogFiles'=>30,
				),
			),
		),

		'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName' => false,
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
		),
	),

	'modules'=>array(
        'wechat'=>array(),
        'wap' => array(),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
    ),
);