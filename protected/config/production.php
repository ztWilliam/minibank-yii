<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			// uncomment the following to use a MySQL database
            'db' => array(
                'connectionString' => 'mysql:host=[YOUR DB HOST];dbname=[YOUR DB NAME]',

                'class' => 'CDbConnection',
                'emulatePrepare' => true,
                'username' => '[YOUR_DB_USER]',
                'password' => '[YOUR_DB_PASSWORD]',

                'charset' => 'utf8',
                'tablePrefix' => '',
                'enableProfiling' => true,
                'schemaCachingDuration' => 0,
                'enableParamLogging' => true,
            ),
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning',
					)
				),
			),

            /**
             * 访问微信api时，需初始化的信息
             */
            'wxApiHelper' => array(
                "class" => "application.modules.wechat.components.wxUtil.ZtWxApiHelper",
                "hostUrl" => "http://core.wx.zhengtuo.net/wxApi/admin/",
            ),

        ),

        'params'=>array(
            //It is recommed that you should save the information to your database.
            //暂存微信公众号的id
            'ghDefinition' => array(
                'ghApiId' => '[YOUR_ID]', //after registration, you must update this api id
                'ghName' => '[YOUR_NAME]',
                'ghId' => '[YOUR_GH_ID]',
                'appId' => '[YOUR_APP_ID]',
                'secret' => '[YOUR_APP_SECRET]',
                'ghDesc' => '[YOUR_DESCRIPTION]',
            ),
        ),
    )
);

