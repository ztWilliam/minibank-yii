<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-3-6
 * Time: 下午2:04
 * To change this template use File | Settings | File Templates.
 */

class m150422_140405_bank_creation extends CDbMigration {
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
            --
            -- 表的结构 `user`
            --

            CREATE TABLE IF NOT EXISTS `user` (
              `userId` varchar(64) NOT NULL,
              `openId` varchar(64) NOT NULL,
              `generalId` varchar(64) NOT NULL DEFAULT 'none' COMMENT '通用号，若是授权公众号，则有通用码（可用于判定是否同一个微信）',
              `bindTime` datetime NOT NULL COMMENT '初次绑定的时间（与初次关注时间相同）',
              `isActive` smallint(6) NOT NULL DEFAULT '1' COMMENT '是否仍在关注',
              `delTag` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否被手动删除（用于封禁用户）'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='所有微信用户索引';

            --
            -- Indexes for table `user`
            --
            ALTER TABLE `user`
             ADD PRIMARY KEY (`userId`);

            -- --------------------------------------------------------

            --
            -- 表的结构 `user_subscription`
            --

            CREATE TABLE IF NOT EXISTS `user_subscription` (
              `userId` varchar(64) NOT NULL,
              `operationId` int(11) NOT NULL COMMENT '该用户的操作序号',
              `operationTime` datetime NOT NULL COMMENT '关注／取消关注的时间',
              `operationType` smallint(6) NOT NULL COMMENT '1:关注 ；0:取消关注'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信用户关注和取消关注的操作历史（用于用户行为分析）';

            --
            -- Indexes for table `user_subscription`
            --
            ALTER TABLE `user_subscription`
             ADD PRIMARY KEY (`userId`,`operationId`);

            -- --------------------------------------------------------

            --
            -- 表的结构 `bank_admin_pass`
            --

            CREATE TABLE IF NOT EXISTS `bank_admin_pass` (
              `bankId` varchar(64) NOT NULL,
              `userId` varchar(64) NOT NULL,
              `password` varchar(64) NOT NULL,
              `isPrimary` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否主管理员（功能最全）',
              `createdTime` datetime NOT NULL COMMENT '赋予管理权限的时间'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行管理员的表';

            --
            -- Indexes for table `bank_admin_pass`
            --
            ALTER TABLE `bank_admin_pass`
             ADD PRIMARY KEY (`bankId`,`userId`);

            -- --------------------------------------------------------

            --
            -- 表的结构 `bank_admin_operation_summary`
            --

            CREATE TABLE IF NOT EXISTS `bank_admin_operation_summary` (
              `bankId` varchar(64) NOT NULL,
              `userId` varchar(64) NOT NULL,
              `lastLoginTime` datetime NOT NULL,
              `lastOperationTime` datetime NOT NULL,
              `loginCount` int(11) NOT NULL,
              `operationCount` int(11) NOT NULL,
              `lastOperationType` varchar(100) NOT NULL COMMENT '最近一次操作的类型'
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='银行管理员的操作统计汇总';

            --
            -- Indexes for table `bank_admin_operation_summary`
            --
            ALTER TABLE `bank_admin_operation_summary`
             ADD PRIMARY KEY (`bankId`,`userId`);


        ";

        $this->execute($sql);

    }

    public function safeDown() {
        echo "m150422_140405_bank_creation does not support migration down.\n";
        return false;
    }

}