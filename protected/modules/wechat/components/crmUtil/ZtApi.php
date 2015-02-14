<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-6
 * Time: 下午12:16
 */

class ZtApi {

    const TOKEN_INVALID_FLAG = 9;

    public $accessToken;
    public $ztId;
    public $openId;

    public static function upgradeToLevel($openid, $upToLevelId)
    {
        $url = self::getApiBaseUrl() . "upgradeToLevel";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'openId'=>$openid,
            'addToLevel'=>$upToLevelId
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);

        return $json['data'][0];

    }

    /*api的返回信息：ResponseData结构
    {
         responseCode   //整数。 0, 表示成功， 非0，表示错误，具体错误码另行说明
         responseDesc   //字符串。 如果发生错误，则为提示信息。
         data     //数组，返回的数据，按api的约定，返回1个或多个数据。
         responseType    //字符串。待扩展，一般为空字符串。
    }*/
    /*获取当前用户可用的角色列表（客户经理、投资顾问等）
    提交参数：成功登录后的 accessToken
    返回角色对象数组
    data = ["accessToken", [{"roleCode":"fc", "roleName":"投资顾问"}, {"roleCode":"bs", "roleName":"客户经理"}]]
    */

    private static function getApiBaseUrl(){
        $baseUrl = "http://" . CRM_API_URL . "/mobile/api/";
        return $baseUrl;
    }

    private static function callRemoteFunc($url, $data,
                                           $method = 'GET',
                                           $tokenNeedSave = true ) {
        $callResult = CommonFunction::callHttp($url,$data,$method);

        try {
            $json = FastJSON::decode($callResult);
            if(!isset($json['responseCode'])) {
                //说明返回的并不是有效的响应数据：
                throw new Exception('获取的结果不合法：' . $callResult);
            }

            if($json['responseCode'] == self::TOKEN_INVALID_FLAG) {
                //若当前的AccessToken失效，则抛出需要重新登录的异常
                throw new WxNeedLoginException($json['responseDesc']);
            }

            if($tokenNeedSave) {
                self::saveAccessToken($json);
            }

            if($json['responseCode'] !== 0){
                throw new WxAppException($json['responseDesc']);
            }

            return $json;
        } catch (WxAppException $ex ) {
            throw $ex;
        } catch(Exception $ex) {
            Yii::log('调用远程CrmApi['. $url .']时出错：' . $ex->getMessage(), 'error');
            throw $ex;
        }

    }

    public static function getAllRoles()
    {
        $url = self::getApiBaseUrl() . "getAllRoles";
        echo $url;
        #$data = array('APIClientToken'=>API_CLIENT_TOKEN);
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken'=>Yii::app()->session['accessToken']);
        $json = self::callRemoteFunc($url,$data);
        return $json['data'][1];
    }

    /*新增微信用户
    提交参数：openId  、introducerOpenId (引荐人的openid)、 levelId (级别)、initDays (期限)、wechatAccount:"微信账号名称"，wechatNickName：微信昵称
    返回userId
    data = ["具体的userID"]
    */
    public static function newWeChatUser($openid,$introducerOpenId,$levelId,$initDays, $wechatNickName ='', $wechatAccount = '')
    {
        $url = self::getApiBaseUrl() . "newWeChatUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openId'=>$openid,
                        'introducerOpenId' => $introducerOpenId,
                        'levelId'          => $levelId,
                        'initDays'         => $initDays,
                        'wechatAccount'    => $wechatAccount,
                        'wechatNickName'   => $wechatNickName,
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);

        return $json['data'][0];

    }

    /*新增内部用户
    提交参数：accessToken , params={openId、orgId (直属部门Id)、roleCode (角色code)
    userName (姓名)、email（邮箱，用于登录）、password（初始密码）}
    返回userID
    data = ["具体的userID"]
    */
    public static function newInnerUser($orgId,$roleCode,$userName,$password,$email)
    {
        $url = self::getApiBaseUrl() . "newInnerUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken'=>Yii::app()->session['accessToken'],
                        'orgId' => $orgId,
                        'roleCode' => $roleCode,
                        'userName' => $userName,
                        'password' => $password,
                        'email' => $email,
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        if($json['responseCode'] == 0){
            $replyStatus = $json['data'][1];
        }else{
            $replyStatus = $json['responseDesc'];
        }

        return $replyStatus;
    }

    public static function getParentIdOfOrg($branchId)
    {
        $url = self::getApiBaseUrl() . "getParentOrgId";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken' => Yii::app()->session['accessToken'],
            'branchId' => $branchId,
//                      'openId'=>Yii::app()->user->getOpenId(),
        );
        $json = self::callRemoteFunc($url,$data);
        #print_r($json);
