<?php

/* USER(AdminServer)
 * HEAD_PORTRAIT 用户默认头像 String 允许填写为auto(),一个合法的URL
 * DEFAULT_GROUP_NAME 默认组名称 String 任何内容
 * DEFAULT_GROUP_LEVEL 默认组权限等级 Int 推荐为1(普通用户权限)
 * USER_DEFAULT_STATUS 默认用户状态 Int 推荐为2,用户状态: 1(正常)、2(待激活)、3(封禁)
 * USER_DEFAULT_GROUP_ID 默认用户组 Int 推荐为1(默认用户组)
 * SALT 盐 String 长度推荐大于等于16个字符,允许定义为任何字符,该项涉及安全如不了解请勿修改
 * 
 * 注意: 本配置文件为AdminServer独有配置
*/

$CONFIG_USER=array(
    'HEAD_PORTRAIT'         =>  config_auto('USER_HEAD_PORTRAIT'),
    'DEFAULT_GROUP_NAME'    =>  '普通用户',
    'DEFAULT_GROUP_LEVEL'   =>  1,
    'USER_DEFAULT_STATUS'   =>  2,
    'USER_DEFAULT_GROUP_ID' =>  1,
    'SALT'                  =>  'B$8FOWKpyNd&ym7dvt@BnBMlIkUic#!V'
);
config_examine('CONFIG_USER');

?>