<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-9-22
 * Time: 上午11:19
 * To change this template use File | Settings | File Templates.
 */

class ZtWxApiAdapter {
    // 根据不同的发布环境，选择不同的url
    //for dev test
    const WX_API_HOME_URL = 'http://core.wx.dev.zhengtuo.net/wxApi/admin/';

    // for demo
//    const WX_API_HOME_URL = 'http://core.wx.demo.zhengtuo.net/wxApi/admin/';

    // for release
//    const WX_API_HOME_URL = 'http://core.wx.51fc.com.cn/wxApi/admin/';

    const WX_API_SUCCESS_CODE = 0;

    private static function callRemoteFunc($url, $data,
                                           $method = 'GET') {

        //添加ApiClientToken
        $data['APIClientToken'] = '51fc_WxAdmin';

        $callResult = CommonFunction::callHttp($url,$data,$method);

        try {
            $json = FastJSON::decode($callResult);
            if(!isset($json['responseCode'])) {
                //说明返回的并不是有效的响应数据：
                throw new Exception('获取的结果不合法：' . $callResult);
            }

            if($json['responseCode'] !== self::WX_API_SUCCESS_CODE ){
                throw new WxAppException($json['responseDesc']);
            }

            return $json;
        } catch(WxAppException $ex) {
            throw $ex;
        } catch(Exception $ex) {
            Yii::log('调用远程WxApi['. $url .']时出错：' . $ex->getMessage(), 'error');
            return null;
        }

    }

