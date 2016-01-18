<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lww
 * Date: 14-9-23
 * Time: 下午1:43
 * To change this template use File | Settings | File Templates.
 */
class LogWriter
{
    public static function logModelSaveError($model, $class_method = '', $params = array())
    {
        $errStr = '';
        if($model instanceof CActiveRecord)
        {
            foreach($model->getErrors() as $error)
            {
                $errStr .= $error[0];
            }

            $logStr = $model->tableName() . "保存失败: " . $errStr . ".";

            if(!empty($class_method))
                $logStr .= "发生于：" . $class_method . ".";

            if(isset($params) )
            {
                foreach($params as $paramKey => $paramValue)
                {
                    $logStr .= "参数[". $paramKey ."]的值为[". $paramValue ."]；";
                }

            }

            Yii::log($logStr, 'error');
        }

    }

    const DEBUG_MODE = true;

    public static function trace($logContent){
        if(self::DEBUG_MODE){
            Yii::log('[WxApp Tracing]' . $logContent, 'trace');
        }
    }

    public static function warning($logContent) {
        Yii::log('[WxApp Warning]' . $logContent, 'warning');
    }

    public static function error($logContent) {
        Yii::log('[WxApp Error]' . $logContent, 'error');
    }


}
