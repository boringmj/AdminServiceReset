<?php

/* KEY
 * KEY 秘钥 String 允许填写完为auto(),长度推荐32位,允许定义为任何字符
 * SALT 盐 String 允许填写完为auto(),长度推荐32位,允许定义为任何字符
 * 
 * 注意:
 * 1. 模块、插件依赖本配置文件进行运算,请勿在程序安装后随意修改此配置文件,出现任何问题请自行处理
 * 2. 所有选项修改后不会立即生效,需要用户自行删除 项目路径/Data(DATA_PATH) 目录下的 key.json 后生效
 * 3. 请自行确认项目路径/Data(DATA_PATH) 目录下的 key.json 文件是否可写入
 * 
 * 安全警告: 该配置文件涉及到全局安全,如不了解请勿随意修改,且所有插件可读取此配置文件,请勿随意加载来源不明的插件
 */
$CONFIG_KEY=array(
    'KEY'           =>  config_auto('KEY_KEY'),
    'SALT'          =>  config_auto('KEY_SALT')
);

//因为情况特殊,所以需要一点运算,如果目录不可写且采用的自动获取那么就可能出点大问题,值会一直变化
if(is_writable(DATA_PATH))
{
    //可以自行将该算法修改并移动到 Write 模块中以获得自动检测是否允许写入的功能
    $_config_data_path=DATA_PATH.'/key.json';
    if(is_file($_config_data_path))
    {
        $_config_json=json_decode(file_get_contents($_config_data_path));
        $CONFIG_KEY=array(
            'KEY'=>$_config_json->KEY,
            'SALT'=>$_config_json->SALT
        );
    }
    else
        file_put_contents($_config_data_path,json_encode($CONFIG_KEY));
}
config_examine('CONFIG_KEY');

?>