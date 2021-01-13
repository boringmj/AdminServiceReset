<?php

/* 语言模块
 * 翻译来源于谷歌翻译
 * 如果您想要提供更多的语言支持可以向wuliaodemoji@wuliaomj.com发送邮件并且注明语言
 * 如果您的语言模块审核通过,我们将会匿名新增到新的版本中作为可选语言支持
 * 语言模块仅允许使用字符串(String)作为值
 *
 * Language module
 * Translation comes from Google Translate
 * If you want to provide more language support, you can send an email to wuliaodemoji@wuliaomj.com and indicate the language
 * If your language module is approved, we will add it to the new version anonymously as optional language support
 * The language module only allows to use string(String) as a value
*/

//简体中文
$LANGUAGE=array(
    'NAME'=>'简体中文',
    'WRITE_PATH_DEBUG'=>'目录或文件不可写',
    'WRITE_PATH'=>'如果您不是管理员:<br>出现致命错误,请联系管理员处理<br><br>如果您是管理员:<br>您可以打开调试来查看具体问题',
    'REQUEST_SITES'=>'站点被请求',
    'REQUEST_ADDR'=>'请求地址',
    'REQUEST_IP'=>'请求IP',
    'REQUEST_NAME'=>'请求模块',
    'REQUEST_ERROR'=>'请求错误',
    'REQUEST_ILLEGAL'=>'非法请求',
    'REQUEST_ERROR_TITLE'=>'Unexpected request',
    'REQUEST_ERROR_MSG'=>'Your request was terminated unexpectedly: please check if the link is correct',
    'REQUEST_SUCCESS'=>'请求模块启动成功',
    'AGENT_IP'=>'代理IP',
    'DATABASE_ERROR_DEBUG'=>'数据库连接失败,请检查配置是否正确,具体信息已写入日志',
    'DATABASE_ERROR'=>'如果您不是管理员:<br>出现致命错误,请联系管理员处理<br><br>如果您是管理员:<br>您可以查看日志或打开调试(不推荐)来查看具体问题',
    'DATABASE_NAME'=>'数据库模块',
    'DATABASE_ERROR_TITLE'=>'数据库连接失败',
    'DATABASE_SUCCESS'=>'数据库模块启动成功',
    'EXTENSION_UNSPPORTED'=>'扩展缺失',
    'EXTENSION_UNSPPORTED_MSG'=>'扩展不支持,请您下载相关扩展后重新尝试',
    'EXTENSION_UNSPPORTED_WARN'=>'扩展不支持,但已被忽视,系统随时可能因为此问题终止正常的运行的服务',
    'INSTALL_DATA_ERROR'=>'安装信息不是一个合法的json',
    'INSTALL_DATA_PATH_ERROR'=>'文件不可写或不是文件类型',
    'INSTALL_NOT_INSTALL'=>'您尚未完成安装,请点击 ',
    'INSTALL_NOT_UPDATE'=>'检测到更新,请点击 ',
    'INSTALL_NOT_INSTALL_CLICK'=>'初始安装',
    'INSTALL_NOT_UPDATE_CLICK'=>'安装扩展更新'
);
config_examine('LANGUAGE');

?>