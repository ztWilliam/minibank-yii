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

    public static function hotTailsMenuClicked($eventObj)
    {
        $ghId = $eventObj['ToUserName'];
        $toUserName = $eventObj['FromUserName'];

        try{

            $limitCount = WeChatCommon::NEWS_LIST_MAX_ITEM_COUNT;

            $tails = TripViewer::hotTripTails($toUserName, $limitCount, false);

            if(count($tails) == 0) {
                $msg = "暂时没有尾单";
                return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);
            }

            $result = array();
            foreach($tails as $tail) {
                $result[] = WeChatCommon::makeArticleItem($tail->title, $tail->picUrl,
                    $tail->url, $tail->description);
            }

            return WeChatCommon::articlesMessage($toUserName, $ghId, time(), $result);

        }catch(WxAppException $ex){
            $msg = $ex->getMessage();
            return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);
        } catch (Exception $ex){
            Yii::log(__METHOD__ . '最新发表的公开文章 菜单处理失败. 错误信息：' . $ex->getMessage(), 'error');
            $msg = "系统忙，请稍后重试";
            return WeChatCommon::textMessage($toUserName, $ghId, time(), $msg);
        }

    }
}