<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-10-12
 * Time: 上午11:20
 * To change this template use File | Settings | File Templates.
 */

 class CommonFunction {
    /**
     * 创建32位全局唯一码
     * @return string
     */
    public static function create_guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        return $charid;
    }

     public static function create_guid_timestamp($time = '')
     {
         //如果所给时间为空，则用当前时间
         if($time == '') {
             $timestamp = date('YmdHis');
         } else {
             $timestamp = date('YmdHis', strtotime($time));
         }

         $charid = strtoupper(md5(uniqid(mt_rand(), true)));

         return $timestamp . $charid;
     }

    public static function shrinkStr($rawStr, $key){
        $base32 = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZaBcDeFgHiJkLmNoPqRsTuVwXyZ";

        // 利用md5算法方式生成hash值
        $hex = hash('md5', $rawStr . $key);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;

        $output = array();
        for ($i = 0; $i < $subHexLen; $i++) {
            // 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作
            $subHex = substr($hex, $i * 8, 8);
            $idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));
            // 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的72个字符
            $out = '';
            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000003D & $idx;
                $out .= $base32[$val];
                $idx = $idx >> 5;
            }
            $output[$i] = $out;
        }

        return $output[0];

    }

     /**
      * @param string $url
      * @param array $data
      * @param string $method
      * @param string $type
      * @return mixed
      */
     public static function callHttp($url, $data=array(), $method='GET',$type='http'){
         $curl = curl_init(); // 启动一个CURL会话
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
         curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
         curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
         curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

         if($method=='POST'){
             curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
             if (is_array($data) && count($data) > 0 && $type == 'http'){
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
             }
             else{
                 curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(json_encode($data))); // Post提交json格式的数据包
             }
         }
         else {
             //说明是get 方式，将参数自动添加到url中：
             if (is_array($data) && count($data) > 0){
                 $url = $url.'?';
                 foreach($data as $field=>$value) {
                     $url = $url . $field . '=' . $value. '&';
                 }

                 $url = trim($url, '&');
             }
         }

         curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
         curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
         curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
         $tmpInfo = curl_exec($curl); // 执行操作
         curl_close($curl); // 关闭CURL会话
         #print_r($tmpInfo);
         return $tmpInfo; // 返回数据
     }

 }