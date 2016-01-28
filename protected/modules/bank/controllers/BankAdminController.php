<?php
/**
 * 
 * User: william
 * Date: 16/1/18
 * Time: 上午11:42
 */

class BankAdminController  extends Controller{

    public function actionWizard(){
        LogWriter::warning('Welcome to bank creation wizard');
        LogWriter::warning('Hello wechat code : ' . $_GET['code']);

        echo 'Hi, this is a test page';
    }

}