<?php

debug_log(LANGUAGE_LOG_REQUEST_NAME,LANGUAGE_LOG_REQUEST_SUCCESS,__FILE__);

//集体处理请求
if(empty($_REQUEST['type']))
    $_REQUEST['type']='view';
if($_REQUEST['type']==='api')
    header('Content-Type:application/json');
if(empty($_REQUEST['from']))
    $_REQUEST['from']='main';

//预定义api输出的数据
$GLOBALS['return_data']=array();

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

//预加载类
load_class_array(array('Admin'));

if($_REQUEST['type']==='api')
{
    //接口安全模块
    //加载插件: ApiSecurity()
    foreach($plugin_array as $main_class=>$plugin_data)
    {
        if(is_callable(array($plugin_array[$main_class]['Object'],'ApiSecurity')))
            $plugin_array[$main_class]['Object']->ApiSecurity();
    }
    //绝对参数处理
    if(empty($_REQUEST['request_type']))
        $_REQUEST['request_type']='post';
    if(empty($_REQUEST['return_type']))
        $_REQUEST['return_type']='json';
    if($_REQUEST['request_type']==='ium'||$_REQUEST['return_type']==='ium')
        load_class_array(array('Iumcode'));
    if(empty($_POST['app_id']))
    {
        $GLOBALS['return_data']=array(
            'code'=>-2,
            'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_TOW,
            'data'=>array('error'=>'app_id is empty')
        );
        echo_return_data();
    }
    $GLOBALS['app_key']=Admin::GetAppKey($Database,$_POST['app_id']);
    //初步处理传入数据
    if(empty($GLOBALS['app_key']))
    {
        $GLOBALS['return_data']=array(
            'code'=>-2,
            'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_TOW,
            'data'=>array('error'=>'app_id is error')
        );
        echo_return_data();
    }
    if($_REQUEST['request_type']==='ium')
    {
        if(!empty($_POST['s']))
        {
            //尝试解密
            $temp_data=Iumcode::DecodeIum($_POST['s'],$GLOBALS['app_key']);
            //销毁传入的s参数
            unset($_POST['s']);
            //将上传的数据分割为数组
            $temp_data=explode('&',$temp_data);
            foreach( $temp_data as $temp_data_value)
            {
                //截取出提交数据的
                if(strpos($temp_data_value,'='))
                {
                    $name=substr($temp_data_value,0,strpos($temp_data_value,'='));
                    $value=substr($temp_data_value,strpos($temp_data_value,'=')+1);
                    //请注意,这里采用的不是原值,而是urldecode后的值
                    $_POST[$name]=urldecode($value);
                }
                else
                {
                    $_POST[$temp_data_value]="";
                }
            }
        }
    }
    //需要进行鉴权的接口组
    $from_api_security_array=array('main','user');
    if(in_array($_REQUEST['from'],$from_api_security_array))
    {
        //基础参数检查
        if(check_request_empty_array('post',array('sign','nonce'))||!(!empty($_POST['time'])||!empty($_POST['timestamp'])))
        {
            $GLOBALS['return_data']=array(
                'code'=>-2,
                'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_TOW,
                'data'=>array('error'=>'required parameters are empty')
            );
            echo_return_data();
        }
        //取时间戳,值得注意的是,接口的time参数值为now这类的,那么这个接口就永远不会超时过期
        if(!empty($_POST['timestamp']))
            $post_timestamp=$_POST['timestamp'];
        else
            $post_timestamp=strtotime($_POST['time']);
        settype($post_timestamp,'int');
        $server_timestamp=time();
        //验证请求是否还在合法时间内(运行时间差为10分钟)
        if($server_timestamp-$post_timestamp>=600||$server_timestamp-$post_timestamp<=-600)
        {
            $GLOBALS['return_data']=array(
                'code'=>-3,
                'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_THREE,
                'data'=>array(
                    'server_timestamp'=>$server_timestamp,
                    'post_timestamp'=>$post_timestamp
                )
            );
            echo_return_data();
        }
        //验证请求是否重复
        if(!Admin::CheckNonce($Database,$_POST['app_id'],$_POST['nonce'],$_POST['sign']))
        {
            $GLOBALS['return_data']=array(
                'code'=>-4,
                'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_FOUR,
                'data'=>array()
            );
            echo_return_data();
        }
        //签名校验
        $post_data='';
        $post_data_array=$_POST;
        //排除签名的数据组
        $not_sign_key=array('sign','language','type');
        if($_REQUEST['request_type']!=='post')
            $post_data_array['request_type']=$_REQUEST['request_type'];
        else
            array_push($not_sign_key,'return_type');
        if($_REQUEST['return_type']!=='json')
            $post_data_array['return_type']=$_REQUEST['return_type'];
        else
            array_push($not_sign_key,'return_type');
        //删除排除的参数,以免对排序造成影响(效率较低)
        foreach($not_sign_key as $value)
        {
            $array_sign_keys=array_keys($post_data_array);
            $index=array_search($value,$array_sign_keys);
            if($index!==false)
                array_splice($post_data_array,$index,1);
        }
        krsort($post_data_array);
        foreach($post_data_array as $key=>$value)
        {
            $post_data.=(empty($post_data)?'':'&')."{$key}={$value}";
        }
        $server_sign=md5($post_data.'&app_key='.$GLOBALS['app_key']);
        if($_POST['sign']!=$server_sign)
        {
            $GLOBALS['return_data']=array(
                'code'=>-5,
                'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_FIVES,
                'data'=>array()
            );
            echo_return_data();
        }
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
if(preg_match("/(\.|\\/|\\\\)/",$_REQUEST['from']))
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
    //检查是否为子系统
    if(!empty($_REQUEST['class']))
    {
        //REQUEST的class参数不为空就被识别为REQUEST的from参数的子系统
        if(!preg_match("/\./",$_REQUEST['class']))
        {
            $app_path=APPLICATION_PATH.($_REQUEST['type']==='api'?'/api':'/view').'/'.$_REQUEST['from'].'/'.$_REQUEST['class'].'.php';
        }
        else
        {
            //记录非法请求的信息和地址到日志
            write_log(LANGUAGE_LOG_REQUEST_NAME,LANGUAGE_LOG_REQUEST_ILLEGAL.' :'.$_REQUEST['from'].'&class='.$_REQUEST['class'].' '.LANGUAGE_LOG_REQUEST_IP.': '.REQUEST_IP,__FILE__,10);
            if(CONFIG_REQUEST_ERROR_LEVEL)
                header('Location: '.CONFIG_REQUEST_ERROR_FROM);
            else
                exit(LANGUAGE_REQUEST_ERROR);
        }
    }
    else
    {
        $app_path=APPLICATION_PATH.($_REQUEST['type']==='api'?'/api':'/view').'/'.$_REQUEST['from'].'.php';
    }
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

//接口强制输出内容(结束本次运行)
function echo_return_data($return_path='')
{
    //目前支持的返回方式只有json
    if(is_array($GLOBALS['return_data']))
    {
        if(class_exists('Iumcode')&&!empty($GLOBALS['app_key'])&&$_REQUEST['return_type']==='ium')
            echo Iumcode::EncodeIum(json_encode($GLOBALS['return_data']),$GLOBALS['app_key']);
        else
            echo json_encode($GLOBALS['return_data']);
        //调试模式下记录所有错误请求
        if($GLOBALS['return_data']['code']!=1)
            debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,'Cdoe: '.$GLOBALS['return_data']['code'].','.$GLOBALS['return_data']['msg'],empty($return_path)?__FILE__:$return_path,15);
    }
    else
    {
        echo json_encode(array(
            'code'=>-1,
            'msg'=>LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_ONE,
            'data'=>array()
        ));
        write_log(LANGUAGE_LOG_EXCEPTION_ERROR,'Cdoe: -1,'.LANGUAGE_ADMINSERVICE_ERROR_CODE_MINUS_ONE,empty($return_path)?__FILE__:$return_path,15);
    }
    exit();
}

//验证参数是否传入且不为空
function check_request_empty_array($type,$post_variable)
{
    //放弃代码量选择效率(反正差距不大)
    switch($type)
    {
        case 'get':
            foreach($post_variable as $value)
                if(empty($_GET[$value]))
                    return true;
            break;
        case 'post':
            foreach($post_variable as $value)
                if(empty($_POST[$value]))
                    return true;
            break;
        default:
            foreach($post_variable as $value)
                if(empty($_REQUEST[$value]))
                    return true;
    }
    return false;
}

?>