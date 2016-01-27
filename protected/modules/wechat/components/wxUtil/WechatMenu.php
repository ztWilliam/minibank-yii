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
                "menuName" => "Mini",
                "children" => array(
                    "1" => array(
                        "menuName" => "活动预告",
                        "menuKey" => "mk_mini_activity",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/miniActivityMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "口算天天练",
                        "menuKey" => "mk_mini_calculation",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/miniCalculationMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
            "2" => array(
                "menuName" => "Bank",
                "children" => array(
                    "1" => array(
                        "menuName" => "银行管理",
                        "menuKey" => "mk_bank_admin",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/bankAdminMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "存款",
                        "menuKey" => "mk_bank_withdraw",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/bankWithdrawMenuClicked"),
                        "url" => "",
                    ),
                    "3" => array(
                        "menuName" => "取款",
                        "menuKey" => "mk_bank_deposit",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/bankDepositMenuClicked"),
                        "url" => "",
                    ),
                    "4" => array(
                        "menuName" => "余额查询",
                        "menuKey" => "mk_bank_balance",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/eventApi/bankBalanceMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
        );
    }

    private static function create($ghApiId, $newMenu = null){
        //获取菜单项设置：
        if(!isset($newMenu)) {
            $menuItems = self::menuItems();
        } else {
            $menuItems = $newMenu;
        }

        // 注册菜单：
        self::registerMenus($ghApiId, $menuItems);

        // 将菜单更新到微信上：
        ZtWxApiAdapter::refreshMenu($ghApiId);

    }

    public static function refresh($ghApiId, $newMenu = null){
        // 先删除现有微信菜单
        ZtWxApiAdapter::removeAllMenus($ghApiId);

        // 构建微信菜单
        self::create($ghApiId, $newMenu);

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