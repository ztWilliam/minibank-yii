<?php
/**
 * 测试地址：
 *
 * http://mini.zhengtuo.net/
 *
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-8-29
 * Time: 下午12:55
 * To change this template use File | Settings | File Templates.
 */

class AdminController extends Controller {

    public function actionRegisterWxApi(){
        // 在微信api服务器上注册本微信公众号的app
        $ghId = Yii::app()->params['ghDefinition']['ghId'];
        $corpname = Yii::app()->params['ghDefinition']['ghName'];
        $appId = Yii::app()->params['ghDefinition']['appId'];
        $appSecret = Yii::app()->params['ghDefinition']['secret'];
        $ghDesc = Yii::app()->params['ghDefinition']['ghDesc'];

        $regResult = ZtWxApiAdapter::registerGH($ghId, $corpname,
            $appId, $appSecret, $ghDesc);

        if(isset($regResult)) {
            echo FastJSON::encode($regResult);
        } else {
            throw new WxAppException('公众号注册失败');
        }

    }

    public function actionRefreshMenu(){
        // 重新刷新微信的菜单
        $ghApiId = SystemOption::getWeChatApiId();
        if($ghApiId > 0) {
            // 注册关注事件处理
            $subscribedHandler = Yii::app()->createAbsoluteUrl(CommonDefinition::USER_SUBSCRIBED_HANDLER);
            ZtWxApiAdapter::setSubscribeHandler($ghApiId, $subscribedHandler);

            WechatMenu::refresh($ghApiId, BankWxMenu::menuItems());
        } else {
            echo '公众号尚未注册';
        }

    }

}