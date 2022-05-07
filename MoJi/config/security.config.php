<?php

/* SECURITY(AdminService)
 * USER_TOKEN_TIME 用户Token有效时间 Int 单位秒(修改后立即生效且仅影响后续请求,不影响现有Token的有效期)
 * USER_TOKEN_TIME_RESET_TIME 用户Token是否允许重置有效时长 Boolean 允许增加时长后,Token有效时长将会在特定接口中重置时长(修改后立即生效)
 * USER_TOKEN_TIME_BIND_IP_GRADE 用户Token绑定IP等级 Int 0:不绑定(无限制),1:绑定请求IP,2:绑定请求IP和代理IP(修改后立即生效且现有ukey将会失效)
 * USER_ALLOWMULTIPLE_TOKEN 是否允许用户使用多个Token Boolean 是否允许允许多个Token同时登录(修改后立即生效且仅影响后续请求,不影响现有Token)
 * IP_LOGIN_THRESHOLD 某个时间段内某个IP登录次数阈值 Int 登录次数阈值(0为不限制)
 * IP_LOGIN_TIME 登录时间段 Int 只有上一次请求在这个时间段里面才会计算登录次数
 * IP_LOGIN_ERROR_THRESHOLD 某个时间段内某个IP登录错误次数阈值 Int 登录错误次数阈值(0为不限制)
 * IP_LOGIN_ERROR_TIME 登录错误时间段 Int 只有上一次请求在这个时间段里面才会计算登录错误次数
 * IP_REGISTER_THRESHOLD 某个时间段内某个IP注册次数阈值 Int 注册次数阈值(0为不限制)
 * IP_REGISTER_TIME 注册时间段 Int 只有上一次请求在这个时间段里面才会计算注册次数
 * IP_LOGIN_TOTAL_DAY 某个IP在一天内登录次数阈值 Int 某个IP每天允许登录的次数(0为不限制且该项并不会使IP被封禁)
 * IP_BAN_TIME 某个IP在超过阈值时被封禁时间 Int 在这个时间段内,IP将被禁止
 *
 * 注意: 本配置文件为AdminService独有配置
 *       想要使Token相关配置立即生效,可以删除数据库中有关Token的所有数据,这会迫使用户重新登录从而使配置立即生效
 *       IP记录登录次数和注册次数并不是访问就纪录的,而是请求合法才会记录
 *
 *
 * 关于 SECURITY 文件
 * 该文件是AdminService提供的额外可控的配置文件,主要为不同业务提供不同的安全等级配置
 * 该文件涉及到程序安全,如不了解相关配置影响范围,不推荐随意修改
 */
$CONFIG_SECURITY=array(
    'USER_TOKEN_TIME'               =>  60*60*24*7,
    'USER_TOKEN_TIME_RESET_TIME'    =>  true,
    'USER_TOKEN_TIME_BIND_IP_GRADE' =>  2,
    'USER_ALLOW_MULTIPLE_TOKEN'     =>  false,
    'IP_LOGIN_THRESHOLD'            =>  20,
    'IP_LOGIN_TIME'                 =>  60*60*1,
    'IP_LOGIN_ERROR_THRESHOLD'      =>  10,
    'IP_LOGIN_ERROR_TIME'           =>  60*3,
    'IP_REGISTER_THRESHOLD'         =>  3,
    'IP_REGISTER_TIME'              =>  60*60*1,
    'IP_LOGIN_TOTAL_DAY'            =>  50,
    'IP_BAN_TIME'                   =>  60*60*3
);
config_examine('CONFIG_SECURITY');

?>