<?php

if(!defined('APPLICATION_PATH')&&is_dir(APPLICATION_PATH))
    exit('APPLICATION_PATH 未按要求传入');
if(!defined('APPLICATION_DEBUG'))
    define('APPLICATION_DEBUG',false);
if(!defined('APPLICATION_DEBUG_LEVEL'))
    if(APPLICATION_DEBUG)
        define('APPLICATION_DEBUG_LEVEL',E_ALL);
    else
        define('APPLICATION_DEBUG_LEVEL',0);

$constant_array=array(
    'TEST'=>'Hello World!'
);

foreach($constant_array as $constant_name=>$constant_value)
{
    define($constant_name,$constant_value);
}

define('REQUEST_ID',get_rand_string_id());
define('REQUEST_IP',$_SERVER['REMOTE_ADDR']);
define('REQUEST_FORWARDED',isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'0.0.0.0');

?>