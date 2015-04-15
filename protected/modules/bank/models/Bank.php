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

    public static function createBank($userId, $newBankName)
    {
        //todo: create a new Bank object, and save it to DB.
        return null;
    }

}