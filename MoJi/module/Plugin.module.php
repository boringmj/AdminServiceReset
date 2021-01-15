<?php

/* 设计思路
 * 先检查插件是否符合规范(先要有一套规范)
 * 然后进行权限检查
 * 因为启用插件默认就是通过函数启用,所以不用担心变量污染问题
*/

//检查插件
if(!file_exists(PLUGIN_DATA_PATH))
{
    $dir_path=iconv('UTF-8','GBK',PLUGIN_DATA_PATH);
    mkdir($dir_path,0751,true);
    //chmod($dir_path,0751);
}
$plugin_path_array=scandir(PLUGIN_PATH);
foreach($plugin_path_array as $plugin_package_name)
{
    if($plugin_package_name==='.'||$plugin_package_name==='..')
        continue;
    $plugin_info_path=PLUGIN_DATA_PATH.'/'.$plugin_package_name.'.data.json';
    if(is_file($plugin_info_path))
    {
        //插件已被系统识别过
    }
    else
    {
        //插件未被系统识别过
        $plugin_path=PLUGIN_PATH.'/'.$plugin_package_name;
        $plugin_default_info_path=$plugin_path.'/info.json';
        $plugin_default_config_path=$plugin_path.'/config.json';
        if(is_file($plugin_default_info_path))
        {
            $config_array=array();
            //解析配置文件(前提是有配置文件)
            if(is_file($plugin_default_config_path))
            {
                $config_default_json;
                if($config_default_json=json_decode(file_get_contents($plugin_default_config_path)))
                    $config_array['Program']=$config_default_json;
                else
                    write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_CONFIG_NOT_IS_JSON_MSG,$plugin_path,10);
            }
            $info_default_json;
            if($info_default_json=json_decode(file_get_contents($plugin_default_info_path)))
            {
                $config_array['System']=array(
                    'State'=>false,
                    'Authentication'=>array(
                        'State'=>empty($info_default_json->Authentication->State)?false:$info_default_json->Authentication->State,
                        'Id'=>empty($info_default_json->Authentication->Id)?'':$info_default_json->Authentication->Id,
                        'Sign'=>empty($info_default_json->Authentication->Sign)?'':$info_default_json->Authentication->Sign,
                        'Succeed'=>false
                    )
                );
                file_put_contents($plugin_info_path,json_encode($config_array));
                write_log(LANGUAGE_LOG_PLUGIN_ADD_TITLE,(empty($info_default_json->Name)?'-NULL-':$info_default_json->Name)."({$plugin_path})".' '.LANGUAGE_LOG_PLUGIN_ADD_MSG,$plugin_path,5);
            }
            else
                write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_INFO_NOT_IS_JSON_MSG,$plugin_path,20);
        }
        else
        {
            write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_NOT_INFO_MSG,$plugin_path,20);
        }
    }
}

//启用插件
function load_plugin()
{

}

?>