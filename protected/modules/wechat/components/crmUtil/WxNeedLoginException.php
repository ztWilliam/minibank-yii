<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-11-5
 * Time: 下午1:10
 * To change this template use File | Settings | File Templates.
 */

class WxNeedLoginException extends WxAppException{

    public function getErrorTip(){
        return "超时或在其他地点登录，需重新登录";
    }
}