    public static function registerGH($ghId, $ghName, $appId, $appSecret, $ghDesc = '') {
        $url  = self::WX_API_HOME_URL . 'registerGh';
        $data = array(
            'ghId' => $ghId,
            'ghName' => $ghName,
            'ghDesc' => $ghDesc,
            'appId' => $appId,
            'appSecret' => $appSecret,
        );

        $method = 'POST';

        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            if(!isset($result['data']))
                throw new Exception('未返回data');

            $resultObj = $result['data'][0];

            return array(
                'ghApiId' => $resultObj['id'],
                'ghUrl' => $resultObj['url'],
                'ghToken' => $resultObj['token'],
            );
        } else {
            throw new WxAppException('无法注册公众号，请联系管理员');
        }
    }

    public static function addTempQrScene($ghApiId, $expireTime, $callBackUrl, $params, $desc)
    {

        $urlAddQr  = self::WX_API_HOME_URL . 'addTempQrScene';
        $urlGetQrUrl = self::WX_API_HOME_URL . 'getQrImageUrl';

        $data = array(
            'ghId' => $ghApiId,
            'handler' => $callBackUrl,
            'params' => FastJSON::encode($params),
            'expires' => $expireTime,
            'desc' => $desc,
        );

        $method = 'POST';

        $result = self::callRemoteFunc($urlAddQr, $data, $method);

        if(isset($result)) {
            if(!isset($result['data']))
                throw new Exception(__METHOD__ . '未返回数据， 参数: '. FastJSON::encode($data));

            $resultObj = $result['data'][0];
            $sceneId = $resultObj['sceneId'];
            $expireAt = $resultObj['expireAt'];

            $data = array(
                'ghId' => $ghApiId,
                'sceneId' => $sceneId,
            );
            $method = 'GET';

            $result = self::callRemoteFunc($urlGetQrUrl, $data, $method);

            if(!isset($result) || !isset($result['data'])) {
                throw new Exception(__METHOD__ . '未返回数据， 参数: '. FastJSON::encode($data));
            }

            $qrUrl = $result['data'][0];

            return array(
                'url' => $qrUrl,
                'sceneId' => $sceneId,
                'expireAt' => $expireAt,
            );
        } else {
            throw new WxAppException('无法获取临时二维码，请联系管理员');
        }
    }

    /**
     * 获取微信用户信息
     *
     * @param $ghApiId
     * @param $userOpenId
     * @return mixed
     * @throws WxAppException
     * @throws Exception
     */
    public static function getUserInfo($ghApiId, $userOpenId)
    {
        $url  = self::WX_API_HOME_URL . 'getWxUserInfo';
        $data = array(
            'id' => $ghApiId,
            'openId' => $userOpenId,
        );

        $method = 'GET';

        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            if(!isset($result['data']))
                throw new Exception('未返回data');

            $resultObj = $result['data'][0];

            return $resultObj;
        } else {
            throw new WxAppException('无法获取用户信息，请联系管理员');
        }

    }

    /**
     * 添加永久二维码场景
     * @param $ghApiId
     * @param $sceneId
     * @param $callBackUrl
     * @param $params
     * @param $desc
     * @return bool
     * @throws WxAppException
     */
    public static function addLimitQrScene($ghApiId, $sceneId, $callBackUrl, $params, $desc)
    {
        $urlAddQr  = self::WX_API_HOME_URL . 'addLimitQrScene';

        $data = array(
            'ghId' => $ghApiId,
            'handler' => $callBackUrl,
            'sceneId' => $sceneId,
            'params' => FastJSON::encode($params),
            'desc' => $desc,
        );

        $method = 'POST';

        $result = self::callRemoteFunc($urlAddQr, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法获取二维码，请联系管理员');
        }

    }

    /**
     * 获取指定场景的二维码url
     * @param $ghApiId
     * @param $sceneId
     * @return mixed
     * @throws Exception
     */
    public static function getQrImageUrl($ghApiId, $sceneId)
    {
        $urlGetQrUrl = self::WX_API_HOME_URL . 'getQrImageUrl';
        $data = array(
            'ghId' => $ghApiId,
            'sceneId' => $sceneId,
        );
        $method = 'GET';

        $result = self::callRemoteFunc($urlGetQrUrl, $data, $method);

        if(!isset($result) || !isset($result['data'])) {
            throw new Exception(__METHOD__ . '未返回数据， 参数: '. FastJSON::encode($data));
        }

        $qrUrl = $result['data'][0];

        return $qrUrl;

    }

    /**
     * 发送客服消息（文本）
     *
     * @param $ghApiId
     * @param $openId
     * @param $message
     * @return bool
     * @throws WxAppException
     */
    public static function sendCustomTextMessage($ghApiId, $openId, $message)
    {
        $urlAddQr  = self::WX_API_HOME_URL . 'sendCustomTextMessage';

        $data = array(
            'ghId' => $ghApiId,
            'toUser' => $openId,
            'content' => $message,
        );

        $method = 'POST';

        $result = self::callRemoteFunc($urlAddQr, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法推送客服消息，请稍后重试');
        }
    }

    public static function addMainMenu($ghApiId, $menuProperties)
    {
        $url = self::WX_API_HOME_URL . 'addMainMenu';
        $data = $menuProperties;

        $data['ghId'] = $ghApiId;
        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法添加主菜单，请稍后重试');
        }

    }

    public static function addSubMenu($ghApiId, $menuProperties)
    {
        $url = self::WX_API_HOME_URL . 'addSubMenu';
        $data = $menuProperties;

        $data['ghId'] = $ghApiId;
        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法添加子菜单，请稍后重试');
        }
    }

    public static function refreshMenu($ghApiId)
    {
        $url = self::WX_API_HOME_URL . 'refreshMenu';
        $data = array();

        $data['ghId'] = $ghApiId;
        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return $result['responseDesc'];
        } else {
            throw new WxAppException('无法刷新微信菜单，请稍后重试');
        }

    }

    public static function removeAllMenus($ghApiId)
    {
        $url = self::WX_API_HOME_URL . 'removeAllMenus';
        $data = array();

        $data['ghId'] = $ghApiId;
        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法删除微信菜单，请稍后重试');
        }

    }

    /**
     * @param $ghApiId
     * @param $openId
     * @param $talkFor
     * @param $answerHandler
     * @param $userLeftHandler
     * @param $userLeavingHandler
     * @param $expiredHandler
     * @param $expireMinutes
     * @param string $desc
     * @return bool
     * @throws WxAppException
     */
    public static function openConversation($ghApiId, $openId, $talkFor,
                                            $answerHandler, $userLeftHandler, $userLeavingHandler = '', $expiredHandler = '',
                                            $expireMinutes = 600, $desc = '')
    {
        $url = self::WX_API_HOME_URL . 'openConversation';
        $data = array();

        $data['ghId'] = $ghApiId;
        $data['openId'] = $openId;
        $data['talkFor'] = $talkFor;
        $data['answerHandler'] = $answerHandler;
        $data['userLeftHandler'] = $userLeftHandler;
        $data['userLeavingHandler'] = $userLeavingHandler;
        $data['expiredHandler'] = $expiredHandler;
        $data['expireMinutes'] = $expireMinutes;
        $data['desc'] = $desc;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }

    }

    public static function closeConversation($openId) {
        $url = self::WX_API_HOME_URL . 'closeConversation';
        $data = array();

        $data['openId'] = $openId;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }

    }

    public static function getFileUrl($fileId, $ghApiId)
    {
        $url = self::WX_API_HOME_URL . 'getFileUrl';
        $data = array();

        $data['fileId'] = $fileId;
        $data['ghId'] = $ghApiId;

        $method = 'GET';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return $result['data'][0];
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }
    }

    public static function getJsApiSignPackage($ghApiId, $targetUrl)
    {
        $url = self::WX_API_HOME_URL . 'getJsApiSignPackage';
        $data = array();

        $data['url'] = $targetUrl;
        $data['ghId'] = $ghApiId;

        $method = 'GET';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return $result['data'][0];
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }
    }


}