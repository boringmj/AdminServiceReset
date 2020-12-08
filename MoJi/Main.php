<?php

require dirname(__FILE__).'/function.php';
require dirname(__FILE__).'/constant.php';
require dirname(__FILE__).'/config/Main.php';
require dirname(__FILE__).'/module/Load.module.php';
require dirname(__FILE__).'/module/Language.module.php';

error_reporting(APPLICATION_DEBUG_LEVEL);
date_default_timezone_set('PRC');

//目录和文件检查
$write_path_array=array();
if(CONFIG_LOG_STATUS)
{
    array_push($write_path_array,CONFIG_LOG_DIR);
    if(is_writable(CONFIG_LOG_DIR))
    {
        if(APPLICATION_DEBUG)
        {
            $log_debug_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_DEBUG_PATH;
            if(!file_exists($log_debug_path))
                file_put_contents($log_debug_path,'');
            array_push($write_path_array,$log_debug_path);
        }
        $log_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_PATH;
        if(!file_exists($log_path))
            file_put_contents($log_path,'');
        array_push($write_path_array,$log_path);
    }
}
foreach($write_path_array as $write_path)
    if(!is_writable($write_path)&&!empty($write_path))
        exit("{$write_path} ".LANGUAGE_WRITE_PATH);

//按顺序加载相对应的模块
$module_array=array('Log','Request');
LoadModule($module_array);

write_log(LANGUAGE_REQUEST_SITES,LANGUAGE_REQUEST_ADDR.REQUEST_IP,__FILE__);

?>