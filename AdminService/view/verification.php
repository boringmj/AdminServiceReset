<?php

if(!empty($_GET['ck_key']))
{
    if(empty($_COOKIE['ck_token'])||empty($_COOKIE['ck_kid'])||empty($_COOKIE['expire_time']))
        exit('错误: 本次验证请求已过期或出现异常');
    $ck_kid=$_COOKIE['ck_kid'];
    $ck_key=$_GET['ck_key'];
    $expire_time=$_COOKIE['expire_time'];
    $ck_token=md5(CONFIG_VERIFICATION_SALT.CONFIG_VERIFICATION_KEY."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
    if($ck_token===$_COOKIE['ck_token'])
    {
        setcookie('ck_key',$_GET['ck_key'],$expire_time);
        echo '恭喜您验证通过';
    }
    else
        echo '错误: 非法请求';
}
else
{
    $javascript_code=file_get_contents(RES_PATH.'/verification.js');
    $javascript_script=new Verification($Database);
    $javascript_tmp=$javascript_script->StartCheck();
    $content_array=array(
        'javascript_script'=>$javascript_tmp
    );
    echo "<html><script>";
    echo javascript_encode(variable_load($content_array,$javascript_code));
    echo "</script></html>";
}

?>