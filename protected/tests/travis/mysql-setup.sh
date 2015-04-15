#!/bin/sh

mysql -u root -e 'CREATE SCHEMA `mini_bank_test` CHARACTER SET utf8 COLLATE utf8_general_ci; GRANT ALL ON `mini_bank_test`.* TO test@localhost IDENTIFIED BY "test"; FLUSH PRIVILEGES;'