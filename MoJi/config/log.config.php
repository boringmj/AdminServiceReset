<?php

/* LOG
 * STATUS 日志功能是否开启 Boolean (Boolean)(Int)
 * DIR 日志存放目录 String 允许填写为auto(),可写目录
 * FORMAT 日志格式 String 允许填写为任意内容,${title} 事件标题,${msg} 事件内容,${grade} 事件标识,${file} 文件位置,${date} 日期,${id} 请求id
 * FORMAT_DATE 日志日期格式 String 遵循PHP原生date()写法
 * PATH 日志路径 String 仅需提供文件名即可
 * DEBUG_PATH 调试日志路径 String 仅需要提供文件名即可
 * FILE_SIZE 日志文件大小 Int 非严格大小,1048576为1MB,填写文件字节大小(调试文件不受到限制)
 * LEVEL 保留的最低日志级别 INT 详见下面的日志等级介绍
 * 
 * 注意:日志不会自动删除,用户需要定期自行清理
 * 日志等级: 0为不限制,等级越高信息越重要,等级低的包含等级高的,例如1包含了10以及更高的所有项,目前可选范围为1(提示),5(一次性提示),10(警告),15(非致命性错误),20(错误)
 * 已知不足: 部分日志是使用 用户语言(LANGUAGE_NAME) 进行写入,所以部分日志内容会根据 用户语言(LANGUAGE_NAME) 变化而变化
 */
$CONFIG_LOG=array(
    'STATUS'        =>  true,
    'DIR'           =>  config_auto('LOG_DIR'),
    'FORMAT'        =>  '[ ${date} ] - ${title} | ${msg} - In ${file} #${id}(${grade})'."\r\n",
    'FORMAT_DATE'   =>  'Y-m-d H:i:s',
    'PATH'          =>  'wwwlog.log',
    'DEBUG_PATH'    =>  'debug.log',
    'FILE_SIZE'     =>  104857600,
    'LEVEL'         =>  0
);
config_examine('CONFIG_LOG');

?>