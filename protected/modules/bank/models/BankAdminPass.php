<?php
/**
 * 
 * User: william
 * Date: 16/1/18
 * Time: 下午10:44
 */

/**
 * This is the model class for table "bank_admin_pass".
 *
 * The followings are the available columns in table 'bank_admin_pass':
 * @property string $bankId
 * @property string $userId
 * @property string $password
 * @property integer $isPrimary
 * @property string $createdTime
 */
class BankAdminPass extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * @return BankAdminPass the static model class
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
        return 'bank_admin_pass';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bankId, userId, password, createdTime', 'required'),
            array('isPrimary', 'numerical', 'integerOnly'=>true),
            array('bankId, userId, password', 'length', 'max'=>64),
            array('createdTime', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('bankId, userId', 'safe', 'on'=>'search'),
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
            'userId' => 'User ID',
            'password' => 'Password',
            'createdTime' => 'Created Time',
            'isPrimary' => 'Primary',
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

        $criteria->compare('bankId',$this->bankId);
        $criteria->compare('userId',$this->createdTime,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    public static function hasPassword($bankId, $userId)
    {
        if(empty($bankId) || empty($userId)){
            return false;
        }

        $obj = self::model()->findByAttributes(array('bankId' => $bankId, 'userId' => $userId));
        return isset($obj);
    }


}