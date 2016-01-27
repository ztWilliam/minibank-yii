<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-9-22
 * Time: 上午11:19
 * To change this template use File | Settings | File Templates.
 */

class ZtWxApiAdapter {
    const WX_API_SUCCESS_CODE = 0;

    private static $remoteHelper;

    private static function initHelper(){
        if(!isset(self::$remoteHelper)){
            //如果远程调用者尚未被初始化，则创建之：
            self::$remoteHelper = Yii::app()->wxApiHelper;
        }
    }

    public static function attachHelper($helper){
        //接受外界给的helper对象
        if(! $helper instanceof ZtWxApiHelper) {
            throw new Exception(sprintf( WxExceptionMessage::PARAM_TYPE_ERROR, __METHOD__, 'ZtWxApiHelper'));
        }

        self::$remoteHelper = $helper;
    }

    public static function detachHelper(){
        //将helper解绑（以后再调用，则会使用默认的helper）
        self::$remoteHelper = null;
    }

    private static function callRemoteFunc($url, $data,
                                           $method = 'GET') {

        //添加ApiClientToken
        $data['APIClientToken'] = '51fc_WxAdmin';

//        $callResult = CommonFunction::callHttp($url,$data,$method);
        $callResult = self::$remoteHelper->callRemoteFunc($url, $data, $method);

        try {
            if(empty($callResult)) {
                //说明返回的并不是有效的响应数据：
                throw new Exception('获取的结果不合法：' . $callResult);
            }

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
            LogWriter::error('调用远程WxApi['. $url .']时出错：' . $ex->getMessage());
            return null;
        }

    }

    public static function registerGH($ghId, $ghName, $appId, $appSecret, $ghDesc = '') {
        self::initHelper();
        $url  = self::$remoteHelper->hostUrl . 'registerGh';
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
                'ghName' => $resultObj['ghName'],
                'ghId' => $resultObj['ghId'],
            );
        } else {
            throw new WxAppException('无法注册公众号，请联系管理员');
        }
    }

    public static function addTempQrScene($ghApiId, $expireTime, $callBackUrl, $params, $desc)
    {
        self::initHelper();

        $urlAddQr  = self::$remoteHelper->hostUrl . 'addTempQrScene';
        $urlGetQrUrl = self::$remoteHelper->hostUrl . 'getQrImageUrl';

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
        self::initHelper();

        $url  = self::$remoteHelper->hostUrl . 'getWxUserInfo';
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
        self::initHelper();

        $urlAddQr  = self::$remoteHelper->hostUrl . 'addLimitQrScene';

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
        self::initHelper();

        $urlGetQrUrl = self::$remoteHelper->hostUrl . 'getQrImageUrl';
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
        self::initHelper();

        $urlAddQr  = self::$remoteHelper->hostUrl . 'sendCustomTextMessage';

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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'addMainMenu';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'addSubMenu';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'refreshMenu';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'removeAllMenus';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'openConversation';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'closeConversation';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'getFileUrl';
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
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'getJsApiSignPackage';
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

    /**
     * 批量发送模版消息
     * @param $ghId
     * @param $templateId
     * @param $targetUrl
     * @param $topColor
     * @param $content
     * @param $toUsers  //is an array in which each element has at least 2 properties: openId (must) and params (optional)
     * @param int $oAuth
     * @return bool
     * @throws WxAppException
     */
    public static function batchSendTemplateMessage($ghId, $templateId, $targetUrl, $topColor, $content, $toUsers, $oAuth = 0)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'templateMessageBatchSend';
        $data = array();

        $data['ghId'] = $ghId;
        $data['templateId'] = $templateId;
        $data['url'] = $targetUrl;
        $data['topColor'] = $topColor;
        $data['data'] = FastJSON::encode($content);
        $data['toUsers'] = FastJSON::encode($toUsers);
        $data['oAuth'] = $oAuth;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }

    }

    /**
     * 发送单个模版消息
     *
     * @param $ghId
     * @param $templateId
     * @param $targetUrl
     * @param $topColor
     * @param $content
     * @param $toUser
     * @param int $oAuth
     * @return bool
     * @throws WxAppException
     */
    public static function sendTemplateMessage($ghId, $templateId, $targetUrl, $topColor, $content, $toUser, $oAuth = 0)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'templateMessageSend';
        $data = array();

        $data['ghId'] = $ghId;
        $data['templateId'] = $templateId;
        $data['url'] = $targetUrl;
        $data['topColor'] = $topColor;
        $data['data'] = FastJSON::encode($content);
        $data['toUser'] = $toUser;
        $data['oAuth'] = $oAuth;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('系统忙，请稍后重试');
        }

    }

    public static function setMessageHandler($ghApiId, $handlerUrl)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'setMessageHandler';

        $data['ghId'] = $ghApiId;
        $data['handler'] = $handlerUrl;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法注册 消息处理接口，请稍后重试');
        }
    }

    public static function setSubscribeHandler($ghApiId, $handlerUrl)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'setSubscribeHandler';

        $data['ghId'] = $ghApiId;
        $data['handler'] = $handlerUrl;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法注册 关注事件处理接口，请稍后重试');
        }
    }

    public static function setUnSubscribeHandler($ghApiId, $handlerUrl)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'setUnSubscribeHandler';

        $data['ghId'] = $ghApiId;
        $data['handler'] = $handlerUrl;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法注册 取消关注事件处理接口，请稍后重试');
        }
    }

    public static function setUrlVerifiedHandler($ghApiId, $handlerUrl)
    {
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'setUrlVerifiedHandler';

        $data['ghId'] = $ghApiId;
        $data['handler'] = $handlerUrl;

        $method = 'POST';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return true;
        } else {
            throw new WxAppException('无法注册 url验证通过事件处理接口，请稍后重试');
        }
    }

    /**
     * @param $ghApiId
     * @param $code
     * @return array 内部结构如下：
     *
     * {
     *   "access_token":"ACCESS_TOKEN",
     *   "expires_in":7200,
     *   "refresh_token":"REFRESH_TOKEN",
     *   "openid":"OPENID",
     *   "scope":"SCOPE",
     *   "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"  //仅当第三方平台设置过
     * }

     * @throws WxAppException
     */
    public static function getPageAuthAccessToken($ghApiId, $code){
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'getPageAuthAccessToken';

        $data['id'] = $ghApiId;
        $data['code'] = $code;

        $method = 'GET';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return $result['data'][0];
        } else {
            throw new WxAppException('无法获取微信页面验证信息，请稍后重试');
        }
    }

    /**
     * @param $ghApiId
     * @param $redirectUrl
     * @param string $scope  'basic' for basic url, 'userinfo' for userInfo url
     * @return mixed
     * @throws WxAppException
     */
    public static function getAuthUrl($ghApiId, $redirectUrl, $scope = 'basic'){
        self::initHelper();

        $url = self::$remoteHelper->hostUrl . 'getAuthUrl';

        $data['id'] = $ghApiId;
        $data['url'] = $redirectUrl;
        $data['scope'] = $scope;

        $method = 'GET';
        $result = self::callRemoteFunc($url, $data, $method);

        if(isset($result)) {
            return $result['data'][0];
        } else {
            throw new WxAppException('无法获取微信页面验证信息，请稍后重试');
        }

    }
}