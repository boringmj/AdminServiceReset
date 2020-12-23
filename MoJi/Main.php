<?php

require dirname(__FILE__).'/function.php';
require dirname(__FILE__).'/constant.php';
require dirname(__FILE__).'/config/Main.php';
require dirname(__FILE__).'/class/Main.php';
require dirname(__FILE__).'/module/Load.module.php';
require dirname(__FILE__).'/module/Language.module.php';

error_reporting(APPLICATION_DEBUG_LEVEL);
date_default_timezone_set('PRC');

//按顺序加载相对应的模块
$module_array=array('Write','Log','Database','Request');
LoadModule($module_array);

//将每一次请求都记录到日志
write_log(LANGUAGE_REQUEST_SITES,LANGUAGE_REQUEST_ADDR.REQUEST_IP,__FILE__);

?>