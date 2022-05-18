<?php

/* 关于
 * 本项目采用自写框架,该框架暂未独立开源,均与项目同时发布,且随时可能更新
 * 项目版权由boringmj(别名wuliaomj),邮箱wuliaodemoji@wuliaomj.com所有
 * 本项目要求框架开启数据库支持(DATABASE_ENABLE 为 true)
 * 
 * 帮助
 * APPLICATION_DEBUG Boolean 常量决定是否开启调试模式,默认关闭
 * APPLICATION_PATH String 常量决定程序路径(非框架路径)(不可缺省)
 * DATABASE_ENABLE Boolean 常量决定是否启用数据库(仅支持Mysql且需要PDO支持,还需要自行配置配置文件),默认关闭
 * DEFAULT_LANGUAGE String 常量决定默认语言(需要本框架支持该语言才行),默认语言为 zh-cn(简体中文)
 * 
 * 注意:
 * 以上所有常量名称均为大写,且值大小写敏感
 * 所有 布尔型(Boolean) 常量允许使用一个合法的布尔表达式,如: true,false,1,0等
 * 默认语言必须有对应的语言包支持,否则将会出现错误(该错误不会被捕获,请自行处理)
 * 除 APPLICATION_PATH 该项不可缺省外,其余项均可缺省
 * 请确保项目所有文件最低可读权限,部分文件可能需要写权限
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
 * 使用 api 和 web 的接口都应将 type 参数设置为post方式提交到服务器(post提交该项需要参与签名),以防止请求类型被篡改
 * 使用 api 和 web 类型的请求应该将 from 和 class 使用post方式提交到服务器(post提交该项需要参与签名)
 * api 和 web 类型请求的 app_id 应该分开使用,且严格限制 web 类型请求的 app_id 权限不可访问 api 类型的请求
 * 以上三条并非强制要求,这里仅仅提出一个安全警告
 * 自动安装反馈到web端的 app_id 和 app_key 拥有所有权限,请勿用于生产环境中(高权限可能会导致安全隐患)
 * 高权限的 app_id 和 app_key 不推荐用于开放源代码的情况,如web的js脚本中使用
 * 
 * 免责声明
 * 因使用本项目和框架而产生的任何纠纷,均与作者、贡献者无关,本项目和框架作者、贡献者不承担任何责任
 * 因使用本项目和框架导致的任何损失,本项目和框架作者、贡献者不承担任何责任
 * 
 * 
 * 
 * 常见问题
 * 
 * 1. 为什么没文档?
 * 答: 其实是有的,不过因为太烂不好意思拿出来
 * 
 * 2. 为什么会提示目录或文件不可写?
 * 答: 请检查提示的目录或文件是否存在且有读写权限,直接给予 AdminService 的目录可写权限将会自动创建目录
 *     不过并不推荐直接赋予 AdminService 可写权限,这将会导致新的安全隐患,推荐逐一补全并检查目录或文件的读写权限
 * 
 * 3. 为什么提示数据库连接失败?
 * 答: 这是仅存在开启数据库的情况下的错误,请您手动检查 MoJi/config/database.config.php 配置是否正确
 * 
 * 4. 我关闭数据库后为什么会报错? ( Uncaught Error: Call to a member function GetTablename() on null )
 * 答: 这是因为部分插件或程序违规使用了数据库,我们并不能提供额外的帮助,您可以选择开启数据库以解决该类问题
 *     com.verification.api 插件与 AdminService 项目均要求开启数据库且均未检查该类问题
 * 
 * 5. 为什么提示缺少权限节点?
 * 答: 这是因为您访问的地址是默认无权限或您使用的 app_id 错误导致的,该项较为复杂
 *     详细参考 MoJi/module/Permission.module.php 中的说明
 * 
 * 6. 我在安装时没有打开数据库,但因为程序依赖数据库,我该怎么办?
 * 答: 目前是没办法的,您可以删除 AdminService/Data/install.data.json 文件,并尝试重新安装
 *     您也可以通过在命令行使用 “php index.php uninstall” 命令来卸载安装
 * 
 * 7. 我想将整个项目移动到一个新的服务器,我该怎么做?
 * 答: 您只需要保证所有配置文件均正确,并且您手动把所有文件和数据移动到新的服务器即可
 *     配置文件中某些配置项是自动计算的,如果您并未修改过 config_auto() 的配置项,理论上您只需要保证数据库配置正确即可
 * 
 * 8. 我想管理我的插件,我该怎么做?
 * 答: 目前还在开发一个可靠的管理器,所以您只能手动对插件进行管理,插件在 MoJi/plugin 目录下
 *     您也可以通过在命令行使用 “php index.php plugin” 命令来查看插件列表
 *     插件数据在 AdminService/Data/plugin 目录下,插件自动开启的实现请参考 com.verification.api 的 Init() 方法
 * 
 * 9. 我在哪里可以找到更多的帮助?
 * 答: 向 wuliaodemoji@wuliaomj.com 发送您的问题,您可能会得到帮助(这是不确定的,作者并没有义务回复您)
 *     请您仔细描述您遇到的问题,且将邮件主题命名为 “[AdminService] 我需要额外的帮助” 
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