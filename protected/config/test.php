<?php
 
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        'components'=>array(
            'fixture'=>array(
                'class'=>'system.test.CDbFixtureManager',
            ),
//            'db'=>array(
//                'connectionString' =>
//                'mysql:host=localhost;dbname=ztcms_test',
//                'emulatePrepare' => true,
//                'username' => 'ztcms',
//                'password' => 'ztcms+0205',
//                'charset' => 'utf8',
//            ),
        ),
	)
);
