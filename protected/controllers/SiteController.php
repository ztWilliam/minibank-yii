<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-2-8
 * Time: 下午6:34
 * To change this template use File | Settings | File Templates.
 */

class SiteController  extends Controller {
    public function filters()
    {
        return array(
            'accessControl', // 实现CRUD操作的访问控制。
        );
    }
    public function accessRules()  //这里就是访问规则的设置。
    {
        return array(
            array('allow',                     // 允许所有用户执行index,view动作。
                'actions'=>array('register','login','logout'),
                'users'=>array('*'),         //*号标识所有用户包括注册的、没注册的、一般的、管理员级的
            ),
            array('allow',                     // 允许所有用户执行index,view动作。
                'actions'=>array(),
                'users'=>array('@'),         //*号标识所有用户包括注册的、没注册的、一般的、管理员级的
            ),
            array('deny',  // 拒绝所有的访问。
                'users'=>array('*'),
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */

    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        if(Yii::app()->request->urlReferrer){
            $this->redirect(Yii::app()->request->urlReferrer);
        }
        else{
            #$this->render('index');
            $this->redirect(Yii::app()->request->baseUrl.'/index.php');
        }

    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }


}