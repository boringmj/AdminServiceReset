<?php

//debug_log("title","msg",__FILE__);
function debug_log($title,$msg,$file,$grade=1)
{
    if(APPLICATION_DEBUG)
    {
        $log_date=date(CONFIG_LOG_FORMAT_DATE,time());
        $content_array=array(
            'title'=>$title,
            'msg'=>$msg,
            'file'=>$file,
            'grade'=>$grade,
            'date'=>$log_date,
            'id'=>REQUEST_ID
        );
        $log_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_DEBUG_PATH;
        $log_file=fopen($log_path,"a");
        if($log_file)
            fwrite($log_file,variable_load($content_array,CONFIG_LOG_FORMAT));
        fclose($log_file);
    }
}

//write_log("title","msg",__FILE__);
function write_log($title,$msg,$file,$grade=1)
{
    $log_date=date(CONFIG_LOG_FORMAT_DATE,time());
    $content_array=array(
        'title'=>$title,
        'msg'=>$msg,
        'file'=>$file,
        'grade'=>$grade,
        'date'=>$log_date,
        'id'=>REQUEST_ID
    );
    $log_path=CONFIG_LOG_DIR.'/'.CONFIG_LOG_PATH;
    $log_file=fopen($log_path,"a");
    if($log_file)
        fwrite($log_file,variable_load($content_array,CONFIG_LOG_FORMAT));
    fclose($log_file);
}

?>