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
 * 这里主要是框架和项目的信息,请勿随意修改
 * 
 * 框架
 * AUTHOR 框架开发作者 String 框架开发作者,不允许修改,请固定填写为Boringmj
 * API_GRADE 框架API标号 Int 该项请勿随意修改,用于项目,插件兼容性判断
 * 项目
 * OWNER 项目开发者 String 项目开发者/所有者
 * VERSION 项目版本 String 项目版本
 * GRADE 版本标号 Int 指的是项目的版本号,用于项目实际版本判断
 * START_DATE 框架版权起始日期 String 框架版权起始年份
 * FINALLY_DATE 框架版权结束日期 String 允许填写为auto(),框架版权结束年份
 * HOME 项目主页地址 String 项目主页地址,项目开发者/开发团队,运营主页地址
 */
$CONFIG_INFO=array(
    'AUTHOR'        =>  'Boringmj',
    'OWNER'         =>  'Boringmj',
    'VERSION'       =>  '1.1',
    'GRADE'         =>  1,
    'START_DATE'    =>  '2020',
    'FINALLY_DATE'  =>  config_auto('INFO_FINALLY_DATE'),
    'HOME'          =>  'http://wuliaomj.com',
    'API_GRADE'     =>  1
);
config_examine('CONFIG_INFO');

/* PROJECT
 * NAME 项目名称 String 任何内容
 * OPERATOR 公开运营署名 String 可以是开发者名称,也可以是公司名称,也可以是项目名称(该项用于项目各种公开署名)
 * COPYRIGHT 版权格式 String 允许使用${DATES}日期,${HOME}主页,${OWNER}拥有者来代替上面的配置
 * 
 * 注意使用变量需要使用 config_load() 函数来加载,否则将视为静态内容
 */
$CONFIG_PROJECT=array(
    'NAME'          =>  'AdminService',
    'OPERATOR'      =>  'AdminService',
    'COPYRIGHT'     =>  config_load('PROJECT_COPYRIGHT','Copyright ${DATES} <a href="${HOME}">${OWNER}</a>. All rights reserved.')
);
config_examine('CONFIG_PROJECT');

?>