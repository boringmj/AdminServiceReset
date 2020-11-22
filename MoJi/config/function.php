<?php

function config_auto($name=null)
{
    switch($name)
    {

        case 'REQUEST_HTTP_TYPE':
            if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')
                return 'https';
            elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&$_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
                return 'https';
            else
                return 'http';
        case 'REQUEST_HTTP_PATH':
            if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
                return $_SERVER['HTTP_X_FORWARDED_HOST'];
            elseif(isset($_SERVER['HTTP_HOST']))
                return $_SERVER['HTTP_HOST'];
            elseif(isset($_SERVER['SERVER_ADDR']))
                return $_SERVER['SERVER_ADDR'];
            else
                return '';
        case 'LOG_DIR':
            return APPLICATION_PATH.'/Log';
        default:
        return $name;
    }
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