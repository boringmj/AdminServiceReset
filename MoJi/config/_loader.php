<?php

/* 关于配置文件使用帮助
 * 首先需要说一下自动填充功能
 * config_auto( [$name] );
 * 允许填写auto()的为支持系统动态获取信息,无须用户手动填写和更改
 * $name 参数为缺省参数,但在实际使用中请填写具体配置名称,详见默认示例
 * 如果无特殊说明,请遵循详细配置要求,并在检查无误后进行程序的安装
 * 系统安装后,请尽量不要修改配置文件,修改配置文件造成的错误自行负责
 * 本配置文件遵循php原生语法
 */

//配置文件处理核心
require __DIR__.'/function.php';
require __DIR__.'/examine.php';

//框架配置
require __DIR__.'/system.config.php';
require __DIR__.'/log.config.php';
require __DIR__.'/database.config.php';
require __DIR__.'/request.config.php';
require __DIR__.'/key.config.php';

//项目配置(AdminService)
require __DIR__.'/user.config.php';
require __DIR__.'/email.config.php';
require __DIR__.'/security.config.php';

?>