<?php
/**
 * 
 * User: william
 * Date: 15-6-22
 * Time: 下午12:19
 */

class ZtWxApiHelper extends CApplicationComponent {

    public $hostUrl;

    public function callRemoteFunc($url, $data,
                                   $method = 'GET') {

        $callResult = CommonFunction::callHttp($url,$data,$method);

        return $callResult;

    }

}