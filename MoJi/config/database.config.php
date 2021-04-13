<?php

/* DATABASE
 * PREFIX 数据表前缀 String 符合数据表命名规范即可
 * HOST 连接地址 String 连接的数据库地址
 * USER 用户名 String 登录用户名
 * PASSWORD 用户密码 String 登录用户密码
 * DATABASE 数据库名称 String 连接的数据库名称
 * 
 * 注意:目前仅支持Mysql数据库
 */
$CONFIG_DATABASE=array(
    'PREFIX'    =>  'Admin_Service_',
    'HOST'      =>  'localhost',
    'USER'      =>  '',
    'PASSWORD'  =>  '',
    'DATABASE'  =>  ''
);
config_examine('CONFIG_DATABASE');

?>