//        self::saveAccessToken($json);
        return $json['data'][1];

    }

    /*用微信登录
    url：http://apitest.zhengtuo.net/mobile/api/loginFromWeChat
    post
    提交参数： openId
    返回值，responseCode 为0表示成功，data中包含用户信息：
    data = ["accessToken", {"userID":"登录者的id", "userName":"xxxx", "userRole":"fc", "openId":"如果已绑定微信，就有值，没绑定，就为空字符串"}]
    */
    private function loginFromWeChat($openid)
    {
        $url = self::getApiBaseUrl() . "loginFromWeChat";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openId'=>$openid
        );
        $json = self::callRemoteFunc($url,$data,'POST', true);
        return $json;
    }

    /*获取当前微信用户的帐户详细信息
    url：http://apitest.zhengtuo.net/mobile/api/getWeChatUserInfo
    get
    参数：openId
    data = [ {"userID":"登录者的id", "userName":"xxxx", "userRole":"fc", "contractLevelName":"普通用户", "contractLevelValue":"0", "fcList":[{"fcName":"林1", "fcUserId":"1000140"},{"fcName":"林2", "fcUserId":"1000141"}], "remainDays":"25", "fcMaxCount":"3"}]
    */
    public static function getWeChatUserInfo($openid)
    {
        $url = self::getApiBaseUrl() . "getWeChatUserInfo";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
//                     'accessToken'=>Yii::app()->session['accessToken'],
                      'openId'=>$openid,
        );
        $json = self::callRemoteFunc($url, $data, 'GET', false);

        return $json['data'][0];

    }


    /*获取内部用户详细信息*/
    public static function getInnerUserInfo($userID)
    {
        $url = self::getApiBaseUrl() . "innerUserInfo";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                      'accessToken'=>Yii::app()->session['accessToken'],
                      'userID'=>$userID,
        );
        $json = self::callRemoteFunc($url,$data);
        return $json['data'][1];
    }
    /*12、清除机构内的所有微信用户
    url：http://[域名]/mobile/api/clearWeChatUser
    post
    提交参数：accessToken
    返回 responseCode 为0表示成功
    data = ["accessToken"]*/
    public function clearWeChatUser()
    {
        $url = self::getApiBaseUrl() . "clearWeChatUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                       'accessToken'=>Yii::app()->session['accessToken'],
        );
        $json = self::callRemoteFunc($url,$data,'POST', true);
//        print_r($json);
        return isset($json);
