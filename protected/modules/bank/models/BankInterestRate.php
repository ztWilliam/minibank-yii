<?php
/**
 * 
 * User: william
 * Date: 16/1/27
 * Time: 上午11:20
 */

/**
 * This is the model class for table "bank_interest_rate".
 *
 * The followings are the available columns in table 'bank_interest_rate':
 * @property string $bankId
 * @property integer $accountType
 * @property float $interestRate
 * @property string $beginDate
 * @property string $endDate
 */
class BankInterestRate  extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * @return BankInterestRate the static model class
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
        return 'bank_interest_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bankId, accountType, beginDate', 'required'),
            array('accountType', 'numerical', 'integerOnly'=>true),
            array('interestRate', 'numerical', 'integerOnly'=>false),
            array('bankId', 'length', 'max'=>64),
            array('beginDate, endDate', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('bankId, accountType', 'safe', 'on'=>'search'),
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
            'accountType' => 'Account Type',
            'interestRate' => 'Interest Rate',
            'beginDate' => 'Begin Date',
            'endDate' => 'End Date',
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
        $criteria->compare('accountType',$this->accountType,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function hasActiveRate($bankId)
    {
        $obj = self::model()->findByAttributes(array('bankId' => $bankId, 'endDate' => CommonDefinition::FIELD_NOT_DEFINED));

        return isset($obj);
    }

}