<?php

debug_log(LANGUAGE_REQUEST_NAME,LANGUAGE_REQUEST_SUCCESS,__FILE__);

//外层请求安全模块
debug_log(LANGUAGE_REQUEST_NAME,LANGUAGE_REQUEST_IP.': '.REQUEST_IP.' '.LANGUAGE_AGENT_IP.': '.REQUEST_FORWARDED,__FILE__);

//集体处理请求
if(empty($_REQUEST['type']))
    $_REQUEST['type']='http';
if($_REQUEST['type']==='http')
    header('Content-Type: text/html; charset='.CONFIG_REQUEST_HTTP_CODE);
else if($_REQUEST['type']==='json')
    header('Content-Type:application/json');

//调用主程序
include APPLICATION_PATH.'/Main.php'

?>