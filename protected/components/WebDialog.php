<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-11-6
 * Time: 下午3:08
 * To change this template use File | Settings | File Templates.
 */

class WebDialog {
    public static function addAlertMessage($title, $message){
        Yii::app()->user->setFlash(rand(1,99999), array('title' => $title, 'content' => $message));
    }

    public static function showAlertMessage($controller) {
        if ($flashes = Yii::app()->user->getFlashes()) {
            foreach ($flashes as $key => $message) {
                if ($key != 'counters') {
                    $controller->beginWidget('zii.widgets.jui.CJuiDialog', array(
                        'id' => $key,
                        'options' => array(
                            'show' => 'blind',
                            'hide' => 'explode',
                            'modal' => 'true',
                            'title' => $message['title'],
                            'autoOpen' => true,
                            'height' => 'auto',
                            'closeOnEscape' => false,
                            'buttons' => array('确定' => 'js:function(){$(this).dialog("close")}'),
                        ),
                    ));

                    //要输出的内容
                    printf('<span class="dialog">%s</span>', $message['content']);

                    $controller->endWidget('zii.widgets.jui.CJuiDialog');
                }
            }
        }
    }

    /**
     * 根据当前用户的flashes里的信息，在移动端显示提示框
     *
     * @param $alertTemplate    由前端传入的提示框显示模版 （应为Html标记），显示消息的位置以“%s”标识。
     */
    public static function showMobileAlertMessage($alertTemplate) {
        if ($flashes = Yii::app()->user->getFlashes()) {
            $alertMessage = "提示:";
            foreach ($flashes as $key => $message) {
                if ($key != 'counters') {
                    $alertMessage .= PHP_EOL . $message['content'];
                }
            }

            //输出提示框信息：
            printf($alertTemplate, $alertMessage);
        }
    }
}