<?php

/* REQUEST
 * ERROR_LEVEL 错误等级 INT 允许填写为auto(),0表示拦截,1表示引导
 * ERROR_FROM 引导地址 String 错误后引导的地址
*/
$CONFIG_REQUEST=array(
    'ERROR_LEVEL'   =>  config_auto('REQUEST_ERROR_LEVEL'),
    'ERROR_FROM'    =>  config_auto('REQUEST_ERROR_FROM')
);
config_examine('CONFIG_REQUEST');

?>