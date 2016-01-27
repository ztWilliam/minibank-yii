<?php
/**
 * 
 * User: william
 * Date: 15-4-20
 * Time: 下午12:06
 */

class BankMessages {

    const ERR_BANK_NAME_TOO_LONG = '银行名称不能超过50个字';
    const ERR_USER_HAS_ONE_ACTIVE_BANK = '您已经有银行了';
    const ERR_BANK_NAME_REQUIRED = '请输入银行名称';

    const ERR_MODEL_SAVING_FAILED = '%s数据保存失败。';  //需嵌入model的类名。

    const WELCOME_MESSAGE_TO_NEW_USER = '嗨，一看您就是个关心孩子健康成长的家长，良好的财商对孩子未来的美好生活至关重要，让我们一起来帮助孩子建立起对“钱”的初步认识吧';
    const WELCOME_MESSAGE_TO_OLD_USER = '嗨，欢迎回来';

    const TIP_MESSAGE_CREATE_YOUR_OWN_BANK = '首先，让我们来创建一家自己的银行...';

    const TIP_MESSAGE_BANK_INFO_NOT_SET = '设置银行名称';
    const TIP_MESSAGE_BANK_INTEREST_NOT_SET = '设置利率';
    const TIP_MESSAGE_BANK_PASSWORD_NOT_SET = '设置管理密码';

}