<?php

/* REQUEST
 * URL 引导页地址 String 允许填写完为auto(),引导页(/index.php)的web地址
 * ERROR_LEVEL 错误等级 INT 允许填写为auto(),0表示拦截,1表示引导
 * ERROR_FROM 引导地址 String 错误后引导的地址
 */
$CONFIG_REQUEST=array(
    'URL'           =>  config_auto('REQUEST_URL'),
    'ERROR_LEVEL'   =>  config_auto('REQUEST_ERROR_LEVEL'),
    'ERROR_FROM'    =>  config_auto('REQUEST_ERROR_FROM')
);
config_examine('CONFIG_REQUEST');

?>