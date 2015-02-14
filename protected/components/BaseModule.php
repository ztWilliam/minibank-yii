<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-10-23
 * Time: 下午4:57
 * To change this template use File | Settings | File Templates.
 */

class BaseModule  extends CWebModule
{
    protected $_assetsUrl;

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'application.models.*',
            'application.components.*',
        ));
    }

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null) {
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('wap.assets'));
            return $this->_assetsUrl;
        }
        else
            return $this->_assetsUrl;
    }

    /**
     * @param string $value the base URL that contains all published asset files of gii.
     */
    public function setAssetsUrl($value)
    {
        $this->_assetsUrl = $value;
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here


            return true;
        }
        else{
            return false;
        }
    }
}