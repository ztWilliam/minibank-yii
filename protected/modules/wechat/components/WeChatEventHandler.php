<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-8-29
 * Time: 下午4:16
 * To change this template use File | Settings | File Templates.
 */
Yii::import('application.modules.trip.components.*');
Yii::import('application.modules.trip.models.*');
class WeChatEventHandler {

    public static function functionNotReady($eventObj)
    {
        $ghId = $eventObj['ToUserName'];
        $toUserName = $eventObj['FromUserName'];

        try{

            $msg = '即将推出，敬请期待';
            return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);

        }catch(WxAppException $ex){
            $msg = $ex->getMessage();
            return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);
        } catch (Exception $ex){
            Yii::log(__METHOD__ . '菜单处理失败. 错误信息：' . $ex->getMessage(), 'error');
            $msg = "系统忙，请稍后重试";
            return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);
        }

    }
}