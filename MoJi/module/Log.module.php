<?php

//debug_log("title","msg",__FILE__);
/**
 * 写入调试日志
 * 
 * @param string $title 日志标题
 * @param string $msg 日志内容
 * @param string $file 文件名
 * @param int $grade 日志等级
 * @return void
 */
function debug_log($title,$msg,$file,$grade=1)
{
    if(CONFIG_LOG_LEVEL!=0&&$grade<CONFIG_LOG_LEVEL)
        return;
    if(APPLICATION_DEBUG&&CONFIG_LOG_STATUS)
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
/**
 * 写入日志
 * 
 * @param string $title 日志标题
 * @param string $msg 日志内容
 * @param string $file 文件名
 * @param int $grade 日志等级
 * @return void
 */
function write_log($title,$msg,$file,$grade=1)
{
    if(CONFIG_LOG_LEVEL!=0&&$grade<CONFIG_LOG_LEVEL)
        return;
    if(CONFIG_LOG_STATUS)
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
        if(filesize($log_path)>=CONFIG_LOG_FILE_SIZE)
        {
            if(!rename($log_path,$log_path.'.'.date('Y-m-d-H-i-s',time()).'.'.rand(1,100)))
                unlink($log_path);
            file_put_contents($log_path,'');
        }
        $log_file=fopen($log_path,"a");
        if($log_file)
            fwrite($log_file,variable_load($content_array,CONFIG_LOG_FORMAT));
        fclose($log_file);
    }
}

?>