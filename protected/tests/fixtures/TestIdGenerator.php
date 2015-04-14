<?php
/**
 *
 * User: william
 * Date: 15-3-12
 * Time: 下午6:41
 */

class TestIdGenerator {
    private static $userIds;
    private static $bankIds;

    public static function getUserId($key){
        if (!is_array(self::$userIds)) {
            self::$userIds = array(
                'uId01' => CommonFunction::create_guid_timestamp('2015-01-02 15:32:31'),
                'uId02' => CommonFunction::create_guid_timestamp('2015-01-05 17:24:57'),
                'uId03' => CommonFunction::create_guid_timestamp('2015-02-05 21:12:36'),
                'uId04' => CommonFunction::create_guid_timestamp('2015-02-11 21:12:36'),
                'uId05' => CommonFunction::create_guid_timestamp('2015-02-13 21:12:36'),
                'uId06' => CommonFunction::create_guid_timestamp('2015-02-23 21:12:36'),
            );
        }

        return self::$userIds[$key];
    }

    public static function getBankId($key){
        if (!is_array(self::$bankIds)) {
            self::$bankIds = array(
                'bId01' => CommonFunction::create_guid_timestamp('2015-03-05 15:32:31'),
                'bId02' => CommonFunction::create_guid_timestamp('2015-03-06 17:24:57'),
                'bId03' => CommonFunction::create_guid_timestamp('2015-03-12 19:12:36'),
                'bId04' => CommonFunction::create_guid_timestamp('2015-03-13 21:12:36'),
                'bId05' => CommonFunction::create_guid_timestamp('2015-03-15 23:12:36'),
            );
        }

        return self::$bankIds[$key];
    }

}