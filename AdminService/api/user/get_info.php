<?php

//本接口要求用户已经登录,所以需要检查用户是否已经登录,且本接口不删除过期的Token但需要更新Token的有效期(如果安全配置允许更新的话)

//基础参数检查
if(check_request_empty_array('post',array('uuid','token','ukey')))
{
    $GLOBALS['return_data']=array(
        'code'=>1000,
        'msg'=>'错误: 必要参数为空',
        'data'=>array('from'=>$_REQUEST['from'])
    );
    echo_return_data();
}

//导入需要用到的用户类
load_class_array(array('User'));

//准备用户类
$User=new User();
$User->SetDatabase($Database);

//验证Token和ukey的合法性
if(!$User->CheckUserToken($_POST['uuid'],$_POST['token']))
{
    $GLOBALS['return_data']=array(
        'code'=>1017,
        'msg'=>'错误: 令牌错误',
        'data'=>array(
            'from'=>$_REQUEST['from']
        )
    );
    echo_return_data();
}
$ukey=md5(CONFIG_KEY_SALT.$_POST['token'].$_POST['uuid'].CONFIG_KEY_KEY.(CONFIG_SECURITY_USER_TOKEN_TIME_BIND_IP_GRADE>=1?REQUEST_IP:'').(CONFIG_SECURITY_USER_TOKEN_TIME_BIND_IP_GRADE>=2?REQUEST_FORWARDED:''));
if($ukey!=$_POST['ukey'])
{
    $GLOBALS['return_data']=array(
        'code'=>1017,
        'msg'=>'错误: 令牌错误',
        'data'=>array(
            'from'=>$_REQUEST['from']
        )
    );
    echo_return_data();
}

//检验是否需要更新Token的有效期
if(CONFIG_SECURITY_USER_TOKEN_TIME_RESET_TIME)
{
    //不验证是否成功
    $User->ResteUserTokenTime($_POST['uuid'],$_POST['token']);
}

//获取用户信息
$user_info=$User->GetUserInfo($_POST['uuid']);
if($user_info)
{
    //验证用户状态是否异常
    if($user_info['status']!=1)
    {
        $GLOBALS['return_data']=array(
            'code'=>1019,
            'msg'=>'错误: 用户状态异常',
            'data'=>array(
                'from'=>$_REQUEST['from']
            )
        );
        echo_return_data();
    }

    //获取组名
    $group_info=$User->GetUserGroup($user_info['user_group']);
    $timestamp=time();
    $sign_array=array(
        'uuid'=>$user_info['uuid'],
        'user'=>$user_info['user_name'],
        'nickname'=>$user_info['nickname'],
        'timestamp'=>$timestamp,
        'user_group'=>$user_info['user_group'],
        'app_id'=>$_POST['app_id'],
        'head_portraits'=>$user_info['head_portraits'],
        'status'=>$user_info['status'],
        'group_name'=>empty($group_info['group_name'])?'无效的组名':$group_info['group_name'],
        'group_level'=>empty($group_info['group_level'])?1:$group_info['group_level']
    );
    $sign=sign($sign_array,$GLOBALS['app_key']);
    $sign_array['sign']=$sign;
    $GLOBALS['return_data']=array(
        'code'=>1,
        'msg'=>'成功',
        'data'=>$sign_array
    );
    echo_return_data();
}
else
{
    $GLOBALS['return_data']=array(
        'code'=>1018,
        'msg'=>'错误: 非法请求',
        'data'=>array(
            'from'=>$_REQUEST['from'],
            'uuid'=>$_POST['uuid']
        )
    );
    echo_return_data();
}
?>