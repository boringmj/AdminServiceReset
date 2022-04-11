<?php

/* SECURITY(AdminService)
 * USER_TOKEN_TIME 用户Token有效时间 Int 单位秒
 * USER_TOKEN_TIME_RESTE_TIME 用户Token是否允许重置有效时长 Boolean 允许增加时长后,Token有效时长将会在特定接口中重置时长
 * USER_TOKEN_TIME_BIND_IP_GRADE 用户Token绑定IP等级 Int 0:不绑定(无限制),1:绑定请求IP,2:绑定请求IP和代理IP
 * USER_ALLOWMULTIPLE_TOKEN 是否允许用户使用多个Token Boolean 是否允许允许多个Token同时登录
 *
 * 注意: 本配置文件为AdminService独有配置
 *
 * 关于 SECURITY 文件
 * 该文件是AdminService提供的额外可控的配置文件,主要为不同业务提供不同的安全等级配置
 * 该文件涉及到程序安全,如不了解相关配置影响范围,不推荐随意修改
 * 
 * 计划
 * 1. 限制同一个IP地址某个时段内登录错误次数
 * 2. 限制同一个IP地址用户注册上限(防止恶意注册)
 */
$CONFIG_SECURITY=array(
    'USER_TOKEN_TIME'=>60*60*24*30,
    'USER_TOKEN_TIME_RESTE_TIME'=>true,
    'USER_TOKEN_TIME_BIND_IP_GRADE'=>2,
    'USER_ALLOW_MULTIPLE_TOKEN'=>false
);
config_examine('CONFIG_SECURITY');

?>