<?php
 
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        // autoloading model and component classes
        'import'=>array(
            'application.models.*',
            'application.components.*',

            'application.modules.wechat.components.*',
            'application.modules.wechat.components.baseUtil.*',
            'application.modules.wechat.components.wxUtil.*',
            'application.modules.wechat.components.crmUtil.*',
        ),

        'components'=>array(
            'fixture'=>array(
                'class'=>'system.test.CDbFixtureManager',
            ),
            'db'=>array(
                'class'=>'CDbConnection',
                'connectionString' => 'mysql:host=localhost;dbname=mini_bank_test',
                'emulatePrepare' => true,
                'username' => 'test',
                'password' => 'test',
                'charset' => 'utf8',
            ),
        ),
	)
);