//        if($json->responseCode == 0){
//            Yii::app()->session['accessToken'] = $json->data->accessToken;
//            return true;
//        }
//        else{
//            return false;
//        }
    }

    /*禁用内部用户（保留数据）
    url：http://apitest.zhengtuo.net/mobile/api/removeInnerUser
    post
    提交参数：accessToken , params = {"userID":"要移除的用户ID"}
    返回 ：responseCode表示成功或失败 ，如果
    data = ["accessToken"]
    */
    public static function removeInnerUser($userId)
    {
        $url = self::getApiBaseUrl() . "removeInnerUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken'=>Yii::app()->session['accessToken'],
            'userID'=>$userId,
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        return $json['responseDesc'];
    }

    /*用email 和密码登录管理后台
    url：http://[域名]/mobile/api/login
    post
    提交参数：params={"loginId":"登录用的邮箱", "psd":"密码"}
    返回值，responseCode 为0表示成功，data中包含用户信息：
    data = ["accessToken", {"userID":"登录者的id", "userName":"xxxx", "userRole":"fc", "openId":"如果已绑定微信，就有值，没绑定，就为空字符串"}]
    */
    public static function login($loginId,$psd)
    {
        $url = self::getApiBaseUrl() . "login";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                           'loginId'=>$loginId,
                           'psd'=>$psd
        );
        #print_r($data);
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }

    /*修改内部用户信息
    url：http://[域名]/mobile/api/modifyInnerUser
    post
    提交参数：accessToken , params={userID,orgId (直属部门Id)、roleCode (角色code)、userName (姓名)、email（邮箱）、resetPsd(0或1，1表示要修改密码)、password（要修改的密码，无需重置则为空）}
    返回 responseCode 为0表示成功
    data = ["accessToken"]
    */
    public static function modifyInnerUser($userID,$orgId,$roleCode,$userName,$email,$resetPsd,$password)
    {
        $url = self::getApiBaseUrl() . "modifyInnerUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken'=> Yii::app()->session['accessToken'],
                        'userID' => $userID,
                        'orgId' => $orgId,
                        'roleCode' => $roleCode,
                        'userName' => $userName,
                        'email' => $email,
                        'resetPsd' => $resetPsd,
                        'password' => $password,
        );
        $json = self::callRemoteFunc($url,$data,'POST');

//        return isset($json);
        if($json['responseCode'] == 0){
//            Yii::app()->session['accessToken'] = $json->data->accessToken;
            return true;
        }
        else{
            return false;
        }
    }

    /*绑定微信
    url：http://[域名]/mobile/api/bindWeChat
    post
    提交参数："accessToken"="登录后获取的accessToken",, "openId":"待绑定的微信openId", "wechatAccount":"微信账号名称", "wechatNickName":"微信昵称"
    返回值，responseCode 为0表示成功
    data = ["accessToken"]
    */
    public static  function bindWeChat($openid,$wechatNickName,$accesstoken)
    {
        $url = self::getApiBaseUrl() . "bindWeChat";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken'=>$accesstoken,
                        'openId'=>$openid,
                        'wechatNickName'=>$wechatNickName,
                      );

        $json = self::callRemoteFunc($url,$data,'POST');


        return $json;
    }

    /*获取当前用户所在的组织架构
    url：http://apitest.zhengtuo.net/mobile/api/getOrgStructure
    get
    参数：openId
    data = [accessToken, {"nodeName":"总部", "nodeId":"id1", "children":
    [
    {"nodeName":"分公司1", "nodeId":"id**", "children":
         [{"nodeName":"营业部1", "nodeId":"id**", "children":[{}]},
              {"nodeName":"营业部2", "nodeId":"id**", "children":[{}]},
              {"nodeName":"营业部3", "nodeId":"id**", "children":[{}]}]
    },
     {"nodeName":"分公司2", "nodeId":"id**", "children":
         [{"nodeName":"营业部1", "nodeId":"id**", "children":[{}]},
         {"nodeName":"营业部2", "nodeId":"i**", "children":[{}]},
         {"nodeName":"营业部3", "nodeId":"id**", "children":[{}]}]
    }
    ]}]
    */
    public static function getOrgStructure()
    {
        $url = self::getApiBaseUrl() . "getOrgStructure";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                      'accessToken' => Yii::app()->session['accessToken'],
//                      'openId'=>Yii::app()->user->getOpenId(),
        );
        $json = self::callRemoteFunc($url,$data);
        #print_r($json);
//        self::saveAccessToken($json);
        return $json['data'][1];

    }

    public static function getSubOrgStructure($rootId)
    {
        $url = self::getApiBaseUrl() . "getSubOrgStructure";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken' => Yii::app()->session['accessToken'],
            'rootId' => $rootId,
//                      'openId'=>Yii::app()->user->getOpenId(),
        );
        $json = self::callRemoteFunc($url,$data);
        #print_r($json);
