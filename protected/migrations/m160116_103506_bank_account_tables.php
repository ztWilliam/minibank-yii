<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-3-6
 * Time: 下午2:04
 * To change this template use File | Settings | File Templates.
 */

class m160116_103506_bank_account_tables extends CDbMigration {
/*

    public function up() {
    }

    public function down() {
        echo "m150306_140358_init_db does not support migration down.\n";
        return false;
    }
*/

    // implement safeUp/safeDown instead if transaction is needed

    public function safeUp() {
        echo "";

        // sql script of tables creation
        $sql = "

            -- --------------------------------------------------------

            -- 去掉不需要的表：
            -- 不需要表： `bank_admin_operation_summary` 了。（管理员的操作日志并没什么用）
            -- 不需要表： `user_subscription` 了。（此类用户行为分析统一交给微信开发框架来做）
            --

            DROP TABLE IF EXISTS `bank_admin_operation_summary`;
            DROP TABLE IF EXISTS `user_subscription`;


            -- --------------------------------------------------------

            --
            -- `user` 表中增加昵称和头像url字段，便于显示
            --
            ALTER TABLE `user`
                ADD `nickName` VARCHAR(50) NOT NULL COMMENT '昵称，从微信用户信息获取' ,
                ADD `avatarUrl` VARCHAR(256) NOT NULL COMMENT '微信头像的url' ;

            -- --------------------------------------------------------

            --
            -- 表的结构 `account_info`
            --

            DROP TABLE IF EXISTS `account_info`;
            CREATE TABLE IF NOT EXISTS `account_info` (
              `accountId` varchar(64) NOT NULL COMMENT '唯一主键',
              `bankId` varchar(64) NOT NULL COMMENT '该账户所属的银行id',
              `accountName` varchar(20) NOT NULL COMMENT '户名',
              `password` varchar(64) NOT NULL COMMENT '加密存储的密码',
              `birthYear` varchar(4) NOT NULL COMMENT '出生年',
              `gender` tinyint(4) NOT NULL COMMENT '性别 （1:男；0:女；）',
              `createdTime` datetime NOT NULL COMMENT '账户创建时间',
              `createdUser` varchar(64) NOT NULL COMMENT '创建该账户的user',
              `currentBalance` decimal(16,6) NOT NULL DEFAULT '0.000000' COMMENT '活期余额',
              `depositBalance` decimal(16,6) NOT NULL DEFAULT '0.000000' COMMENT '定期余额'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行账户，每个账户对应一个小孩';


            -- --------------------------------------------------------

            --
            -- 表的结构 `account_sub`
            --

            DROP TABLE IF EXISTS `account_sub`;
            CREATE TABLE IF NOT EXISTS `account_sub` (
              `subAccountId` varchar(32) NOT NULL COMMENT '子账户的id，guid，不含时间戳',
              `mainAccountId` varchar(64) NOT NULL COMMENT '该子账户所属的主账户id',
              `bankId` varchar(64) NOT NULL COMMENT '所属bank的id',
              `isCurrentAccount` tinyint(4) NOT NULL COMMENT '0:表示定期账户；1:表示活期账户',
              `interestRate` decimal(7,6) NOT NULL COMMENT '月化利率。不能大于1',
              `createdTime` datetime NOT NULL COMMENT '账户创建时间',
              `depositPeriod` int(11) NOT NULL COMMENT '以月为单位的时间段',
              `fromDate` varchar(10) NOT NULL COMMENT '计息起始日期',
              `toDate` varchar(10) NOT NULL DEFAULT 'none' COMMENT '计息截至日期（活期为none）',
              `balance` decimal(16,6) NOT NULL COMMENT '当前账户余额'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='子账户表，每个账户下可有多个子账户。有且只能有一个活期；定期可有多个';

            -- --------------------------------------------------------

            --
            -- 表的结构 `account_transaction`
            --

            DROP TABLE IF EXISTS `account_transaction`;
            CREATE TABLE IF NOT EXISTS `account_transaction` (
              `transId` varchar(32) NOT NULL COMMENT 'GUID',
              `accountId` varchar(64) NOT NULL COMMENT '主accountId',
              `subAccountId` varchar(32) NOT NULL COMMENT 'sub account id',
              `transType` smallint(6) NOT NULL COMMENT '交易类型代码',
              `transTime` datetime NOT NULL COMMENT '发生交易的时间',
              `current` decimal(16,6) NOT NULL COMMENT '发生金额。存+取-',
              `balance` decimal(16,6) NOT NULL COMMENT '交易发生后余额',
              `description` varchar(140) NOT NULL COMMENT '该笔交易的文字描述',
              `operatorId` varchar(64) NOT NULL COMMENT '负责该笔交易的管理员userId'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账户交易记录';

            -- --------------------------------------------------------

            --
            -- 表的结构 `account_balance_daily`
            --

            DROP TABLE IF EXISTS `account_balance_daily`;
            CREATE TABLE IF NOT EXISTS `account_balance_daily` (
              `subAccountId` varchar(32) NOT NULL COMMENT '子账户的id',
              `balanceDate` varchar(10) NOT NULL COMMENT '余额计算日期（格式：yyyy-mm-dd）',
              `lastBalance` decimal(16,6) NOT NULL COMMENT '上日余额',
              `todayBalance` decimal(16,6) NOT NULL COMMENT '当日余额',
              `interestBalance` decimal(16,6) NOT NULL COMMENT '昨日的interestBalance + 当日账户余额。该数值在结息后清零。'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账户余额日计表（为了计算利息用）';

            --
            -- Indexes for table `account_balance_daily`
            --
            ALTER TABLE `account_balance_daily`
             ADD PRIMARY KEY (`subAccountId`,`balanceDate`);

            --
            -- Indexes for table `account_info`
            --
            ALTER TABLE `account_info`
             ADD PRIMARY KEY (`accountId`);

            --
            -- Indexes for table `account_sub`
            --
            ALTER TABLE `account_sub`
             ADD PRIMARY KEY (`subAccountId`);

            --
            -- Indexes for table `account_transaction`
            --
            ALTER TABLE `account_transaction`
             ADD PRIMARY KEY (`transId`);


            -- --------------------------------------------------------

            --
            -- 表的结构 `bank_interest_rate`
            --

            CREATE TABLE IF NOT EXISTS `bank_interest_rate` (
              `bankId` varchar(64) NOT NULL COMMENT '银行id',
              `accountType` smallint(6) NOT NULL COMMENT '账户类型（活期、定期（3月／6月等））',
              `interestRate` decimal(7,6) NOT NULL COMMENT '利息率（月化利率）',
              `beginDate` int(11) NOT NULL DEFAULT '19000000' COMMENT '利息开始实行日期（yyyymmdd格式的日期对应的数字）',
              `endDate` int(11) NOT NULL DEFAULT '30000000' COMMENT '利息停止使用日期（yyyymmdd格式的日期对应的数字）'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行利率定义表';

            -- --------------------------------------------------------

            --
            -- 表的结构 `account_auto_save`
            --

            CREATE TABLE IF NOT EXISTS `account_auto_save` (
              `subAccountId` varchar(32) NOT NULL COMMENT '子账户id',
              `bankId` varchar(64) NOT NULL COMMENT '银行的id',
              `accountId` varchar(64) NOT NULL COMMENT '主账户id',
              `saveMoney` decimal(16,6) NOT NULL COMMENT '自动存的金额',
              `repeatType` smallint(6) NOT NULL COMMENT '重复周期类型（1:周、2:月）',
              `repeatAt` smallint(6) NOT NULL COMMENT '周（1-7）；月（1－28）；',
              `status` tinyint(4) NOT NULL COMMENT '0:启用；1:禁用'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行账户自动存款选项';

            --
            -- Indexes for table `account_auto_save`
            --
            ALTER TABLE `account_auto_save`
             ADD PRIMARY KEY (`subAccountId`);

        ";

        $this->execute($sql);

    }

    public function safeDown() {
        echo "m160116_103506_bank_account_tables does not support migration down.\n";
        return false;
    }

}