<?php

//基础参数检查
if(check_request_empty_array('get',array('sign','user','token','timestamp','uuid','app_id')))
{
    $GLOBALS['return_data']=array(
        'code'=>1000,
        'msg'=>'错误: 必要参数为空',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

//检验app_id是否合法
$app_key=Admin::GetAppKey($Database,$_GET['app_id']);
if(!$app_key)
{
    $GLOBALS['return_data']=array(
        'code'=>1015,
        'msg'=>'错误: APP_ID不合法',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

$server_timestamp=time();
//计算出签名
$sign_array=array(
    'user'=>$_GET['user'],
    'token'=>$_GET['token'],
    'timestamp'=>$_GET['timestamp'],
    'uuid'=>$_GET['uuid'],
    'key'=>md5(CONFIG_USER_SALT.CONFIG_KEY_KEY.CONFIG_KEY_SALT),
    'app_id'=>$_GET['app_id']
);
$sign=sign($sign_array,$app_key);
//签名校验
if($sign!=$_GET['sign'])
{
    $GLOBALS['return_data']=array(
        'code'=>1008,
        'msg'=>'错误: 签名校验失败',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

//验证请求是否过期
if(($server_timestamp-$_GET['timestamp'])>CONFIG_USER_VERIFY_OVERDUE_TIME)
{
    $GLOBALS['return_data']=array(
        'code'=>1009,
        'msg'=>'错误: 请求已过期',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}


//导入需要用到的用户类
load_class_array(array('User'));

//准备用户类
$User=new User();
$User->SetDatabase($Database);
$User->app_id=$_GET['app_id'];

//验证基本信息是否正确
$user_info=$User->GetUserInfo($_GET['uuid']);
if(!$user_info)
{
    $GLOBALS['return_data']=array(
        'code'=>1010,
        'msg'=>'错误: 非法请求',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}
$password=$user_info['password'];
$user=$user_info['user_name'];
if($user!=$_GET['user']||$password!=md5($_GET['token'].$User->user_salt)||$user_info['status']!=2)
{
    $GLOBALS['return_data']=array(
        'code'=>1011,
        'msg'=>'错误: 非法请求',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

if(empty($_GET['action']))
{
    //返回一个用于填写密码和用户名的页面
    $url=CONFIG_REQUEST_URL."?from=user&class=verify&sign={$sign}&user={$user}&token={$_GET['token']}&timestamp={$_GET['timestamp']}&uuid={$_GET['uuid']}&app_id={$_REQUEST['app_id']}&action=yes";
    $title='激活用户-'.CONFIG_PROJECT_OPERATOR;
    $content=variable_load(array(
        'title'=>$title,
        'user'=>$_GET['user'],
        'overdue_date'=>date('Y-m-d H:i:s',$_GET['timestamp']+CONFIG_USER_VERIFY_OVERDUE_TIME),
        'organization'=>CONFIG_PROJECT_OPERATOR,
        'url'=>$url,
        'nickname_rule'=>preg_replace('/\\\x\{4e00\}\-\\\x\{9fa5\}/','\u4e00-\u9fa5',CONFIG_USER_NICKNAME_RULE),
        'password_rule'=>preg_replace('/\\\x\{4e00\}\-\\\x\{9fa5\}/','\u4e00-\u9fa5',CONFIG_USER_PASSWORD_RULE)
    ),file_get_contents(RES_PATH.'/view_user_verify.html'));
    echo $content;
}
else
{
    if(check_request_empty_array('post',array('nickname','password','rpassword','user')))
    {
        $GLOBALS['return_data']=array(
            'code'=>1000,
            'msg'=>'错误: 必要参数为空',
            'data'=>array(
                'user'=>empty($_POST['user'])?'':$_POST['user'],
                'nickname'=>empty($_POST['nickname'])?'':$_POST['nickname'],
                'password'=>empty($_POST['password'])?'':$_POST['password'],
                'rpassword'=>empty($_POST['rpassword'])?'':$_POST['rpassword']
            )
        );
        echo_return_data();
    }
    if($_GET['user']!=$_POST['user'])
    {
        $GLOBALS['return_data']=array(
            'code'=>1012,
            'msg'=>'错误: 用户名不匹配',
            'data'=>array('from'=>$_REQUEST['from'])
        );
        echo_return_data();
    }
    if($_POST['password']!=$_POST['rpassword'])
    {
        $GLOBALS['return_data']=array(
            'code'=>1013,
            'msg'=>'错误: 密码不匹配',
            'data'=>array('from'=>$_REQUEST['from'])
        );
        echo_return_data();
    }
    if(!preg_match(CONFIG_USER_NICKNAME_RULE,$_POST['nickname']))
    {
        $GLOBALS['return_data']=array(
            'code'=>1014,
            'msg'=>'错误: 昵称不符合法',
            'data'=>array(
                'from'=>$_REQUEST['from'],
                'user'=>$_POST['user']
            )
        );
        debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,json_encode(array(
            'data'=>$GLOBALS['return_data'],
            'rule'=>CONFIG_USER_NICKNAME_RULE
        )),__FILE__,15);
        echo_return_data();
    }
    //设置用户信息(不判断是否成功)
    $User->SetUserStatus($_GET['uuid'],1);
    $User->SetUserNickname($_GET['uuid'],$_POST['nickname']);
    $User->SetUserPassword($_GET['uuid'],$_POST['password']);
    $GLOBALS['return_data']=array(
        'code'=>1,
        'msg'=>'您的账户已激活!',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

?>