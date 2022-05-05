<?php

//原则上是不对传入数据进行格式检查的,规则可能随时会变更,规则变更可能会导致以前合法的数据变得不再合法,这是不期望的

//基础参数检查
if(check_request_empty_array('post',array('user','password')))
{
    $GLOBALS['return_data']=array(
        'code'=>1000,
        'msg'=>'错误: 必要参数为空',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

//导入需要用到的用户类
load_class_array(array('User','Security'));

//准备安全类
$Security=new Security();
$Security->RecordLogin();
if(!$Security->CheckLogin())
{
    $GLOBALS['return_data']=array(
        'code'=>1028,
        'msg'=>'错误: 请求被拒绝',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    $path=CACHE_PATH.'/api_ip_'.md5(CONFIG_KEY_KEY.REQUEST_IP.CONFIG_USER_SALT.CONFIG_KEY_SALT).'.data.json';
    debug_log(LANGUAGE_LOG_EXCEPTION_ERROR,file_get_contents($path),__FILE__,15);
    echo_return_data();
}

//准备用户类
$User=new User();
$User->SetDatabase($Database);

//删除过期的Token(可放到监控中执行,也可以直接在这里执行)
$User->RemoveExpiredToken();

$uuid=$User->CheckUser($_POST['user'],$_POST['password']);
if($uuid)
{
    if(!CONFIG_SECURITY_USER_ALLOW_MULTIPLE_TOKEN)
    {
        //禁止一个用户同时使用多个Token
        $User->RemoveUserToken($uuid);
    }
    $user_info=$User->GetUserInfo($uuid);
    //根据token计算ukey
    $timestamp=time();
    $token=$User->AddUserToken($user_info['uuid']);
    $ukey=md5(CONFIG_KEY_SALT.$token.$uuid.CONFIG_KEY_KEY.(CONFIG_SECURITY_USER_TOKEN_TIME_BIND_IP_GRADE>=1?REQUEST_IP:'').(CONFIG_SECURITY_USER_TOKEN_TIME_BIND_IP_GRADE>=2?REQUEST_FORWARDED:''));
    //获取组名
    $group_info=$User->GetUserGroup($user_info['user_group']);
    $sign_array=array(
        'uuid'=>$user_info['uuid'],
        'user'=>$user_info['user_name'],
        'token'=>$token,
        'ukey'=>$ukey,
        'server_timestamp'=>$timestamp,
        'server_app_id'=>$_POST['app_id'],
        'status'=>$user_info['status']
    );
    $sign=sign($sign_array,$GLOBALS['app_key']);
    $sign_array['sign']=$sign;
    $GLOBALS['return_data']=array(
        'code'=>1,
        'msg'=>'登录成功',
        'data'=>$sign_array
    );
    echo_return_data();
}
else
{
    $Security->RecordLoginError();
    $GLOBALS['return_data']=array(
        'code'=>1016,
        'msg'=>'错误: 登录失败',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}
?>