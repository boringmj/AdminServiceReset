<?php

debug_log(LANGUAGE_LOG_REQUEST_NAME,LANGUAGE_LOG_REQUEST_SUCCESS,__FILE__);

//集体处理请求
if(empty($_REQUEST['type']))
    $_REQUEST['type']='view';
if($_REQUEST['type']==='view')
    header('Content-Type: text/html; charset='.CONFIG_HTTP_CODE);
else if($_REQUEST['type']==='api')
    header('Content-Type:application/json');
if(empty($_REQUEST['from']))
    $_REQUEST['from']='Main';

//错误页显示
if($_REQUEST['from']==='error')
{
    //加载插件: RequestError()
    foreach($plugin_array as $main_class=>$plugin_data)
    {
        if(is_callable(array($plugin_array[$main_class]['Object'],'RequestError')))
            $plugin_array[$main_class]['Object']->RequestError();
    }
    if(empty($_GET['info']))
    $_GET['info']='NULL';
    $content=file_get_contents(RES_PATH.'/error.html');
    if($_GET['info']==='ERROR_FROM')
    {
        $content_array=array(
            'error_title'=>LANGUAGE_REQUEST_ERROR_TITLE,
            'error_msg'=>LANGUAGE_REQUEST_ERROR_MSG
        );
    }
    else
    {
        $content_array=array(
            'error_title'=>LANGUAGE_REQUEST_ERROR_TITLE_DEFAULT,
            'error_msg'=>LANGUAGE_REQUEST_ERROR_MSG_DEFAULT
        );
    }
    exit(variable_load($content_array,$content));
}

//外层请求安全模块
debug_log(LANGUAGE_LOG_REQUEST_NAME,LANGUAGE_LOG_REQUEST_IP.': '.REQUEST_IP.' '.LANGUAGE_LOG_AGENT_IP.': '.REQUEST_FORWARDED,__FILE__);
debug_log(LANGUAGE_LOG_REQUEST_NAME,'/?from='.urlencode($_REQUEST['from']).'&type='.urlencode($_REQUEST['type']),__FILE__);
if($_REQUEST['type']==='api')
{
    //接口安全模块
    //加载插件: ApiSecurity()
    foreach($plugin_array as $main_class=>$plugin_data)
    {
        if(is_callable(array($plugin_array[$main_class]['Object'],'ApiSecurity')))
            $plugin_array[$main_class]['Object']->ApiSecurity();
    }
}
else
{
    //显示安全模块
    //加载插件: ViewSecurity()
    foreach($plugin_array as $main_class=>$plugin_data)
    {
        if(is_callable(array($plugin_array[$main_class]['Object'],'ViewSecurity')))
            $plugin_array[$main_class]['Object']->ViewSecurity();
    }
}

//检验访问地址合法性
if(preg_match("/\./",$_REQUEST['from']))
{
    //记录非法请求的信息和地址到日志
    write_log(LANGUAGE_LOG_REQUEST_NAME,LANGUAGE_LOG_REQUEST_ILLEGAL.' :'.$_REQUEST['from'].' '.LANGUAGE_LOG_REQUEST_IP.': '.REQUEST_IP,__FILE__,10);
    if(CONFIG_REQUEST_ERROR_LEVEL)
        header('Location: '.CONFIG_REQUEST_ERROR_FROM);
    else
        exit(LANGUAGE_REQUEST_ERROR);
}
else
{
    $app_path=APPLICATION_PATH.($_REQUEST['type']==='api'?'/api':'/view').'/'.$_REQUEST['from'].'.php';
    if(is_file($app_path))
        call_user_func(function () use (&$Database,&$plugin_array,$app_path)
        {
            //这里使用匿名函数主要还是防止变量污染
            try
            {
                include $app_path;
                //加载插件: Finish()
                foreach($plugin_array as $main_class=>$plugin_data)
                {
                    if(is_callable(array($plugin_array[$main_class]['Object'],'Finish')))
                        $plugin_array[$main_class]['Object']->Finish();
                }
            }
            catch(Exception $error)
            {
                write_log(LANGUAGE_LOG_EXCEPTION_ERROR,$error->getMessage(),$app_path,20);
                if(APPLICATION_DEBUG)
                    exit($error->getMessage());
                else
                    exit(LANGUAGE_EXCEPTION_ERROR_NOT_DEBUG);
            }
        });
    else
        if(CONFIG_REQUEST_ERROR_LEVEL)
            header('Location: '.CONFIG_REQUEST_ERROR_FROM);
        else
            exit(LANGUAGE_REQUEST_ERROR);
    unset($app_path);
}

//通过数组的方式加载类(用户类)
function load_class_array($class_name)
{
    foreach($class_name as $class)
    {
        $class_path=USER_CLASS_PATH.'/'.$class.'.class.php';
        if(is_file($class_path))
            require $class_path;
        else
            write_log(LANGUAGE_LOG_REQUEST_CLASS_ERROR_TITLE,$class.' '.LANGUAGE_LOG_REQUEST_CLASS_ERROR,__FILE__,10);
    }
}

?>