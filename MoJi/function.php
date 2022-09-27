<?php

/**
 * 变量与内容自动绑定
 * 
 * @param array $variable_array 变量数组
 * @param string $content 内容
 * @return string
 */
function variable_load($variable_array,$content)
{
    foreach($variable_array as $variable=>$variable_value)
        $content=preg_replace("/\\\$\{{$variable}\}/",$variable_value,$content);
    return $content;
}

/**
 * 预处理变量(我也忘记是干什么的了)
 * 
 * @param string $content 内容
 * @return string
 */
function preg_variable_load($content)
{
    $variable_array=array(
        '\\'=>'\\\\',
        '/'=>'\\/',
        '.'=>'\\.'
    );
    foreach($variable_array as $variable=>$variable_value)
        $content=str_replace($variable,$variable_value,$content);
    return $content;
}

/**
 * 获取随机字符串
 * 
 * @param int $length 长度
 * @param string $chars 字符串池(默认为数字字母)
 * @return string
 */
function get_rand_string($len,$chars=null)
{
    if(is_null($chars))
        $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i=0,$str='',$lc=strlen($chars)-1;$i<$len;$i++)
        $str.=$chars[mt_rand(0,$lc)];
    return $str;
}

/**
 * 获取随机36位UID(如果无 uuid_create 支持则自动使用 get_rand_string)
 * 
 * @param int $type 类型(uuid_create 函数参数)
 * @return string
 */
function get_rand_string_id($type=0)
{
    if(function_exists('uuid_create'))
    {
        return uuid_create($type);
    }
    else
    {
        $char="1234567890abcdef";
        $str=get_rand_string(8,$char)."-".get_rand_string(4,$char)."-".get_rand_string(4,$char)."-".get_rand_string(4,$char)."-".get_rand_string(12,$char);
        return strtolower($str);
    }
}

?>