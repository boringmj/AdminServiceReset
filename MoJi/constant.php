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
    define('DATABASE_ENABLE',FALSE);

$constant_array=array(
    'REQUEST_ID'=>get_rand_string_id(),
    'REQUEST_IP'=>isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'0.0.0.0',
    'REQUEST_FORWARDED'=>isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'0.0.0.0',
    'RES_PATH'=>dirname(__FILE__).'/res',
    'DATA_PATH'=>APPLICATION_PATH.'/Data'
);

foreach($constant_array as $constant_name=>$constant_value)
{
    define($constant_name,$constant_value);
}

?>