//        self::saveAccessToken($json);
        return $json['data'][1];

    }


    /*获取摩欧机构下所有内部用户列表
    get
    提交参数：accessToken,orgId(直属部门id)、keyword（用于搜索的关键词）
    */
    public static function usersInOrg($orgId,$keyword='')
    {
        $url = self::getApiBaseUrl() . "usersInOrg";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                      'accessToken' => Yii::app()->session['accessToken'],
                      'orgId'=>$orgId,
                      'keyword' =>$keyword
        );
        $json = self::callRemoteFunc($url,$data);

        return $json['data'][1];

    }


    /*新增组织结构节点
    url：http://apitest.zhengtuo.net/mobile/api/addOrg
    post
    提交参数：accessToken,  {"nodeName":"新节点名称", "parentId":"父节点的Id" }
    返回：responseCode表示成功或失败
    */
    public static function addOrg($nodeName,$parentId)
    {
        $url = self::getApiBaseUrl() . "addOrg";
        $data = array(  'APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken' => Yii::app()->session['accessToken'],
                        'nodeName'=>$nodeName,
                        'parentId'=>$parentId,
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        if($json['responseCode'] == 0){
            $replyStatus = '添加成功';
        }else{
            $replyStatus = $json['responseDesc'];
        }

        return $replyStatus;
    }

    /*移除组织结构节点
    url：http://apitest.zhengtuo.net/mobile/api/delOrg
    提交参数：accessToken,  {"nodeId":"待删除节点id" }
    返回：responseCode表示成功或失败
    */
    public static function delOrg($nodeId)
    {
        $url = self::getApiBaseUrl() . "delOrg";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken' => Yii::app()->session['accessToken'],
            'nodeId'=>$nodeId,
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        if($json['responseCode'] == 0){
            $replyStatus = '删除成功';
        }else{
            $replyStatus = $json['responseDesc'];
        }

        return $replyStatus;
    }

    /*变更组织结构节点名称
    url：http://apitest.zhengtuo.net/mobile/api/modifyOrg
    提交参数：accessToken,  {"nodeName":"新节点名称", "nodeId":"待变更节点的Id" }
    返回：responseCode表示成功或失败
    */
    public static function modifyOrg($nodeName,$nodeId)
    {
        $url = self::getApiBaseUrl() . "modifyOrg";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'accessToken' => Yii::app()->session['accessToken'],
                       'nodeName'=>$nodeName,
                       'nodeId'=>$nodeId,
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        if($json['responseCode'] == 0){
            $replyStatus = '修改成功';
        }else{
            $replyStatus = $json['responseDesc'];
        }
//        self::saveAccessToken($json);
        return $replyStatus;
    }

    /*获取当前用户所在组织的所有级别信息
    url：http://apitest.zhengtuo.net/mobile/api/getAllLevels
    get
    提交参数：openId
    返回
    data = [{"id":"##", "contractLevelName":"普卡客户", "contractLevelValue":"1", "fcMaxCount":"1"},
                {"id":"##", "contractLevelName":"金卡客户", "contractLevelValue":"2", "fcMaxCount":"3"},
                {"id":"##", "contractLevelName":"钻石客户", "contractLevelValue":"3", "fcMaxCount":"-1"}]        //-1表示不限制数量
    */
    public static function getAllLevels()
    {
        $url = self::getApiBaseUrl() . "getAllLevels";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken' => Yii::app()->session['accessToken'],
        );
        $json = self::callRemoteFunc($url,$data);
//        self::saveAccessToken($json);

        return $json['data'][1];

    }


    public static function getAllLevelsWithWeChat($openid)
    {
        $url = self::getApiBaseUrl() . "getAllLevelsWithWechat";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
            'openId' => $openid,
        );

        $json = self::callRemoteFunc($url,$data, 'GET', false);

        return $json['data'][0];
    }


    /*获取当前用户的级别信息
    url：http://apitest.zhengtuo.net/mobile/api/getMyLevelInfo
    get
    参数：openId
    data = [ {"contractLevelName":"普通用户", "contractLevelValue":"0", "fcList":[{"fcName":"林1", "fcUserId":"1000140"},{"fcName":"林2", "fcUserId":"1000141"}], "remainDays":"25", "fcMaxCount":"3"}]
    */
    public static  function getMyLevelInfo($openid)
    {
        $url = self::getApiBaseUrl() . "getMyLevelInfo";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken' => Yii::app()->session['accessToken'],
                        'openId'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data);
