<?php

//获取开始时间
$start_time=microtime(true); 

require __DIR__.'/function.php';
require __DIR__.'/constant.php';
require __DIR__.'/config/_loader.php';
require __DIR__.'/lib/_loader.php';
require __DIR__.'/class/_loader.php';
require __DIR__.'/module/Load.module.php';
require __DIR__.'/module/Language.module.php';
require __DIR__.'/install.php';

error_reporting(APPLICATION_DEBUG_LEVEL);
date_default_timezone_set('PRC');
header('Content-Type: text/html; charset='.CONFIG_HTTP_CODE);

//预定义变量
$Database;
$plugin_array=array();

//预加载模块
$module_array=array('Write','Log','Check','Database','Admin','Plugin');
LoadModule($module_array);

//加载插件: Strat()
foreach($plugin_array as $main_class=>$plugin_data)
    load_plugin($main_class);

//进行安装
$Install=new Install();
if($Install->Start($Database))
    write_log(LANGUAGE_LOG_REQUEST_SITES,LANGUAGE_LOG_REQUEST_ADDR.': '.REQUEST_IP,__FILE__);
else
    exit();
unset($Install);

//后加载模块
$module_array=array('Request');
LoadModule($module_array);

//获取结束时间
$end_time=microtime(true);
//计算执行时间
if(APPLICATION_DEBUG)
{
    $total_time=$end_time-$start_time;
    debug_log(LANGUAGE_LOG_EXECUTION_TIME_TITLE,LANGUAGE_LOG_EXECUTION_TIME.' '.$total_time.'S',__FILE__);
}

?>