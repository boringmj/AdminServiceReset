<?php

//自动补全目录(前提是程序路径 APPLICATION_PATH 可以写入)
if(is_writable(APPLICATION_PATH))
{
    if(!file_exists(APPLICATION_PATH.'/Data'))
        mkdir(APPLICATION_PATH.'/Data');
    if(!file_exists(APPLICATION_PATH.'/Log'))
        mkdir(APPLICATION_PATH.'/Log');
}

//自动补全数据目录的子目录(如果 DATA_PATH 可写的话)
if(is_writable(DATA_PATH))
{
    if(!file_exists(DATA_PATH.'/permission'))
        mkdir(DATA_PATH.'/permission');
    if(!file_exists(CACHE_PATH))
        mkdir(CACHE_PATH);
}

$write_path_array=array();
if(CONFIG_LOG_STATUS)
{
    array_push($write_path_array,CONFIG_LOG_DIR);
    if(is_writable(CONFIG_LOG_DIR))
    {
        if(APPLICATION_DEBUG)
        {
            $log_debug_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_DEBUG_PATH;
            if(!file_exists($log_debug_path))
                file_put_contents($log_debug_path,'');
            array_push($write_path_array,$log_debug_path);
        }
        $log_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_PATH;
        if(!file_exists($log_path))
            file_put_contents($log_path,'');
        array_push($write_path_array,$log_path);
    }
}
if(file_exists(PLUGIN_DATA_PATH))
    array_push($write_path_array,PLUGIN_DATA_PATH);
array_push($write_path_array,DATA_PATH);
array_push($write_path_array,DATA_PATH.'/permission');
// array_push($write_path_array,DATA_PATH.'/key.json');
foreach($write_path_array as $write_path)
    if(!is_writable($write_path)&&!empty($write_path))
        if(APPLICATION_DEBUG)
            exit("{$write_path} ".LANGUAGE_WRITE_PATH_DEBUG);
        else
            exit(LANGUAGE_WRITE_PATH);

?>