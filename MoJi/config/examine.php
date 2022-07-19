<?php

/**
 * 将配置项加载到系统中
 * 
 * @param array $config_array 配置项数组
 * @return void
 */
function config_examine($config_array)
{
    global $$config_array;
    foreach($$config_array as $config_name=>$config_value)
    {
        define("{$config_array}_{$config_name}",$config_value);
    }
    $$config_array=null;
}

?>