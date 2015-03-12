<?php
/**
 * Created by JetBrains PhpStorm.
 * User: william
 * Date: 15-3-6
 * Time: 下午2:04
 * To change this template use File | Settings | File Templates.
 */

class m150306_140358_init_db extends CDbMigration {
    public function up() {
    }

    public function down() {
        echo "m150306_140358_init_db does not support migration down.\n";
        return false;
    }


    // implement safeUp/safeDown instead if transaction is needed

    public function safeUp() {
        echo "";

        // sql script of tables creation
        $sql = "
            --
            -- 表的结构 `bank`
            --

            CREATE TABLE IF NOT EXISTS `bank` (
              `bankId` varchar(64) NOT NULL,
              `bankName` varchar(50) NOT NULL,
              `createUserId` varchar(64) NOT NULL COMMENT '创建者的id'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            --
            -- Indexes for table `bank`
            --
            ALTER TABLE `bank`
             ADD PRIMARY KEY (`bankId`);

        ";

        $this->execute($sql);

    }

    public function safeDown() {
        echo "m150306_140358_init_db does not support migration down.\n";
        return false;
    }

}