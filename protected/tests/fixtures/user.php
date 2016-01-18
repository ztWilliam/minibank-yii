<?php
/**
 * 
 * User: william
 * Date: 16/1/16
 * Time: 下午8:26
 */
require_once('TestIdGenerator.php');

return array(
    'active' => array(
        'userId' => TestIdGenerator::getUserId('uId01'),
        'nickName' => 'GeneralUser1',
        'openId' => 'TestOpenIdForUser01',
        'generalId' => 'TestGeneralIdForUser01',
        'avatarUrl' => 'http://test.avatarurl.zt/test1.jpg',
        'bindTime' => '2015-08-22 09:30:01',
        'isActive' => 1,
        'delTag' => 0,
    ),
    'admin' => array(
        'userId' => TestIdGenerator::getUserId('uId02'),
        'nickName' => 'AdminUser1',
        'openId' => 'TestOpenIdForUser02',
        'generalId' => 'TestGeneralIdForUser02',
        'avatarUrl' => 'http://test.avatarurl.zt/test2.jpg',
        'bindTime' => '2015-07-22 09:30:01',
        'isActive' => 1,
        'delTag' => 0,
    ),

);