//        print_r($json);
//        self::saveAccessToken($json);
        return $json;
    }
    /*续约
    url：http://apitest.zhengtuo.net/mobile/api/addContractDays
    post
    参数：openId, {"addDays":"10"}
    返回值：
    data = [ {"contractLevelName":"普通用户", "contractLevelValue":"0", "fcList":[{"fcName":"林1", "fcUserId":"1000140"},{"fcName":"林2", "fcUserId":"1000141"}], "remainDays":"续约后的天数", "fcMaxCount":"3"}]
    */
    public static function addContractDays($openid,$addDays)
    {
        $url = self::getApiBaseUrl() . "addContractDays";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openId'=>$openid,
                        'addDays'=>$addDays
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);

        return $json['data'][0];
    }

    /*升级
    url：http://apitest.zhengtuo.net/mobile/api/upgradeLevel
    post
    参数：openId, {"addLevel":"待升的级数"}
    返回值：
    data = [ {"contractLevelName":"升级后的级别", "contractLevelValue":"升级后的value", "fcList":[{"fcName":"林1", "fcUserId":"1000140"},{"fcName":"林2", "fcUserId":"1000141"}], "remainDays":"25", "fcMaxCount":"升级后的最大数量"}]
    */
    public  static function upgradeLevel($openid,$addLevel)
    {
        $url = self::getApiBaseUrl() . "upgradeLevel";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openId'=>$openid,
                        'addLevel'=>$addLevel
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);

        return $json['data'][0];
    }

    /*关注投顾 （微信应用内部记录的基础上，必须向api登记关注信息）
    url：http://apitest.zhengtuo.net/mobile/api/followUser
    post
    参数：openId, {"newFollowId":"新关注的userId"}
    array('openId'=>当前用户的openId, 'params'=>array('newFollowId'=>'要关注的投顾的id'), 'APIClientToken'=>'51FC_wechat')
    返回值：
    data = [ {"contractLevelName":"当前级别", "contractLevelValue":"当前value", "fcList":[【增加新投顾的列表】], "remainDays":"25", "fcMaxCount":"最大数量"}]
    */
    public function followUser($openid,$newFollowId)
    {
        $url = self::getApiBaseUrl() . "followUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
                        'newFollowId'=>$newFollowId
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*取消关注（微信应用内部记录的基础上，必须向api登记取消关注的信息）
    url：http://apitest.zhengtuo.net/mobile/api/unfollowUser
    post
    参数：openId, {"delFollowId":"待取消关注的userId"}
    返回值：
    data = [ {"contractLevelName":"当前级别", "contractLevelValue":"当前value", "fcList":[【去掉该投顾的列表】], "remainDays":"25", "fcMaxCount":"最大数量"}]
    */
    public function unfollowUser($openid,$delFollowId)
    {
        $url = self::getApiBaseUrl() . "unfollowUser";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
            'delFollowId'=>$delFollowId
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*获取所有当前用户可选的投顾列表
    url：http://[域名]/mobile/api/selectableFcList
    get
    参数：openId
    返回值：
    data = [ [{"userName":"投顾的姓名", "follows":"关注人数", "lastShowTime":"上次直播时间", "isFollowing":"0或1（表示当前用户是否已经关注）"}, {……}, ……]]
    （备注：如果已经关注的，后面跟的是“取消关注”的动作，如果是未关注的，后面跟的是“关注”的动作）
    */
    public function selectableFcList($openid)
    {
        $url = self::getApiBaseUrl() . "selectableFcList";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openId'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data, 'GET', false);
        return $json;
    }

    /*投顾设置直播室选项
    url：http://[域名]/mobile/api/setMyCastRoom
    post
    参数：accessToken=>"", params=>{"maxUser":"最大人数", "name":"直播室名称", "desc":"概要描述"（不超过300字）}
    返回值：responseCode表示成功或失败
    data = ["accessToken"]
    */
    public function setMyCastRoom($maxUser,$name,$desc)
    {
        $url = self::getApiBaseUrl() . "setMyCastRoom";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken' => Yii::app()->session['accessToken'],
                        'maxUser'=>$maxUser,
                        'name'=>$name,
                        'desc'=>$desc,

        );
        $json = self::callRemoteFunc($url,$data,'POST');
        return $json;
    }
    /*用户进入某投顾的直播室
    url：http://[域名]/mobile/api/enterCastRoom
    post
    参数：openId=>"", params=>{"fcUserId":"投顾的userId"}
    返回值：responseCode表示成功或失败
    */
    public function enterCastRoom($openid,$fcUserId)
    {
        $url = self::getApiBaseUrl() . "enterCastRoom";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
                        'fcUserId'=>$fcUserId
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*获取某投顾直播室内当前所有听众
    url：http://[域名]/mobile/api/usersInCastRoom
    get
    参数：openId=>"" (openId必须是投顾的openId)

    返回值：
    data = [["openid1", "openid2",……]]
    */
    public function usersInCastRoom($openid)
    {
        $url = self::getApiBaseUrl() . "usersInCastRoom";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                       'openid'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*开始直播
    url：http://[域名]/mobile/api/liveShowOn
    post
    参数：openId=>""

    返回值：responseCode表示成功
    成功的话，需要把responseDesc里的内容，发给所有当前直播室中的用户
    失败的话，需要把responseDesc里的内容，发给发起该请求（即参数openId所指的）用户
    data = [["openid1", "openid2",……]]
    */
    public function liveShowOn($openid)
    {
        $url = self::getApiBaseUrl() . "liveShowOn";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                       'openid'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*停止直播
    url：http://[域名]/mobile/api/liveShowOff
    post
    参数：openId=>""

    返回值：responseCode表示成功或失败
    成功的话，需要把responseDesc里的内容，发给所有当前直播室中的用户
    失败的话，需要把responseDesc里的内容，发给发起该请求（即参数openId所指的）用户
    data = [["openid1", "openid2",……]]
    */
    public function liveShowOff($openid)
    {
        $url = self::getApiBaseUrl() . "liveShowOff";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*用户离开某投顾的直播室
    url：http://[域名]/mobile/api/leaveCastRoom
    post
    参数：openId=>"", params=>{"fcUserId":"投顾的userId"}

    返回值：responseCode表示成功或失败
    */
    public function leaveCastRoom($openid,$fcUserId)
    {
        $url = self::getApiBaseUrl() . "leaveCastRoom";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
                        'params'=>array("fcUserId"=>$fcUserId)
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }
    /*投顾提交直播预告
    url：http://[域名]/mobile/api/setNextShowTime
    post
    参数：openId=>""（必须是投顾的openId）, params=>{"nextShowTime":"日期"}
    返回值：responseCode表示成功或失败
    */
    public function setNextShowTime($openid,$nextShowTime)
    {
        $url = self::getApiBaseUrl() . "setNextShowTime";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'accessToken' => Yii::app()->session['accessToken'],
                        'openid'=>$openid,
                        "nextShowTime"=>$nextShowTime
        );
        $json = self::callRemoteFunc($url,$data,'POST');
        return $json;
    }
    /*获取当前用户可选的直播室
    url：http://[域名]/mobile/api/allCastRooms
    get
    参数：openId=>""
   返回值：responseCode表示成功或失败
    data = [[{"roomName":"直播室名称", "hostUserName":"投顾名称", "hostUserId":"投顾的id","showTime":"开播时间",
        "status":"未开播/正在直播", "usersInRoom":"直播室内人数", "maxUser":"直播室规定的最大人数"}, ……]]
     */
    public function allCastRooms($openid)
    {
        $url = self::getApiBaseUrl() . "allCastRooms";
        $data = array('APIClientToken'     => API_CLIENT_TOKEN,
                        'openid'=>$openid,
        );
        $json = self::callRemoteFunc($url,$data,'POST', false);
        return $json;
    }


    //保存token至session供下次使用
    private function saveAccessToken($json)
    {
        if($json['data'][0])
            Yii::app()->session['accessToken'] = $json['data'][0];
        else
            throw new Exception('api调用的结果数据中不存在accessToken'. FastJSON::encode($json));
    }

}