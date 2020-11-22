<?php

debug_log("请求模块","Request模块顺利启动",__FILE__);

//外层请求安全模块
debug_log("请求模块","请求IP:".REQUEST_IP." 代理IP: ".REQUEST_FORWARDED,__FILE__);

//集体处理请求
if(empty($_REQUEST['from']))
    $_REQUEST['from']='http';

?>