<?php

require dirname(__FILE__).'/function.php';
require dirname(__FILE__).'/constant.php';
require dirname(__FILE__).'/config/Main.php';
require dirname(__FILE__).'/class/Main.php';
require dirname(__FILE__).'/module/Load.module.php';
require dirname(__FILE__).'/module/Language.module.php';
require dirname(__FILE__).'/install.php';

error_reporting(APPLICATION_DEBUG_LEVEL);
date_default_timezone_set('PRC');

//预加载模块
$module_array=array('Write','Log','Check','Database');
LoadModule($module_array);

//进行安装
$Install=new Install();
if($Install->Start($Database))
    write_log(LANGUAGE_REQUEST_SITES,LANGUAGE_REQUEST_ADDR.': '.REQUEST_IP,__FILE__);
else
    exit();
unset($Install);

//后加载模块
$module_array=array('Request');
LoadModule($module_array);

?>