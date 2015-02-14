<?php
/**
 * 提供本微信应用的所有菜单项设置
 *
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 14-10-9
 * Time: 下午5:53
 * To change this template use File | Settings | File Templates.
 */

class WechatMenu {

    private static function menuItems(){
        /**
         * 关于菜单的顺序：以数组中先后顺序，
         *  主菜单自左至右
         *  子菜单自下而上
         */
        return array(
            "1" => array(
                "menuName" => "尾单",
                "children" => array(
                    "1" => array(
                        "menuName" => "正在热抢",
                        "menuKey" => "mk_tail_hot",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/hotTailsMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "近期尾单",
                        "menuKey" => "mk_tail_coming",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/comingTailsMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
            "2" => array(
                "menuName" => "我的",
                "children" => array(
                    "1" => array(
                        "menuName" => "跟踪的尾单",
                        "menuKey" => "mk_tail_tracing",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/tracingTailsMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "抢到的尾单",
                        "menuKey" => "mk_tail_mine",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/myTailsMenuClicked"),
                        "url" => "",
                    ),
                    "3" => array(
                        "menuName" => "我的级别",
                        "menuKey" => "mk_tail_account",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/myAccountMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
        );
    }

    private static function create($ghApiId){
        //获取菜单项设置：
        $menuItems = self::menuItems();

        // 注册菜单：
        self::registerMenus($ghApiId, $menuItems);

        // 将菜单更新到微信上：
        ZtWxApiAdapter::refreshMenu($ghApiId);

    }

    public static function refresh($ghApiId){
        // 先删除现有微信菜单
        ZtWxApiAdapter::removeAllMenus($ghApiId);

        // 构建微信菜单
        self::create($ghApiId);

    }

    private static function registerMenus($ghApiId, $menuItems)
    {
        $afterMainMenu = "";
        foreach($menuItems as $mainMenu) {
            self::registerMainMenu($ghApiId, $mainMenu, $afterMainMenu);

            $afterSubMenu = "";
            $parentMenuName = $mainMenu['menuName'];
            foreach($mainMenu['children'] as $subMenu){
                self::registerSubMenu($ghApiId, $parentMenuName, $subMenu, $afterSubMenu);

                $afterSubMenu = $subMenu['menuName'];
            }

            $afterMainMenu = $mainMenu['menuName'];
        }
    }

    private static function registerMainMenu($ghApiId, $mainMenu, $afterMainMenu)
    {
        $params = $mainMenu;
        $params['afterMenu'] = $afterMainMenu;

        ZtWxApiAdapter::addMainMenu($ghApiId, $params);
    }

    private static function registerSubMenu($ghApiId, $parentMenuName, $subMenu, $afterSubMenu)
    {
        $params = $subMenu;
        $params['parentName'] = $parentMenuName;
        $params['afterMenu'] = $afterSubMenu;

        ZtWxApiAdapter::addSubMenu($ghApiId, $params);
    }

}