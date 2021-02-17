<?php

/* 关于
 * 本项目采用自写框架,该框架暂未独立开源,均与项目同时发布,且随时可能更新
 * 项目版权由boringmj(别名wuliaomj),邮箱wuliaodemoji@wuliaomj.com所有
 * 本项目要求框架开启数据库支持(DATABASE_ENABLE 为 true)
 * 
 * 帮助
 * APPLICATION_DEBUG 常量决定是否开启调试模式
 * APPLICATION_PATH 常量决定程序路径(非框架路径)(不可缺省)
 * DATABASE_ENABLE 常量决定是否启用数据库(仅支持Mysql且需要PDO支持,还需要自行配置配置文件)
 * DEFAULT_LANGUAGE 常量决定默认语言(需要本框架支持该语言才行,默认语言为zh-cn)
 * 
 * 调试模式
 * 调试模式比正常模式来说更加浪费读写资源
 * 调试模式在错误显示上更加具体,并且开放PHP的错误显示(php.ini禁止了错误显示调试模式开启与关闭均无法显示)
*/

$APP_PATH=dirname(__FILE__).'/AdminService';

define('APPLICATION_DEBUG',true);
define('APPLICATION_PATH',$APP_PATH);
define('DATABASE_ENABLE',true);
define('DEFAULT_LANGUAGE','zh-cn');
unset($APP_PATH);

require dirname(__FILE__).'/MoJi/Main.php';

?>