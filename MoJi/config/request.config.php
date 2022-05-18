<?php

/* REQUEST
 * URL 引导页地址 String 允许填写完为auto(),引导页(/index.php)的web地址
 * ERROR_LEVEL 错误等级 INT 0表示拦截,1表示引导
 * ERROR_FROM 引导地址 String 错误后引导的完整地址
 * 
 * 注意:如果需要带端口才能访问网页,那选项同样需要带上端口
 */
$CONFIG_REQUEST=array(
    'URL'           =>  config_auto('REQUEST_URL'),
    'ERROR_LEVEL'   =>  1,
    'ERROR_FROM'    =>  config_auto('REQUEST_ERROR_FROM')
);
config_examine('CONFIG_REQUEST');

?>