<?php
/**
 * 测试地址：
 *
 * http://wx.test.zhengtuo.net/trip/
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
        //todo 重新刷新微信的菜单
        $ghApiId = Yii::app()->params['ghDefinition']['ghApiId'];
        if($ghApiId > 0) {
            WechatMenu::refresh($ghApiId);
        } else {
            echo '公众号尚未注册';
        }

    }

}