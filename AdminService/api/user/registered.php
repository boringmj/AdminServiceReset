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

//环境补偿,用于 Php 不符合配置要求的情况(仅补偿Cookie)
if(isset($_COOKIE['ck_token']))
    $_REQUEST['ck_token']=$_COOKIE['ck_token'];
if(isset($_COOKIE['ck_kid']))
    $_REQUEST['ck_kid']=$_COOKIE['ck_kid'];
if(isset($_COOKIE['ck_key']))
    $_REQUEST['ck_key']=$_COOKIE['ck_key'];
if(isset($_COOKIE['expire_time']))
    $_REQUEST['expire_time']=$_COOKIE['expire_time'];

//我想了又想,最终决定还是基于 $_REQUEST 接收参数
if(empty($_REQUEST['ck_token'])||empty($_REQUEST['ck_kid'])||empty($_REQUEST['ck_key'])||empty($_REQUEST['expire_time']))
{
    $GLOBALS['return_data']=array(
        'code'=>1001,
        'msg'=>'错误: 暂无验证信息',
        'data'=>array($_REQUEST)
    );
    echo_return_data();
}

//开始验证结果
$ck_kid=$_REQUEST['ck_kid'];
$ck_key=$_REQUEST['ck_key'];
$expire_time=$_REQUEST['expire_time'];
$ck_token=md5(CONFIG_VERIFICATION_SALT.CONFIG_VERIFICATION_KEY."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
if($ck_token!=$_REQUEST['ck_token']||time()>$expire_time)
{
    $GLOBALS['return_data']=array(
        'code'=>1002,
        'msg'=>'错误: 验证不通过',
        'data'=>array()
    );
    echo_return_data();
}


//一切准备就绪
echo 'ok!'

?>