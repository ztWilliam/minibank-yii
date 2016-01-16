<?php
 
return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
        'components' => array(
            // uncomment the following to use a MySQL database
//            'db' => array(
//                'connectionString' => 'mysql:host=localhost;dbname=ztcms',
//
//                'class' => 'CDbConnection',
//                'emulatePrepare' => true,
//                'username' => 'ztcms',
//                'password' => 'ztcms+0205',
//
//                'charset' => 'utf8',
//                'tablePrefix' => '',
//                'enableProfiling' => true,
//                'schemaCachingDuration' => 0,
//                'enableParamLogging' => true,
//            ),
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'error, warning, trace',
                    ),
                    array(
                        'class' => 'CProfileLogRoute',
                    ),
                    /*
                    array(
                        'class'=>'CWebLogRoute',
                    ),
                    */
                ),
            ),

            'urlManager' => array(
                'rules' => array(
//                    'gii' => 'gii',
//                    'gii/<controller:\w+>' => 'gii/<controller>',
//                    'gii/<controller:\w+>/<action:\w+>' => 'gii/<controller>/<action>',
                ),

            ),

            /**
             * 访问微信api时，需初始化的信息
             */
            'wxApiHelper' => array(
                "class" => "application.modules.wechat.components.wxUtil.ZtWxApiHelper",
                "hostUrl" => "http://core.wx.dev.zhengtuo.net/wxApi/admin/",
            ),

        ),
        'modules' => array(
            // uncomment the following to enable the Gii tool
//            'gii' => array(
//                'class' => 'system.gii.GiiModule',
//                'password' => 'sa',
//                // If removed, Gii defaults to localhost only. Edit carefully to taste.
//                'ipFilters' => array('127.0.0.1', '::1'),
//            ),
        ),
        'params' => array(

            //It is recommed that you should save these informations to your database.
            //暂存微信公众号的id
            'ghDefinition' => array(
                'ghApiId' => 1, //after registration, you must update this api id
                'ghName' => 'MiniBank',
                'ghId' => 'gh_29feb7e0093c',
                'appId' => 'wx61f2b244262db5dd',
                'secret' => '9fdc04d2a1671a620a63b0de70ebf232',
                'ghDesc' => 'MiniBank，for kids',
            ),

        ),
    )
);
