<?php

/* USER(AdminService)
 * NAME_RULE 用户名规则 String 一个合法的正则表达式(请注意,该项数据库最高存储长度为32个字符串)
 * PASSWORD_RULE 密码规则 String 一个合法的正则表达式
 * NICKNAME_RULE 昵称规则 String 一个合法的正则表达式(请注意,该项数据库最高存储32个字符串)
 * EMAIL_RULE 邮箱规则 String 一个合法的正则表达式(请注意,该项数据库最高存储64个字符串)
 * HEAD_PORTRAIT 用户默认头像 String 默认请使用 default 一个合法的图片id(请注意,该项数据库最高存储255个字符串)
 * DEFAULT_GROUP_NAME 默认组名称 String 任何内容(请注意,该项数据库最高存储32个字符串)
 * DEFAULT_GROUP_LEVEL 默认组权限等级 Int 推荐为1(普通用户权限)
 * USER_DEFAULT_STATUS 默认用户状态 Int 推荐为2,用户状态: 1(正常)、2(待激活)、3(封禁)
 * USER_DEFAULT_GROUP_ID 默认用户组 Int 推荐为1(默认用户组)
 * SALT 盐 String 长度推荐大于等于16个字符,允许定义为任何字符,该项涉及安全如不了解请勿修改
 * VERIFY_OVERDUE_TIME 验证过期时间 Int 推荐为3600(一小时),单位秒(该项修改后立即生效),过期未激活用户将被自动删除
 * 
 * 注意: 本配置文件为AdminService独有配置
 * 正则表达式需要使用 "/"开头和结尾,例如: '/^[a-zA-Z0-9_]{6,16}$/'
 * 中文字符请使用 \x{4e00}-\x{9fa5} 表示,这条规则可以在某些情况下自动转换为 \u4e00-\u9fa5
 * 
 * 默认规则:
 * 用户名: /^[a-zA-Z0-9_]{6,32}$/ 使用字母数字下划线任意组合的6-32个字符
 * 密码: /^.{6,36}$/ 使用任意字符的6-36个字符
 * 昵称: /^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{2,16}$/u 使用字母数字下划线中文任意组合的2-16个字符
 * 邮箱: /^(?=.{6,64}$)[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+)+$/ 6-64个字符,匹配邮箱格式(不支持中文邮箱)
 * 
 * 安全警告: SALT 该项涉及安全如不了解请勿随意修改
 */
$CONFIG_USER=array(
    'NAME_RULE'             =>  '/^[a-zA-Z0-9_]{6,32}$/',
    'PASSWORD_RULE'         =>  '/^.{6,36}$/',
    'NICKNAME_RULE'         =>  '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{2,16}$/u',
    'EMAIL_RULE'            =>  '/^(?=.{6,64}$)[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+)+$/',
    'HEAD_PORTRAIT'         =>  'default',
    'DEFAULT_GROUP_NAME'    =>  '普通用户',
    'DEFAULT_GROUP_LEVEL'   =>  1,
    'USER_DEFAULT_STATUS'   =>  2,
    'USER_DEFAULT_GROUP_ID' =>  1,
    'SALT'                  =>  'B$8FOWKpyNd&ym7dvt@BnBMlIkUic#!V',
    'VERIFY_OVERDUE_TIME'   =>  3600
);
config_examine('CONFIG_USER');

?>