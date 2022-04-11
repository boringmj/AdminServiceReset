<?php

/* EMAIL(AdminService)
 * SMTP_HOST SMTP主机地址 String 一个合法的主机地址(请联系您的邮箱服务商查询)
 * SMTP_PORT SMTP端口 Int 一个合法的端口(请联系您的邮箱服务商查询)并确保端口已开放
 * SMTP_USER SMTP用户名 String 一个合法的用户名(一般为邮箱地址)
 * SMTP_PASS SMTP密码 String 一个合法的密码(一般为邮箱密码,也可能是是邮箱授权码)
 * FROM_EMAIL 发件人邮箱 String 一个合法的邮箱地址(一般同 SMTP_USER 选项)
 * FROM_NAME 发件人昵称 String 允许填写为auto(),一个合法的昵称(一般为邮箱地址的昵称)
 * SSL 是否使用SSL Boolean 该项决定是否使用SSL连接,如果检测到端口465(SMTP_PORT = 465)且使用qq邮箱或腾讯企业邮箱,则自动开启SSL
 * 
 * 注意: 本配置文件为AdminService独有配置
 * QQ邮箱SMTP_HOST : smtp.qq.com
 * 腾讯企业邮箱SMTP_HOST : smtp.exmail.qq.com
 * 网易163邮箱SMTP_HOST : smtp.163.com
 * 常用端口: 25, 465, 587
 * 个人推荐使用腾讯企业邮箱,且使用465端口发送邮件
 * 
 * 安全警告:任何插件都能读取到该配置文件,请勿使用常规密码或随意导入插件
 */

$CONFIG_EMAIL=array(
    'SMTP_HOST'     =>  'smtp.exmail.qq.com',
    'SMTP_PORT'     =>  465,
    'SMTP_USER'     =>  '',
    'SMTP_PASS'     =>  '',
    'FROM_EMAIL'    =>  '',
    'FROM_NAME'     =>  config_auto('EMAIL_FROM_NAME'),
    'SSL'           =>  true,
);
config_examine('CONFIG_EMAIL');

?>