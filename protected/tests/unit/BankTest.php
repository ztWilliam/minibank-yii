<?php
/**
 * 
 * User: william
 * Date: 15-3-12
 * Time: 下午7:18
 */
Yii::import('application.modules.bank.models.*');
Yii::import('application.modules.bank.components.*');

Yii::import('application.tests.fixtures.TestIdGenerator');
class BankTest extends CDbTestCase{
    public $fixtures = array(
        'banks'=>'Bank',
    );

    public function testCreateBank(){
        //Test creating new bank by a user who has deleted his bank before.
        //A new Bank object expected
        $oldBank = $this->banks('toBeCreated');
        $userId = $oldBank->createUserId;
        $newBankName = $oldBank->bankName . '(new)';

        $newBank = Bank::createBank($userId, $newBankName);
        $this->assertInstanceOf('Bank', $newBank);
        $this->assertEquals($newBank->bankName, $newBankName);
        $this->assertFalse($newBank->bankId == $oldBank->bankId);

        // Test creating new bank by a new user
        //A new Bank object expected
        $userId = TestIdGenerator::getUserId('uId05');
        $newBankName = 'New 银行 Of New User';

        $newBank = Bank::createBank($userId, $newBankName);
        $this->assertInstanceOf('Bank', $newBank);
        $this->assertEquals($newBank->bankName, $newBankName);
        $this->assertEquals($userId, $newBank->createUserId );


    }

    public function testCreateBankByDuplicateUser(){
        // Test creating new bank by a user who has a bank active.
        //WxAppException expected
        $oldBank = $this->banks('static');
        $userId = $oldBank->createUserId;
        $newBankName = $oldBank->bankName . '(new)';

        $this->setExpectedException('WxAppException', BankMessages::ERR_USER_HAS_ONE_ACTIVE_BANK);
        Bank::createBank($userId, $newBankName);
    }

    public function testCreateBankWithLongName(){
        // Test creating new bank with a name longer than max length.
        //WxAppException expected
        $userId = TestIdGenerator::getUserId('uId06');
        $newBankName = 'TheLongestName(more than fifty 字数)Should not be Used';

        $this->setExpectedException('WxAppException', BankMessages::ERR_BANK_NAME_TOO_LONG);
        Bank::createBank($userId, $newBankName);
    }

    public function testCreateBankWithEmptyName(){
        // Test creating new bank with a name longer than max length.
        //WxAppException expected
        $userId = TestIdGenerator::getUserId('uId06');
        $newBankName = '  ';

        $this->setExpectedException('WxAppException', BankMessages::ERR_BANK_NAME_REQUIRED);
        Bank::createBank($userId, $newBankName);
    }
}