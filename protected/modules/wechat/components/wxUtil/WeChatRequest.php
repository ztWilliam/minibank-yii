<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-11-25
 * Time: 下午8:41
 * To change this template use File | Settings | File Templates.
 */

class WeChatRequest {

    public static function extractEventObj($params) {
        $objPost = $params['eventObj'];
        if(!isset($objPost)) {
            Yii::log('The event object body is nothing ' , 'warning');
            echo "";
            exit;
        }

        $eventObj = json_decode($objPost, true);

        return $eventObj;
    }

    public static function extractEventParameters($params)
    {
        $parameters = $params['parameters'];
        if(!isset($parameters)) {
            return null;
        }

        if(trim($parameters) == '')
            return null;
        else
            return FastJSON::decode($params['parameters']);
    }

    public static function extractMessageObj($params)
    {
        $objPost = $params['msgObj'];
        if(!isset($objPost)) {
            Yii::log('The message object body is nothing ' , 'warning');
            echo "";
            exit;
        }

        $msgObj = json_decode($objPost, true);

        return $msgObj;
    }

}