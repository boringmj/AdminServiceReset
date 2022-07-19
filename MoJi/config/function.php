<?php

/**
 * 自动填充配置项(失败返回配置项名称)
 * 
 * @param string $name 完整的配置项名称
 * @return mixed
 */
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
            // 本代码已废弃,后续将不再信任代理服务器,也不再支持自动识别出代理服务器,如业务需要,请自行配置
            // if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
            //     return $_SERVER['HTTP_X_FORWARDED_HOST'];
            if(isset($_SERVER['HTTP_HOST']))
                return $_SERVER['HTTP_HOST'];
            elseif(isset($_SERVER['SERVER_NAME']))
                return $_SERVER['SERVER_NAME'];
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
        case 'REQUEST_URL':
            $web_path=preg_replace('/'.preg_variable_load(CONFIG_HTTP_PATH).'/','',isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'');
            $web_path=dirname($web_path);
            if($web_path==='/'||$web_path==='\\'||$web_path==='.')
                $web_path='';
            return CONFIG_HTTP_TYPE.'://'.CONFIG_HTTP_HOST.$web_path;
        case 'REQUEST_ERROR_FROM':
            $web_path=preg_replace('/'.preg_variable_load(CONFIG_HTTP_PATH).'/','',isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'');
            $web_path=dirname($web_path);
            if($web_path==='/'||$web_path==='\\'||$web_path==='.')
                $web_path='';
            return CONFIG_HTTP_TYPE.'://'.CONFIG_HTTP_HOST.$web_path.'/?from=error&info=ERROR_FROM'.(CURRENT_LANGUAGE!=DEFAULT_LANGUAGE?'&language='.CURRENT_LANGUAGE:'');
        case 'KEY_KEY':
            return get_rand_string(32);
        case 'KEY_SALT':
            return get_rand_string(32);
        case 'EMAIL_FROM_NAME':
            return CONFIG_PROJECT_NAME;
        case 'INFO_FINALLY_DATE':
            return date('Y',time());
        default:
            return $name;
    }
}

/**
 * 加载配置项(失败返回配置项内容)
 * 
 * @param string $name 完整的配置项名称
 * @param mixed $content 配置项内容
 * @return mixed
 */
function config_load($name=null,$content='')
{
    if($name==='PROJECT_COPYRIGHT')
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