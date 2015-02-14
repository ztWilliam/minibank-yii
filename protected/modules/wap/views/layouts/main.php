<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.1.0/css/amazeui.min.css">

    <title><?php echo $this->pageTitle ?></title>

    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">

    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp">


</head>

<body>
<?php
    //如果有错误消息，则显示alert，模版如下：
        //<div class="am-alert am-alert-danger" data-am-alert>
        //<button type="button" class="am-close">&times;</button>
        //［这是显示错误消息的地方］
        //</div>
    $alertTemplate = '<div class="am-alert am-alert-danger" data-am-alert>';
    $alertTemplate .= '<button type="button" class="am-close">&times;</button>';
    $alertTemplate .= '%s';
    $alertTemplate .= '</div>';
    WebDialog::showMobileAlertMessage($alertTemplate);
?>

<?php echo $content;
?>

<script src="http://cdn.amazeui.org/amazeui/2.1.0/js/amazeui.min.js"></script>

</body>
</html>
