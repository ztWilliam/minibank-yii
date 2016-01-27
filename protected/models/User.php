<?php
/**
 * 
 * User: william
 * Date: 16/1/16
 * Time: 下午10:26
 */

/**
 * 所有关注了公众号的用户
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $userId
 * @property string $nickName
 * @property string $openId
 * @property string $generalId
 * @property string $avatarUrl
 * @property string $bindTime
 * @property integer $isActive
 * @property integer $delTag
 *
 *
 */
class User extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class
     * @param string $className
     * @return User
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
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId, nickName, openId, generalId, bindTime', 'required'),
            array('userId, openId, generalId', 'length', 'max'=>64),
            array('nickName', 'length', 'max'=>50),
            array('avatarUrl', 'length', 'max'=>256),
            array('bindTime', 'length', 'max'=>20),
            array('isActive, delTag', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('nickName, openId', 'safe', 'on'=>'search'),
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
            'userId' => 'ID',
            'nickName' => 'Nickname',
            'openId' => 'Open ID',
            'generalId' => 'General ID',
            'avatarUrl' => 'Avatar Url',
            'bindTime' => 'Bind Time',
            'isActive' => 'Is Active',
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

        $criteria->compare('nickName',$this->nickName);
        $criteria->compare('openId',$this->openId,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    const USER_STATUS_ACTIVE_TRUE = 1;
    const USER_STATUS_ACTIVE_FALSE = 0;
    const USER_STATUS_DELETED_TRUE = 1;
    const USER_STATUS_DELETED_FALSE = 0;



    /**
     *
     * @param $openId
     * @return User  If there is not a record found, return null.
     */
    public static function findByOpenId($openId){
        if(empty($openId)){
            return null;
        }

        return self::model()->findByAttributes(array('openId' => $openId));
    }

    /**
     *
     * @param $openId
     * @return User  If there is not a record found, return null
     */
    public static function findActiveUserByOpenId($openId) {
        if(empty($openId)){
            return null;
        }

        return self::model()->findByAttributes(array('openId' => $openId, 'isActive' => self::USER_STATUS_ACTIVE_TRUE));

    }

    public static function createNewUser($openId){

        //
        $wxApiId = SystemOption::getWeChatApiId();
        $userInfo = ZtWxApiAdapter::getUserInfo($wxApiId, $openId);

        if ($userInfo['subscribe'] !== 1) {
            //若该openId并未关注公众号，则不能添加
            throw new WxAppException(CommonMessage::USER_DOES_NOT_SUBSCRIBE_OFFICIAL_ACCOUNT);
        }


        $user = self::findByOpenId($userInfo['openid']);
        if(isset($user)){
            //已经存在该用户，则不能保存
            throw new Exception(sprintf(CommonMessage::OBJECT_SAVE_ERROR_DUPLICATE, 'User', 'openId', $openId));
        }

        // 保存信息
        $user = new User();
        $user->userId = CommonFunction::create_guid_timestamp();
        $user->openId = $userInfo['openid'];
        $user->avatarUrl = $userInfo['headimgurl'];
        $user->nickName = $userInfo['nickname'];
        $user->generalId = empty($userInfo['unionid'])? CommonDefinition::FIELD_NOT_DEFINED : $userInfo['unionid'];

        $user->bindTime = date('Y-m-d H:i:s', $userInfo['subscribe_time']);

        $user->isActive = self::USER_STATUS_ACTIVE_TRUE;
        $user->delTag = self::USER_STATUS_DELETED_FALSE;

        if(!$user->save()){
            LogWriter::logModelSaveError($user, __METHOD__, array(
                'openId' => $userInfo['openid'],
                'userId' => $user->userId,
                'nickName' => $userInfo['nickname'],
                'avatarUrl' => $userInfo['headimgurl'],
                'generalId' => $userInfo['unionid'],
            ));

            throw new Exception(sprintf(CommonMessage::OBJECT_SAVE_ERROR, 'User'));
        }

        return $user;

    }

    public function activate(){
        $this->isActive = self::USER_STATUS_ACTIVE_FALSE;

        if(!$this->save()){
            LogWriter::logModelSaveError($this, __METHOD__,
                array('userId' => $this->userId, 'nickName' => $this->nickName));

            throw new Exception(sprintf(CommonMessage::OBJECT_SAVE_ERROR, 'User'));
        }

    }
}