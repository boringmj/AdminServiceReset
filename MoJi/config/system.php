<?php

/* 关于配置文件使用帮助
 * 首先需要说一下自动填充功能
 * config_auto( [$name] );
 * 允许填写auto()的为支持系统动态获取信息,无须用户手动填写和更改
 * $name 参数为缺省参数,但在实际使用中请填写具体配置名称,详见默认示例
 * 如果无特殊说明,请遵循详细配置要求,并在检查无误后进行程序的安装
 * 系统安装后,请尽量不要修改配置文件,修改配置文件造成的错误自行负责
*/

/* REQUEST
 * HTTP_TYPE WEB请求类型 String 允许填写为auto(),http,https
 * HTTP_PATH WEB请求地址 String 允许填写为auto(),地址带端口
*/
$CONFIG_REQUEST=array(
    'HTTP_TYPE'=>config_auto('HTTP_TYPE'),
    'HTTP_PATH'=>config_auto('HTTP_PATH')
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