<?php

/**
 * 加载模块
 * 
 * @param array $module_array 模块数组
 * @return void
 */
function LoadModule($module_array)
{
    global $Database,$plugin_array;
    foreach($module_array as $module)
    {
        $module_path=__DIR__.'/'.$module.'.module.php';
        if(is_file($module_path)&&preg_match('/^[A-Z_]\w*$/',$module))
            require $module_path;
    }
}

?>