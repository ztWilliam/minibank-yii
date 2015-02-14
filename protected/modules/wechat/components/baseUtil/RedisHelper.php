<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lww
 * Date: 13-9-20
 * Time: 下午3:32
 * To change this template use File | Settings | File Templates.
 */
class RedisHelper
{
    private $serverIp;
    private $serverPort;

    public function  __construct($serverIp = '', $serverPort = ''){
        if(empty($serverIp)) {
            $this->serverIp = '127.0.0.1';
        } else {
            $this->serverIp = $serverIp;
        }

        if(empty($serverPort)) {
            $this->serverPort = '6379';
        } else {
            $this->serverPort = $serverPort;
        }
    }

    public  function hashAdd($hashKey, $field, $value){
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $redis->hSet($hashKey, $field, $value);
        $redis->close();
    }

    public function hashRemove($hashKey, $field) {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $redis->hDel($hashKey, $field);
        $redis->close();
    }

    public function hashGet($hashKey, $field) {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $value = $redis->hGet($hashKey, $field);
        $redis->close();

        return $value;
    }

    public function hashGetAll($hashKey) {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $keyValues = $redis->hGetAll($hashKey);
        $redis->close();

        return $keyValues;
    }

    private function hashMSet($hashKey, $fieldValues){
        $redis = $this->connect();

        $result = $redis->hMSet($hashKey, $fieldValues);
        $redis->close();

        return $result;
    }

    private function connect()
    {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);
        return $redis;
    }

    public function objectAdd($key, $object)
    {
        if (!isset($object))
            return false;

        $fieldValues = (array)$object;

        return $this->hashMSet($key, $fieldValues);
    }

    public function objectRemove($key)
    {
        $redis = $this->connect();

        $result = $redis->del($key);
        $redis->close();

        return $result;
    }

    /**
     * @param $key
     * @return array 原对象的 属性=>值 的集合
     */
    public function objectGet($key) {
        $result = $this->hashGetAll($key);
        return $result;
    }

    public function keys($keyword) {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $result = $redis->keys($keyword);
        $redis->close();

        return $result;

    }

    public function keyExpire($key, $seconds) {
        $redis = new Redis();
        $redis->connect($this->serverIp, $this->serverPort);

        $result = $redis->expire($key, $seconds);

        if($result != 1) {
            //执行失败，记录warning类型的日志：
            Yii::log('设置Redis Key 的超时时间失败：key:' . $key . ', expires in ' . $seconds . ' seconds.', 'warning');
        }

        $redis->close();

        return $result;
    }

}
