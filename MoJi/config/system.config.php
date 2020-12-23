<?php

/* REQUEST
 * HTTP_TYPE WEB请求类型 String 允许填写为auto(),http,https
 * HTTP_PATH WEB请求地址 String 允许填写为auto(),地址带端口
 * HTTP_CODE 编码类型 String 默认为utf-8
*/
$CONFIG_REQUEST=array(
    'HTTP_TYPE'     =>  config_auto('REQUEST_HTTP_TYPE'),
    'HTTP_PATH'     =>  config_auto('REQUEST_HTTP_PATH'),
    'HTTP_CODE'     =>  'UTF-8'
);
config_examine('CONFIG_REQUEST');

/* INFO
 * 暂不提供帮助
 * 请您不要在任何未授权的情况下修改下面的信息,修改后视为侵权
*/
$CONFIG_INFO=array(
    'AUTHOR'        =>  'Wuliaomj',
    'OWNER'         =>  'Wuliaomj',
    'VERSION'       =>  '1.1',
    'GRADE'         =>  1,
    'START_DATE'    =>  '2020',
    'FINALLY_DATE'  =>  '2020',
    'HOME'          =>  'http://wuliaomj.com',
    'API_GRADE'     =>  1
);
config_examine('CONFIG_INFO');

/* PROJECT
 * NAME 项目名称 String 任何内容
 * COPYRIGHT 版权格式 String 允许使用${DATES}日期,${HOME}主页,${OWNER}拥有者来代替上面的配置
*/
$CONFIG_PROJECT=array(
    'NAME'          =>  'AdminService',
    'COPYRIGHT'     =>  config_load('COPYRIGHT','Copyright ${DATES} <a href="${HOME}">${OWNER}</a>. All rights reserved.')
);
config_examine('CONFIG_PROJECT');

?>