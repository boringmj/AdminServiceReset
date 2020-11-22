<?php

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