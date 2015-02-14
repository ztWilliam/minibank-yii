<?php
/**
 * 用于在微信openId 和 系统应用之间，搭建一个流转的身份记录信息，
 * 以免将openId直接暴露在链接中。
 *
 * 实现原理：
 * 当用户通过微信发起请求（发消息、点菜单、扫描等事件），当需要返回给用户一些链接，
 * 以随机的token串来作为标识参数，而具体的请求信息相关参数，放在服务端缓存。
 *
 * 好处：
 * 1、用该token可以控制链接的失效时间；
 * 2、隐藏openId等敏感信息，避免被盗用；
 * 3、采用token，而不是cookie或session存储，可以避免微信异常退出时，所有微信浏览器相关的临时存储数据丢失的问题。
 *
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-1-25
 * Time: 下午9:40
 * To change this template use File | Settings | File Templates.
 */

class WxToken {
    //RedisHelper对象：
    private $cache;

    const TOKEN_KEY_PREFIX = 'wx_token_%s_%s';   //嵌入参数依次是 tokenType 、 token
    const TOKEN_DEFAULT_EXPIRED_SECONDS = 172800; //秒数：默认的token的过期时间，48小时

    public function  __construct($serverIp = '', $serverPort = ''){
        $this->cache = new RedisHelper($serverIp, $serverPort);
    }

    public function createToken( $userId, $options = array(), $expiredAt = 0, $tokenType = '') {
        $tokenObj = new WxTokenModel();

        $tokenObj->tokenType = $tokenType;
        $tokenObj->options = $options;
        $tokenObj->token = CommonFunction::create_guid();
        $tokenObj->createUserId = $userId;
        $tokenObj->createdTime = date('Y-m-d H:i:s');

        if(empty($expiredAt)) {
            $expiredAt = self::TOKEN_DEFAULT_EXPIRED_SECONDS;
        }
        $tokenObj->invalidTime = date('Y-m-d H:i:s', strtotime($tokenObj->createdTime . ' + ' . $expiredAt . ' second'));

        $tokenObj->lastVisitTime = '';
        $tokenObj->visitCount = 0;

        return $tokenObj;

    }

    public function saveTokenToCache($tokenType, $tokenObj) {
        if(!$tokenObj instanceof WxTokenModel) {
            throw new Exception(sprintf(WxExceptionMessage::PARAM_TYPE_ERROR, __METHOD__, 'WxTokenModel'));
        }

        $key = sprintf(self::TOKEN_KEY_PREFIX, $tokenType, $tokenObj->token);
        $tokenObj->tokenType = $tokenType;
        $result = $this->cache->objectAdd($key, $tokenObj->serialize());

        return $result;
    }

    public function getTokenInCache($tokenType, $token) {
        $key = sprintf(self::TOKEN_KEY_PREFIX, $tokenType, $token);
        $result = $this->cache->objectGet($key);

        if($result == false) {
            throw new WxTokenNotFoundException();
        }

        $tokenObj = new WxTokenModel();
        $tokenObj->tokenType = $tokenType['tokenType'];
        $tokenObj->createUserId = $result['createUserId'];
        $tokenObj->createdTime = $result['createdTime'];
        $tokenObj->visitCount = $result['visitCount'] + 1;
        $tokenObj->lastVisitTime = date('Y-m-d H:i:s');
        $tokenObj->options = FastJSON::decode($result['options']);
        $tokenObj->token = $result['token'];
        $tokenObj->invalidTime = $result['invalidTime'];

        //将更新了 lastVisitTime 和 visitCount 的token 对象重新存入缓存：
        $this->saveTokenToCache($tokenType, $tokenObj);

        return $tokenObj;
    }

    public function removeTokenInCache($tokenType, $token) {
        $key = sprintf(self::TOKEN_KEY_PREFIX, $tokenType, $token);
        $result = $this->cache->objectRemove($key);

        return $result;
    }

    public function replaceTokenInCache($oldTokenId, $newTokenObj, $tokenType = ''){

        if(!$newTokenObj instanceof WxTokenModel) {
            throw new Exception(sprintf(WxExceptionMessage::PARAM_TYPE_ERROR, __METHOD__, 'WxTokenModel'));
        }

        if($tokenType == '') {
            $tokenType = $newTokenObj->tokenType;
        }

        $this->saveTokenToCache($tokenType, $newTokenObj);

        $this->removeTokenInCache($tokenType, $oldTokenId);
    }

    public function cloneTokenWithNewId($oldTokenModel) {
        if($oldTokenModel instanceof WxTokenModel) {
            $tokenObj = new WxTokenModel();

            $tokenObj->tokenType = $oldTokenModel->tokenType;
            $tokenObj->options = $oldTokenModel->options;
            $tokenObj->token = CommonFunction::create_guid();
            $tokenObj->createUserId = $oldTokenModel->createUserId;
            $tokenObj->createdTime = $oldTokenModel->createdTime;

            $tokenObj->invalidTime = $oldTokenModel->invalidTime;

            $tokenObj->lastVisitTime = $oldTokenModel->lastVisitTime;
            $tokenObj->visitCount = $oldTokenModel->visitCount;
        } else {
            throw new Exception(sprintf(WxExceptionMessage::PARAM_TYPE_ERROR, __METHOD__, 'WxTokenModel'));
        }

        return $tokenObj;
    }
}

class WxTokenModel {
    var $tokenType;
    var $token;
    var $createUserId;
    var $createdTime;
    var $lastVisitTime;
    var $invalidTime;
    var $visitCount;
    var $options;

    public function serialize(){
        return array(
            'tokenType' => $this->tokenType,
            'token' => $this->token,
            'createUserId' => $this->createUserId,
            'createdTime' => $this->createdTime,
            'lastVisitTime' => $this->lastVisitTime,
            'invalidTime' => $this->invalidTime,
            'visitCount' => $this->visitCount,
            'options' => FastJSON::encode($this->options),
        );
    }

}

