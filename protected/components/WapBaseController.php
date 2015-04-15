<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-10-11
 * Time: 上午11:03
 * To change this template use File | Settings | File Templates.
 */

class WapBaseController extends CController {
    protected function returnAjaxResult($result)
    {
        header("Content-Type: application/json; charset=utf-8", true);
        echo FastJSON::encode($result);
    }

    protected function registerCssFile($cssFileName)
    {
        $cs = Yii::app()->clientScript;
        $cs->coreScriptPosition = CClientScript::POS_HEAD;
        $cs->scriptMap = array();
        $baseUrl = $this->module->assetsUrl;

        $cs->registerCssFile($baseUrl . '/css/' . $cssFileName);
    }

    protected function registerJsFile($jsFileName){
        $cs = Yii::app()->clientScript;
        $cs->coreScriptPosition = CClientScript::POS_HEAD;
        $cs->scriptMap = array();
        $baseUrl = $this->module->assetsUrl;

        $cs->registerScriptFile($baseUrl . '/js/' . $jsFileName);

    }

    protected function currentPageUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        return $url;
    }

}