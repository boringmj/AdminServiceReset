<?php

//本接口不要求用户登录,但也仅返回优先的用户信息且不论账户是否异常(未激活用户不支持获取用户信息)

//基础参数检查
if(check_request_empty_array('post',array('uuid')))
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


//获取用户信息
$user_info=$User->GetUserInfo($_POST['uuid']);
if($user_info)
{
    //验证用户是否已经激活
    if($user_info['status']==2)
    {
        $GLOBALS['return_data']=array(
            'code'=>1021,
            'msg'=>'错误: 非法请求',
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
        'server_timestamp'=>$timestamp,
        'server_app_id'=>$_POST['app_id'],
        'regtime'=>date('Y-m-d',$user_info['timestamp']),
        'user_group'=>$user_info['user_group'],
        'head_portraits'=>CONFIG_REQUEST_URL.'/?from=user&class=head_portrait&action='.$user_info['head_portraits'],
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
        'code'=>1020,
        'msg'=>'错误: 非法请求',
        'data'=>array(
            'from'=>$_REQUEST['from'],
            'uuid'=>$_POST['uuid']
        )
    );
    echo_return_data();
}
?>