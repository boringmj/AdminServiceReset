<?php

function variable_load($variable_array,$content)
{
    foreach($variable_array as $variable=>$variable_value)
        $content=preg_replace("/\\\$\{{$variable}\}/",$variable_value,$content);
    return $content;
}

?>