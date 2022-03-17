<?php

if(!defined('APPLICATION_PATH')||!is_dir(APPLICATION_PATH))
    exit('<b>APPLICATION_PATH</b> Not incoming as required.');
if(!defined('APPLICATION_DEBUG'))
    define('APPLICATION_DEBUG',false);
if(!defined('APPLICATION_DEBUG_LEVEL'))
    if(APPLICATION_DEBUG)
        define('APPLICATION_DEBUG_LEVEL',E_ALL);
    else
        define('APPLICATION_DEBUG_LEVEL',0);
if(!defined('DATABASE_ENABLE'))
    define('DATABASE_ENABLE',false);
if(!defined('DEFAULT_LANGUAGE'))
    define('DEFAULT_LANGUAGE','zh-cn');
if(!empty($_REQUEST['language']))
    if(!preg_match("/(\.|\_)/",$_REQUEST['language']))
        define('CURRENT_LANGUAGE',$_REQUEST['language']);
    else
        define('CURRENT_LANGUAGE',DEFAULT_LANGUAGE);
else
    define('CURRENT_LANGUAGE',DEFAULT_LANGUAGE);

$constant_array=array(
    'REQUEST_ID'=>get_rand_string_id(),
    'REQUEST_IP'=>isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'0.0.0.0',
    'REQUEST_FORWARDED'=>isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'0.0.0.0',
    'HOST_NAME'=>(empty($_SERVER['SERVER_NAME'])||$_SERVER['SERVER_NAME']=='_')?(empty($_SERVER['HTTP_HOST'])?'0.0.0.0':$_SERVER['HTTP_HOST']):$_SERVER['SERVER_NAME'],
    'RES_PATH'=>dirname(__FILE__).'/res',
    'DATA_PATH'=>APPLICATION_PATH.'/Data',
    'USER_CLASS_PATH'=>APPLICATION_PATH.'/class',
    'PLUGIN_PATH'=>dirname(__FILE__).'/plugin',
    'PLUGIN_DATA_PATH'=>APPLICATION_PATH.'/Data/plugin',
    'CONFIG_PATH'=>APPLICATION_PATH.'/config',
    "REQUEST_URI"=>isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:''
);

foreach($constant_array as $constant_name=>$constant_value)
{
    define($constant_name,$constant_value);
}

?>