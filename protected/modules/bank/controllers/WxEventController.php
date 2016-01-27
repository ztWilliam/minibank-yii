<?php
/**
 * 
 * User: william
 * Date: 16/1/18
 * Time: 上午11:24
 */

class WxEventController  extends Controller {

    /**
     * Sample action:
     *
     * Action name shall be what the event is.
     */
    public function actionSampleMenuClicked(){
        //Event parameters are all in the post data.
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionBankAdminMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionBankWithdrawMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionBankDepositMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionBankBalanceMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionMiniActivityMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    public function actionMiniCalculationMenuClicked(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = WeChatEventHandler::functionNotReady($eventObj);
        echo $result;
    }

    /**
     * user subscribed
     */
    public function actionSubscribed(){
        $eventObj = WeChatRequest::extractEventObj($_POST);
        $result = BankWxEventHandler::userSubscribed($eventObj);
        echo $result;
    }
}