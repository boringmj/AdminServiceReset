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

//我想了又想,最终决定还是基于cookie实现吧
if(empty($_COOKIE['ck_token'])||empty($_COOKIE['ck_kid'])||empty($_COOKIE['ck_key'])||empty($_COOKIE['expire_time']))
{
    $GLOBALS['return_data']=array(
        'code'=>1001,
        'msg'=>'错误: COOKIE未开启或已过期',
        'data'=>array('ck_token'=>$_COOKIE['ck_token'],'ck_kid'=>$_COOKIE['ck_kid'],'ck_key'=>$_COOKIE['ck_key'],'expire_time'=>$_COOKIE['expire_time'])
    );
    echo_return_data();
}

//开始验证结果
$ck_kid=$_COOKIE['ck_kid'];
$ck_key=$_COOKIE['ck_key'];
$expire_time=$_COOKIE['expire_time'];
$ck_token=md5(CONFIG_VERIFICATION_SALT.CONFIG_VERIFICATION_KEY."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
if($ck_token!=$_COOKIE['ck_token'])
{

}


?>