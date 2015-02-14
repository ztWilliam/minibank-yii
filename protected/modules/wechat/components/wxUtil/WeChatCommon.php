<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-8-29
 * Time: 下午3:33
 * To change this template use File | Settings | File Templates.
 */

class WeChatCommon {
    const COMMON_TPL = "<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                        %s
                       </xml>";

    const  TEXT_CONTENT_TPL = "<MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>";

    const NEWS_LIST_TPL = "<MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>%s</Articles>";

    const NEWS_LIST_ITEM_TPL = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";

    const NEWS_LIST_MAX_ITEM_COUNT = 10;

    const IMAGE_CONTENT_TPL = "<MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>";

    const VOICE_CONTENT_TPL = "<MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>";

    const VIDEO_CONTENT_TPL = "<MsgType><![CDATA[video]]></MsgType>
                    <Video>
                    <MediaId><![CDATA[%s]]></MediaId>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    </Video> ";

    const MUSIC_CONTENT_TPL = "<MsgType><![CDATA[music]]></MsgType>
                    <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                    </Music>";

    //如果微信消息是多媒体文件，从parameters中，可以根据此key，获取云端的文件的ID，
    //以后可以利用该ID，获取文件的url
    const FILE_GUID_INCLOUD_KEY = 'FileGUID';

    public static function textMessage($toUser, $fromUser, $createTime, $msgContent) {
        $contentText = sprintf(self::TEXT_CONTENT_TPL, $msgContent);
        $commonText = self::commonMessage($toUser, $fromUser, $createTime, $contentText);
        return $commonText;
    }

    public static function articlesMessage($toUser, $fromUser, $createTime, $articles) {
        if(!is_array($articles)) {
            throw new Exception('文章列表类型非法');
        }
        $articleCount = count($articles);
        if($articleCount == 0) {
            throw new Exception('没有待发送的图文');
        }
        if($articleCount > self::NEWS_LIST_MAX_ITEM_COUNT) {
            //微信对于超过10条的图文信息，会不响应：
            throw new Exception('图文信息不能超过'. self::NEWS_LIST_MAX_ITEM_COUNT .'条');
        }

        $items = "";
        foreach($articles as $article) {
            $itemStr = sprintf(self::NEWS_LIST_ITEM_TPL,
                $article['title'], $article['description'], $article['picurl'], $article['url']);
            $items .= $itemStr;
        }

        $newsXml = sprintf(self::NEWS_LIST_TPL, $articleCount, $items);

        $message = self::commonMessage($toUser, $fromUser, $createTime, $newsXml);

        return $message;
    }

    public static function makeArticleItem($title, $picUrl, $url, $description = ''){

        return array(
            'title' => $title,
            'description' => $description,
            'picurl' => $picUrl,
            'url' => $url,
        );
    }


    private static function commonMessage($toUser, $fromUser, $createTime, $content)
    {
        return sprintf(self::COMMON_TPL, $toUser, $fromUser, $createTime, $content);
    }

}