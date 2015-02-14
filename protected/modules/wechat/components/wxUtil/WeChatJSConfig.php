<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-2-1
 * Time: 下午4:53
 * To change this template use File | Settings | File Templates.
 */

class WeChatJSConfig {

    const DEFAULT_JS_API_DEBUG = 'false';
    const DEFAULT_OPTIONS_VISIBLE = false;

    public static function getSignPackage($ghApiId, $pageUrl)
    {
        $signPackage = ZtWxApiAdapter::getJsApiSignPackage($ghApiId, $pageUrl);

        return $signPackage;
    }
}