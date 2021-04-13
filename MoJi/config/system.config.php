<?php

/* HTTP
 * TYPE WEB请求类型 String 允许填写为auto(),http,https
 * HOST WEB请求地址 String 允许填写为auto(),地址带端口
 * CODE 编码类型 String 默认为utf-8
 * PATH WEB根目录  String 一个合法的web根目录
 */
$CONFIG_HTTP=array(
    'TYPE'      =>  config_auto('HTTP_TYPE'),
    'HOST'      =>  config_auto('HTTP_HOST'),
    'CODE'      =>  'UTF-8',
    'PORT'      =>  config_auto('HTTP_PORT'),
    'PATH'      =>  config_auto('HTTP_PATH')
);
config_examine('CONFIG_HTTP');

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