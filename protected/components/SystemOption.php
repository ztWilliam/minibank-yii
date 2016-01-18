<?php
/**
 * 
 * User: william
 * Date: 16/1/17
 * Time: 上午12:08
 */

class SystemOption {

    public static function getWeChatApiId(){
        //todo 暂时从配置文件中获取，以后有了options表之后，从表中取
        return Yii::app()->params['ghDefinition']['ghApiId'];
    }
}