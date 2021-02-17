<?php

/* 设计思路
 * 先检查插件是否符合规范(先要有一套规范)
 * 然后进行权限检查
 * 因为启用插件默认就是通过函数启用,所以不用担心变量污染问题
*/

//兼容的sdk版本
$compatible_level=array(1);

//检查插件
if(!file_exists(PLUGIN_DATA_PATH))
{
    $dir_path=iconv('UTF-8','GBK',PLUGIN_DATA_PATH);
    mkdir($dir_path,0751,true);
}
$plugin_path_array=scandir(PLUGIN_PATH);
foreach($plugin_path_array as $plugin_package_name)
{
    if($plugin_package_name==='.'||$plugin_package_name==='..')
        continue;
    $is_complete=false;
    $plugin_info_path=PLUGIN_DATA_PATH.'/'.$plugin_package_name.'.data.json';
    $plugin_path=PLUGIN_PATH.'/'.$plugin_package_name;
    $plugin_default_info_path=$plugin_path.'/info.json';
    $plugin_default_config_path=$plugin_path.'/config.json';
    $plugin_main_path=$plugin_path.'/src/main.php';
    $data_json;
    if(is_file($plugin_default_info_path))
    {
        $info_default_json;
        if($info_default_json=json_decode(file_get_contents($plugin_default_info_path)))
        {
            //包名命名检查以及注册与实际是否一致
            $main_package_name=empty($info_default_json->PackageName)?'':$info_default_json->PackageName;
            if(preg_match("/^com\.[A-Za-z0-9\._\-]+$/",$main_package_name))
            {
                if($plugin_package_name!==$main_package_name)
                    write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_PACKAGE_NMAE_NOT_STANDARD,$plugin_path,15);
                else
                    $is_complete=true;
            }
            else
                write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_PACKAGE_NMAE_NOT_STANDARD,$plugin_path,15);
        }
        else
            write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_INFO_NOT_IS_JSON_MSG,$plugin_path,15);
    }
    else
        write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_NOT_INFO_MSG,$plugin_path,15);
    //检查插件是否兼容于系统
    if(!in_array($info_default_json->Level,$compatible_level))
    {
        write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_NOT_COMPATIBLE_LEVEL_MSG,$plugin_path,15);
        continue;
    }
    //检查插件是否兼容于当前php版本
    if(version_compare(PHP_VERSION,$info_default_json->Compatible,'<'))
    {
        write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_NOT_COMPATIBLE_PHP_VERSION_MSG,$plugin_path,15);
        continue;
    }
    //检查 main.php 是否存在
    if(!is_file($plugin_main_path))
    {
        write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_MAIN_NOT_STANDARD,$plugin_path,15);
        continue;
    }
    //如果插件数据无法正常被识别就直接格式化
    if(is_file($plugin_info_path))
    {
        if(!$data_json=json_decode(file_get_contents($plugin_info_path)))
        {
            unlink($plugin_info_path);
            write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_DATA_NOT_JSON_MSG,$plugin_path,10);
        }
    }

    //没有问题继续处理
    if($is_complete)
    {
        //识别是否更新
        if(is_file($plugin_info_path))
        {
            # 这里留个坑,以后都不一定会填(检测到更新直接把数据删了)
            if($data_json->System->Grade!=$info_default_json->Grade)
                unlink($plugin_info_path);
        }
        //没有被识别过
        if(!is_file($plugin_info_path))
        {
            $config_array=array();
            if(is_file($plugin_default_config_path))
            {
                //解析配置文件(前提是有配置文件)
                $config_default_json;
                if($config_default_json=json_decode(file_get_contents($plugin_default_config_path)))
                    $config_array['Program']=$config_default_json;
                else
                    write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_CONFIG_NOT_IS_JSON_MSG,$plugin_path,10);
            }
            //主类命名检查
            $main_class=empty($info_default_json->Main)?'':$info_default_json->Main;
            if(!preg_match("/^Plugin[A-Z][A-Za-z0-9]+$/",$main_class))
            {
                write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_MAIN_CLASS_NOT_STANDARD,$plugin_path,15);
                continue;
            }
            //main.php 文件检查
            $main_content=file_get_contents($plugin_main_path);
            if(!preg_match('/class\s+'.$main_class.'.*\{.*(public\s+function\s+Start\s*\().*\}/s',$main_content))
            {
                write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_MAIN_CLASS_FORMAT_NOT_STANDARD,$plugin_path,15);
                continue;
            }
            //如若插件完整就记录识别
            $config_array['System']=array(
                'State'=>false,
                'Grade'=>empty($info_default_json->Grade)?0:$info_default_json->Grade,
                'Main'=>empty($info_default_json->Main)?0:$info_default_json->Main,
                'Authentication'=>array(
                    'State'=>empty($info_default_json->Authentication->State)?false:$info_default_json->Authentication->State,
                    'Id'=>empty($info_default_json->Authentication->Id)?'':$info_default_json->Authentication->Id,
                    'Sign'=>empty($info_default_json->Authentication->Sign)?'':$info_default_json->Authentication->Sign,
                    'Succeed'=>false
                )
            );
            file_put_contents($plugin_info_path,json_encode($config_array));
            write_log(LANGUAGE_LOG_PLUGIN_ADD_TITLE,(empty($info_default_json->Name)?'-NULL-':$info_default_json->Name)."({$plugin_package_name})".' '.LANGUAGE_LOG_PLUGIN_ADD_MSG,__FILE__,5);
            //更新数据
            $data_json=json_decode(file_get_contents($plugin_info_path));
            //初始化插件
            $data_path=PLUGIN_DATA_PATH.'/'.$main_package_name;
            if(!file_exists($data_path))
            {
                $dir_path=iconv('UTF-8','GBK',$data_path);
                mkdir($dir_path,0751,true);
            }
            require $plugin_main_path;
            //加载插件: Init()
            $main_class::Init($Database,$data_path);
        }
        //开始整理目前的插件并准备好加载
        if($data_json->System->State)
        {
            //检查主类命名是否重复
            if(array_key_exists($data_json->System->Main,$plugin_array))
            {
                write_log(LANGUAGE_LOG_PLUGIN_PARSING_FAILED,LANGUAGE_LOG_PLUGIN_MAIN_REPEAT_MSG,$plugin_path,15);
            }
            else
            {
                //导入插件主类
                require $plugin_main_path;
                //插件如果状态是开启就添加到就绪列表中
                $plugin_array[$data_json->System->Main]=array('PackageName'=>$plugin_package_name,'Config'=>(empty($data_json->Program)?'':$data_json->Program));
            }
        }
    }
}

//检查插件是否已经就绪
function check_plugin($main_class)
{
    global $plugin_array;
    if(class_exists($main_class)&&isset($plugin_array[$main_class]))
        return 1;
    else
        return 0;
}

//加载单个插件
function load_plugin($main_class)
{
    global $Database,$plugin_array;
    if(check_plugin($main_class))
    {
        $plugin_array[$main_class]['Object']=new $main_class();
        $data_path=PLUGIN_DATA_PATH.'/'.$plugin_array[$main_class]['PackageName'];
        if(!file_exists($data_path))
        {
            $dir_path=iconv('UTF-8','GBK',$data_path);
            mkdir($dir_path,0751,true);
        }
        $plugin_array[$main_class]['Object']->Start($Database,$data_path,$plugin_array[$main_class]['Config']);
    }
}

?>