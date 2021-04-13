<?php

/* VERIFICATION
 * KEY 秘钥 String 允许填写完为auto(),长度推荐32位,允许定义为任何字符
 * SALT 盐 String 允许填写完为auto(),长度推荐32位,允许定义为任何字符
 * EXPIRE_TIME 过期时间 INT 这里控制了一个验证的生命周期,请慎重
 * 
 * 注意:所有选项修改后不会立即生效,需要用户自行删除 项目路径/Data(DATA_PATH) 目录下的 verification.json 后生效
 */
$CONFIG_VERIFICATION=array(
    'KEY'           =>  config_auto('VERIFICATION_KEY'),
    'SALT'          =>  config_auto('VERIFICATION_SALT'),
    'EXPIRE_TIME'   =>  300
);

//因为情况特殊,所以需要一点运算,如果目录不可写且采用的自动获取那么就可能出点大问题,值会一直变化
if(is_writable(DATA_PATH))
{
    $_config_data_path=DATA_PATH.'/verification.json';
    if(is_file($_config_data_path))
    {
        $_config_json=json_decode(file_get_contents($_config_data_path));
        $CONFIG_VERIFICATION=array(
            'KEY'=>$_config_json->KEY,
            'SALT'=>$_config_json->SALT,
            'EXPIRE_TIME'=>$_config_json->EXPIRE_TIME
        );
    }
    else
        file_put_contents($_config_data_path,json_encode($CONFIG_VERIFICATION));
}
config_examine('CONFIG_VERIFICATION');

?>