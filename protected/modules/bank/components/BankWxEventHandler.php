<?php
/**
 * 
 * User: william
 * Date: 16/1/18
 * Time: 上午11:33
 */

class BankWxEventHandler {

    public static function userSubscribed($eventObj)
    {
        $ghId = $eventObj['ToUserName'];
        $toUserName = $eventObj['FromUserName'];

        try{
            $user = User::findByOpenId($toUserName);
            if(!isset($user)){
                //if first subscribed, create user
                $user = User::createNewUser($toUserName);
                $welcome = BankMessages::WELCOME_MESSAGE_TO_NEW_USER;
            } else {
                // if has subscribed before, then active user
                $user->activate();
                $welcome = BankMessages::WELCOME_MESSAGE_TO_OLD_USER;
            }

            $bank = Bank::findByUser($user->userId);
            $linkMessage = '';
            if(!isset($bank)){
                $tip = BankMessages::TIP_MESSAGE_CREATE_YOUR_OWN_BANK;

                $authUrl = self::authUrlOfBankAdminWizard();

                $linkMessage = sprintf(CommonDefinition::HYPER_LINK_TEXT, $authUrl, $tip);


            } elseif ($bank->infoCompleted($user->userId)){
                //if bank of user has been finished configuration, needn't return bank-config url.
                $linkMessage = '';

            } else {
                $tip = $bank->whatNeedSet();

                $authUrl = self::authUrlOfBankAdminWizard();

                $linkMessage = sprintf(CommonDefinition::HYPER_LINK_TEXT, $authUrl, $tip);
            }

            //build the welcome message.
            $msg = $welcome . PHP_EOL . $linkMessage;

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

    /**
     * @return mixed
     */
    private static function authUrlOfBankAdminWizard()
    {
        $url = Yii::app()->createAbsoluteUrl(BankDefinition::HANDLER_BANK_SETTING);
        $authUrl = ZtWxApiAdapter::getAuthUrl(SystemOption::getWeChatApiId(), $url);
        return $authUrl;
    }
}