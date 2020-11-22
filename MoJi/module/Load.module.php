<?php

function LoadModule($module_array)
{
    foreach($module_array as $module)
    {
        $module_path=dirname(__FILE__).'/'.$module.'.module.php';
        if(is_file($module_path)&&preg_match('/^[A-Z_]\w*$/',$module))
            require $module_path;
    }
}

?>