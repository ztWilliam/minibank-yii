<?php
 
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        'components'=>array(
            'fixture'=>array(
                'class'=>'system.test.CDbFixtureManager',
            ),
            'db'=>array(
                'connectionString' =>
                'mysql:host=localhost;dbname=mini_bank_test',
                'emulatePrepare' => true,
                'username' => 'test',
                'password' => '',
                'charset' => 'utf8',
            ),
        ),
	)
);
