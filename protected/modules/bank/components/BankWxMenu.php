<?php
/**
 * 
 * User: william
 * Date: 16/1/18
 * Time: 上午11:30
 */

class BankWxMenu {
    public static function menuItems(){
        /**
         * 关于菜单的顺序：以数组中先后顺序，
         *  主菜单自左至右
         *  子菜单自下而上
         */
        return array(
            "1" => array(
                "menuName" => "Bank",
                "children" => array(
                    "1" => array(
                        "menuName" => "银行管理",
                        "menuKey" => "mk_bank_admin",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/bankAdminMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "存款",
                        "menuKey" => "mk_bank_withdraw",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/bankWithdrawMenuClicked"),
                        "url" => "",
                    ),
                    "3" => array(
                        "menuName" => "取款",
                        "menuKey" => "mk_bank_deposit",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/bankDepositMenuClicked"),
                        "url" => "",
                    ),
                    "4" => array(
                        "menuName" => "余额查询",
                        "menuKey" => "mk_bank_balance",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/bankBalanceMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
            "2" => array(
                "menuName" => "Kid",
                "children" => array(
                    "1" => array(
                        "menuName" => "活动预告",
                        "menuKey" => "mk_mini_activity",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/miniActivityMenuClicked"),
                        "url" => "",
                    ),
                    "2" => array(
                        "menuName" => "口算天天练",
                        "menuKey" => "mk_mini_calculation",
                        "eventType" => "click",
                        "handler" => Yii::app()->createAbsoluteUrl("/bank/wxEvent/miniCalculationMenuClicked"),
                        "url" => "",
                    ),
                ),
            ),
        );
    }

}