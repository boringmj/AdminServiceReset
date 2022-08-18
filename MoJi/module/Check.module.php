<?php

/**
 * 扩展需求
 * 
 * PDO 如果不启用数据库为非必要项,但本项目要求您必须启动数据库
 * UUID 非必要项,如不支持则不能保证数据的唯一性
 * OpenSSL 目前非必须,在将来版本中随时可能会启用
 */

$unsupported_extension=array();
if(!class_exists('PDO'))
    if(DATABASE_ENABLE)
        array_push($unsupported_extension,'PDO');
    else
        write_log(LANGUAGE_LOG_EXTENSION_UNSPPORTED,'PDO '.LANGUAGE_LOG_EXTENSION_UNSPPORTED_WARN,__FILE__,10);
if(!function_exists('uuid_create'))
    write_log(LANGUAGE_LOG_EXTENSION_UNSPPORTED,'UUID '.LANGUAGE_LOG_EXTENSION_UNSPPORTED_WARN,__FILE__,10);
if(!class_exists('OpenSSLAsymmetricKey'))
    write_log(LANGUAGE_LOG_EXTENSION_UNSPPORTED,'OpenSSL '.LANGUAGE_LOG_EXTENSION_UNSPPORTED_WARN,__FILE__,10);
if(!empty($unsupported_extension))
{
    if(APPLICATION_DEBUG)
    {
        foreach($unsupported_extension as $name)
        {
            write_log(LANGUAGE_LOG_EXTENSION_UNSPPORTED,"{$name} ".LANGUAGE_LOG_EXTENSION_UNSPPORTED_MSG,__FILE__,20);
            echo $name.' '.LANGUAGE_LOG_EXTENSION_UNSPPORTED_MSG.'<br>';
        }
    }
    exit(LANGUAGE_LOG_EXTENSION_UNSPPORTED);
}
unset($unsupported_extension);

?>