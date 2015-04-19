<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-3-12
 * Time: 上午11:58
 * To change this template use File | Settings | File Templates.
 */
require_once('TestIdGenerator.php');

return array(
    'static' => array(
        'bankId' => TestIdGenerator::getBankId('bId01'),
        'bankName' => 'Bank Of Mini China',
        'createUserId' => TestIdGenerator::getUserId('uId01'),
        'createTime' => '2014-12-21 03:13:23',
        'delTag' => 0,
    ),
    'deleted' => array(
        'bankId' => TestIdGenerator::getBankId('bId02'),
        'bankName' => 'Bank Of East Family',
        'createUserId' => TestIdGenerator::getUserId('uId02'),
        'createTime' => '2014-12-31 06:14:23',
        'delTag' => 1,
    ),
    'toBeDeleted' => array(
        'bankId' => TestIdGenerator::getBankId('bId03'),
        'bankName' => '林记大银行',
        'createUserId' => TestIdGenerator::getUserId('uId02'),
        'createTime' => '2015-01-18 07:13:33',
        'delTag' => 0,
    ),
    'toBeCreated' => array(
        'bankId' => TestIdGenerator::getBankId('bId04'),
        'bankName' => '欧阳家的小银行',
        'createUserId' => TestIdGenerator::getUserId('uId03'),
        'createTime' => '2015-02-20 19:13:23',
        'delTag' => 1,
    ),
    'toBeModified' => array(
        'bankId' => TestIdGenerator::getBankId('bId05'),
        'bankName' => '名字一定要响亮',
        'createUserId' => TestIdGenerator::getUserId('uId04'),
        'createTime' => '2015-04-08 17:13:23',
        'delTag' => 0,
    ),

);