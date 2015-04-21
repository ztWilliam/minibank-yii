<?php

/**
 * This is the model class for table "bank".
 *
 * The followings are the available columns in table 'bank':
 * @property string $bankId
 * @property string $bankName
 * @property string $createUserId
 * @property string $createTime
 * @property string $delTag
 */
class Bank extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @return Bank the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'bank';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bankId, bankName, createUserId, createTime, delTag', 'required'),
            array('delTag', 'numerical', 'integerOnly'=>true),
            array('bankId, createUserId', 'length', 'max'=>64),
            array('createTime', 'length', 'max'=>20),
            array('bankName', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('bankName, createTime', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'bankId' => 'Bank ID',
            'bankName' => 'Bank Name',
            'createUserId' => 'Create User',
            'createTime' => 'Create Time',
            'delTag' => 'Deleted Tag',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('bankName',$this->bankName);
        $criteria->compare('createTime',$this->createTime,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function createBank($userId, $bankName, $pTrans = null)
    {
        //create a new Bank object, and save it to DB.
        // check the bankName length
        $bankName = trim($bankName);
        if($bankName == ''){
            throw new WxAppException(BankMessages::ERR_BANK_NAME_REQUIRED);
        }
        if(mb_strlen($bankName) > 50) {
            throw new WxAppException(BankMessages::ERR_BANK_NAME_TOO_LONG);
        }

        //check whether the userId has been used as a creator.(One user shall create only one bank.)
        $activeBank = self::model()->findByAttributes(array('createUserId' => $userId, 'delTag' => 0));
        if(isset($activeBank)){
            throw new WxAppException(BankMessages::ERR_USER_HAS_ONE_ACTIVE_BANK);
        }

        //create a new Bank object
        $newBank = new Bank();
        $newBank->createTime = date('Y-m-d H:i:s');
        $newBank->bankId = CommonFunction::create_guid_timestamp($newBank->createTime);
        $newBank->bankName = $bankName;
        $newBank->createUserId = $userId;
        $newBank->delTag = 0;

        if(!$newBank->save()){
            LogWriter::logModelSaveError($newBank, __METHOD__, array(
                'createUserId' => $userId,
                'bankName' => $bankName,
                'bankId' => $newBank->bankId,
            ));

            if($pTrans !== null){
                //If there is transaction in caller, the exception shall be thrown to caller.
                throw new Exception(sprintf(BankMessages::ERR_MODEL_SAVING_FAILED, 'Bank'));
            }
        }

        return $newBank;
    }

}