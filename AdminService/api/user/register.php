<?php

//基础参数检查
if(check_request_empty_array('post',array('user','email')))
{
    $GLOBALS['return_data']=array(
        'code'=>1000,
        'msg'=>'错误: 必要参数为空',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

//传入数据格式检查
if(!preg_match(CONFIG_USER_NAME_RULE,$_POST['user']))
{
    $GLOBALS['return_data']=array(
        'code'=>1003,
        'msg'=>'错误: 用户名不合法',
        'data'=>array(
            'from'=>$_REQUEST['from'],
            'user'=>$_POST['user']
        )
    );
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,json_encode(array(
        'data'=>$GLOBALS['return_data'],
        'rule'=>CONFIG_USER_NAME_RULE
    )),__FILE__,15);
    echo_return_data();
}
if(!preg_match(CONFIG_USER_EMAIL_RULE,$_POST['email']))
{
    $GLOBALS['return_data']=array(
        'code'=>1004,
        'msg'=>'错误: 邮箱不合法',
        'data'=>array(
            'from'=>$_REQUEST['from'],
            'email'=>$_POST['email']
        )
    );
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,json_encode(array(
        'data'=>$GLOBALS['return_data'],
        'rule'=>CONFIG_USER_EMAIL_RULE
    )),__FILE__,15);
    echo_return_data();
}

//导入需要用到的用户类
load_class_array(array('User','Security'));

//准备安全类
$Security=new Security();
$Security->RecordRegister();
if(!$Security->CheckRegister())
{
    $GLOBALS['return_data']=array(
        'code'=>1029,
        'msg'=>'错误: 请求被拒绝',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    $path=CACHE_PATH.'/api_ip_'.md5(CONFIG_KEY_KEY.REQUEST_IP.CONFIG_USER_SALT.CONFIG_KEY_SALT).'.data.json';
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,file_get_contents($path),__FILE__,15);
    echo_return_data();
}

//准备邮箱类
$Sendmail=new Sendmail();
$Sendmail->SetSmtpConfig(CONFIG_EMAIL_SMTP_PORT,CONFIG_EMAIL_SMTP_HOST,CONFIG_EMAIL_SMTP_USER,CONFIG_EMAIL_SMTP_PASS);
$Sendmail->SetFromConfig(CONFIG_EMAIL_FROM_EMAIL,CONFIG_EMAIL_FROM_NAME);

//准备用户类
$User=new User();
$User->SetDatabase($Database);
//删除过期未激活用户(可放到监控中执行,也可以直接在这里执行)
$User->RemoveExpiredUser();

//验证用户是否已经存在
if($User->CheckUserExist($_POST['user'],$_POST['email']))
{
    $GLOBALS['return_data']=array(
        'code'=>1005,
        'msg'=>'错误: 用户名或邮箱已存在',
        'data'=>array(
            'user'=>$_POST['user'],
            'email'=>$_POST['email'],
            'from'=>$_REQUEST['from']
        )
    );
    echo_return_data();
}

$password=get_rand_string_id();

//创建用户
$uuid=$User->AddUser($_POST['user'],$_POST['user'],$password,$_POST['email']);
if(!$uuid)
{
    $GLOBALS['return_data']=array(
        'code'=>1006,
        'msg'=>'错误: 注册失败',
        'data'=>array(
            'user'=>$_POST['user'],
            'email'=>$_POST['email'],
            'from'=>$_REQUEST['from']
        )
    );
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,json_encode(array(
        'data'=>$GLOBALS['return_data'],
        'pdo_error'=>$User->DebugGetPdoError()
    )),__FILE__,15);
    echo_return_data();
}

$server_timestamp=time();
//计算出签名
$sign_array=array(
    'user'=>$_POST['user'],
    'token'=>$password,
    'timestamp'=>$server_timestamp,
    'uuid'=>$uuid,
    'key'=>md5(CONFIG_USER_SALT.CONFIG_KEY_KEY.CONFIG_KEY_SALT),
    'app_id'=>$_REQUEST['app_id']
);
$sign=sign($sign_array,$GLOBALS['app_key']);
$url=CONFIG_REQUEST_URL."?from=user&class=verify&sign={$sign}&user={$_POST['user']}&token={$password}&timestamp={$server_timestamp}&uuid={$uuid}&app_id={$_REQUEST['app_id']}";

//发送验证邮件
$title='感谢您使用-'.CONFIG_PROJECT_NAME;
$content=variable_load(array(
    'title'=>$title,
    'user_name'=>$_POST['user'],
    'overdue_date'=>date('Y-m-d H:i:s',$server_timestamp+CONFIG_USER_VERIFY_OVERDUE_TIME),
    'organization'=>CONFIG_PROJECT_NAME,
    'url'=>$url,
    'date'=>date('Y-m-d H:i:s',$server_timestamp)
),file_get_contents(RES_PATH.'/mail/email_user_verify.html'));
if($Sendmail->send($title,$content,$_POST['email'],$_POST['user']))
{
    $GLOBALS['return_data']=array(
        'code'=>1,
        'msg'=>'用户注册成功',
        'data'=>array(
            'uuid'=>$uuid,
            'user'=>$_POST['user'],
            'email'=>$_POST['email']
        )
    );
    echo_return_data();
}
else
{
    //失败就删除用户
    $User->RemoveUser($uuid);
    $GLOBALS['return_data']=array(
        'code'=>1007,
        'msg'=>'错误: 验证邮件发送失败',
        'data'=>array(
            'from'=>$_REQUEST['from'],
            'user'=>$_POST['user'],
            'email'=>$_POST['email']
        )
    );
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,json_encode(array(
        'data'=>$GLOBALS['return_data'],
        'email'=>$_POST['email'],
        'error'=>$Sendmail->error_info
    )),__FILE__,15);
    echo_return_data();
}

?>