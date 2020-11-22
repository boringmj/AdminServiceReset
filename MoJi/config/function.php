<?php

function config_auto($name=null)
{
    if($name==='HTTP_TYPE')
    {
        if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')
            return 'https';
        elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&$_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
            return 'https';
        else
            return 'http';
    }
    if($name==='HTTP_PATH')
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
            return $_SERVER['HTTP_X_FORWARDED_HOST'];
        elseif(isset($_SERVER['HTTP_HOST']))
            return $_SERVER['HTTP_HOST'];
        else
            return '';
    }
    return $name;
}

function config_load($name=null,$content='')
{
    if($name==='COPYRIGHT')
    {
        $content_array=array(
            'DATES'=>CONFIG_INFO_START_DATE===CONFIG_INFO_FINALLY_DATE?CONFIG_INFO_START_DATE:CONFIG_INFO_START_DATE.'-'.CONFIG_INFO_FINALLY_DATE,
            'OWNER'=>CONFIG_INFO_OWNER==''?CONFIG_INFO_AUTHOR:CONFIG_INFO_OWNER,
            'HOME'=>CONFIG_INFO_HOME
        );
        return variable_load($content_array,$content);
    }
    return $content;
}

?>