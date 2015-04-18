<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../vendor/yiisoft/yii/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

// Support PHPUnit <=3.7 and >=3.8
if (@include_once('PHPUnit/Framework/TestCase.php')===false) // <= 3.7
    require_once('src/Framework/TestCase.php'); // >= 3.8


// make sure non existing PHPUnit classes do not break with Yii autoloader
Yii::$enableIncludePath = false;
Yii::setPathOfAlias('tests', dirname(__FILE__));
Yii::import('tests.*');

//new TestApplication($config);

Yii::createWebApplication($config);
