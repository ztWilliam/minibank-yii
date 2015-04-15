<?php
/**
 * 
 * User: william
 * Date: 15-3-12
 * Time: 下午7:18
 */
Yii::import('application.modules.bank.models.*');
class BankTest extends CDbTestCase{
    public $fixtures = array(
        'banks'=>'Bank',
    );

    public function testCreateBank(){
        //Test creating new bank by a user who has deleted his bank before.
        //A new Bank object expected
        $oldBank = $this->banks['toBeCreated'];
        $userId = $oldBank->createUserId;
        $newBankName = $oldBank->bankName . '(new)';

        $newBank = Bank::createBank($userId, $newBankName);

        $this->assertInstanceOf('Bank', $newBank);
        $this->assertEquals($newBank->bankName, $newBankName);
        $this->assertFalse($newBank->bankId == $oldBank->bankId);

        //todo: Test creating new bank by a new user
        //A new Bank object expected


        //todo: Test creating new bank by a user who has a bank active.
        //WxAppException expected


    }
}