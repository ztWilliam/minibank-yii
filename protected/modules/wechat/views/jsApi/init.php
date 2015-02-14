<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-2-1
 * Time: 下午5:12
 * To change this template use File | Settings | File Templates.
 */
?>
<?php
//获取js api 接入所需要的参数
$signPackage = WeChatJSConfig::getSignPackage($ghApiId, $url);
?>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: <?php echo FastJSON::encode($signPackage["jsApiList"]) ?>
    });
    wx.ready(function () {
        // 是否隐藏右上角菜单
        <?php
        if($options) {
            echo "wx.showOptionMenu();";
        } else {
            echo "wx.hideOptionMenu();";
        }
        ?>
    });
</script>
