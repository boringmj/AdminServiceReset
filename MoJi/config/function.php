<?php

function config_auto($name=null)
{
    switch($name)
    {

        case 'HTTP_TYPE':
            if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')
                return 'https';
            elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&$_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
                return 'https';
            else
                return 'http';
        case 'HTTP_HOST':
            if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
                return $_SERVER['HTTP_X_FORWARDED_HOST'];
            elseif(isset($_SERVER['HTTP_HOST']))
                return $_SERVER['HTTP_HOST'];
            elseif(isset($_SERVER['SERVER_ADDR']))
                return $_SERVER['SERVER_ADDR'];
            else
                return '';
        case 'HTTP_PORT':
            if(isset($_SERVER['SERVER_PORT']))
                return $_SERVER['SERVER_PORT'];
            else
                return 80;
        case 'HTTP_PATH':
                return isset($_SERVER['DOCUMENT_ROOT'])?$_SERVER['DOCUMENT_ROOT']:'';
        case 'LOG_DIR':
            return APPLICATION_PATH.'/Log';
        case 'LOG_LEVEL':
                return 0;
        case 'REQUEST_URL':
            $port='';
            if(CONFIG_HTTP_PORT!=80&&CONFIG_HTTP_PORT!=443)
                $port=':'.CONFIG_HTTP_PORT;
            $web_path=preg_replace('/'.preg_variable_load(CONFIG_HTTP_PATH).'/','',isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'');
            $web_path=dirname($web_path);
            if($web_path==='/'||$web_path==='\\')
                $web_path='';
            return CONFIG_HTTP_TYPE.'://'.CONFIG_HTTP_HOST.$port.$web_path;
        case 'REQUEST_ERROR_LEVEL':
            return 1;
        case 'REQUEST_ERROR_FROM':
            $port='';
            if(CONFIG_HTTP_PORT!=80&&CONFIG_HTTP_PORT!=443)
                $port=':'.CONFIG_HTTP_PORT;
            $web_path=preg_replace('/'.preg_variable_load(CONFIG_HTTP_PATH).'/','',isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'');
            $web_path=dirname($web_path);
            if($web_path==='/'||$web_path==='\\')
                $web_path='';
            return CONFIG_HTTP_TYPE.'://'.CONFIG_HTTP_HOST.$port.$web_path.'/?from=error&info=ERROR_FROM';
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