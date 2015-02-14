<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-8-22
 * Time: 下午2:54
 * To change this template use File | Settings | File Templates.
 */

class MessageApiController extends Controller {
    /**
     * 对来自微信端的普通文本消息，进行响应：
     */
    public function actionText(){
        $msgObjPost = $_POST['msgObj'];
        if(!isset($msgObjPost)) {
            Yii::log('The text message body is nothing ' , 'warning');
            echo "nothing";
            exit;
        }
        Yii::log('The text message body is ' . $msgObjPost, 'warning');

        $msgObj = json_decode($msgObjPost, true);


//        echo "welcome" . $msgObj['Content'];
//        exit;


        $fromUsername = $msgObj['FromUserName'];
        $toUsername = $msgObj['ToUserName'];
        $keyword = trim($msgObj['Content']);
        $time = time();
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        if(!empty( $keyword ))
        {
            $msgType = "text";
            $contentStr = "欢迎关注我们! 高大上的功能正在开发测试中，请继续关注我们～～";

            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            header("Content-Type: application/json; charset=utf-8", true);
            echo $resultStr;
            exit;
        }else{
            echo "Input something...";
        }

    }

    public function actionReply(){
        $msgObj = WeChatRequest::extractMessageObj($_POST);

        $result = WeChatCommon::textMessage($msgObj['FromUserName'], $msgObj['ToUserName'], time(), '这是一条普通消息');

        echo $result;

    }

    public function actionReplyTalking(){
        $msgObj = WeChatRequest::extractMessageObj($_POST);

        $result = WeChatCommon::textMessage($msgObj['FromUserName'], $msgObj['ToUserName'], time(), '这是一条会话消息，回复 over 结束本次会话');

        echo $result;

    }

    public function actionUserLeft(){
        $msgObj = WeChatRequest::extractMessageObj($_POST);

        $result = WeChatCommon::textMessage($msgObj['FromUserName'], $msgObj['ToUserName'], time(), '您已离开本次会话，之前的会话内容已丢弃');

        echo $result;

    }

    public function actionUserLeaving(){
        $msgObj = WeChatRequest::extractMessageObj($_POST);

        $result = WeChatCommon::textMessage($msgObj['FromUserName'], $msgObj['ToUserName'], time(), '您即将离开本次会话，之前的会话内容将丢弃，回复 确定 离开本次会话，若不离开，请继续提交内容即可');

        echo $result;

    }

    public function actionUserExpired(){
        $msgObj = WeChatRequest::extractMessageObj($_POST);

        $result = WeChatCommon::textMessage($msgObj['FromUserName'], $msgObj['ToUserName'], time(), '本次会话超时，之前的会话内容已丢弃');

        echo $result;
    }

}
