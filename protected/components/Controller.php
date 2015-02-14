<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();


    /*用户访问时的身份验证（全局）*/
    /*public function beforeAction($action)
    {
        #Yii::app()->name = Init::model()->getOption('siteName', 'CampaignPortal');
        #定义无需验证的action,module
        $extraActions = array('wechat','login','resetpage', 'resetpassword');

        if (!in_array(strtolower($action->id), $extraActions)
            && !in_array(strtolower($action->controller->module->id),$extraActions)
            && Yii::app()->user->isGuest)
        {
            Yii::app()->user->loginRequired();
            #print_r($action);
        }

        return true;
    }*/

    protected function checkAdminUser(){
        $user = Yii::app()->user->getUserModel();

        if(!UserManager::isAdminUser($user)) {
            throw new WxAppException("只有管理员可以进行此操作");
        }
    }

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

    protected function enableLog(){
        foreach (Yii::app()->log->getRoutes() as $route)
            $route->enabled = true;
    }

    protected function currentPageUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        return $url;
    }
}