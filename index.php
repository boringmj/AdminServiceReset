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
 * 
 * 安全警告
 * 任何插件都有等同于当前运行环境的用户权限,且插件导入插件目录后均会执行一次初始化操作,请勿随意导入来源不明的插件
 * 所有由插件导致的问题均与本项目和框架作者、贡献者无关,需用户自行承担责任
 * 本项目和框架能通过HTTP访问到的文件应且只有 index.php 文件
 * 其他所有目录和文件均应屏蔽HTTP访问,以免造成安全隐患
 * 
 * 免责声明
 * 本项目和框架仅供学习交流,未获得许可均不得用于商业用途,否则本项目和框架作者有权追究其法律责任
 * 因使用本项目和框架而产生的任何纠纷,均与作者、贡献者无关,本项目和框架作者、贡献者不承担任何责任
 * 因使用本项目和框架导致的其他任何损失,本项目和框架作者、贡献者不承担任何责任
 */

$APP_PATH=__DIR__.'/AdminService';

define('APPLICATION_DEBUG',true);
define('APPLICATION_PATH',$APP_PATH);
define('DATABASE_ENABLE',true);
define('DEFAULT_LANGUAGE','zh-cn');
unset($APP_PATH);

//兼容命令行(请保留以下内容)
if(isset($argv))
    $GLOBALS["argv"]=&$argv;
require __DIR__.'/MoJi/Main.php';

?>