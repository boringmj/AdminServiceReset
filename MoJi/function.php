<?php

function variable_load($variable_array,$content)
{
    foreach($variable_array as $variable=>$variable_value)
        $content=preg_replace("/\\\$\{{$variable}\}/",$variable_value,$content);
    return $content;
}

function get_rand_string($len,$chars=null)
{
    if(is_null($chars))
    {
        $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i=0,$str='',$lc=strlen($chars)-1;$i<$len;$i++)
    {
        $str.=$chars[mt_rand(0,$lc)];
    }
    return $str;
}

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