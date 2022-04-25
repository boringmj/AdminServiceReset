<h1>这里模拟黑客(恶意用户)拦截到的即将提交到服务器的数据</h1>
<form name="input" action="/?type=api" method="POST" enctype="multipart/form-data">
<?php
include "../test_data.php";
$server_variable=array(
    'from'=>"user",
    'class'=>"register",
    'app_id'=>$app_id,
    'time'=>"",
    'verification_value'=>isset($_GET['verification_value'])?$_GET['verification_value']:'',
    'ck_kid'=>isset($_GET['ck_kid'])?$_GET['ck_kid']:'',
    'ck_token'=>isset($_GET['ck_token'])?$_GET['ck_token']:'',
    'user'=>isset($_GET['user'])?$_GET['user']:$user,
    'email'=>isset($_GET['email'])?$_GET['email']:$email,
    'timestamp'=>time(),
    'nonce'=>rand(1000,999999)
);
krsort($server_variable);
$server_sign='';
foreach($server_variable as $key=>$value)
{
    $server_sign.=$server_sign?"&{$key}={$value}":"{$key}={$value}";
    echo "[POST]";
    echo "{$key}<input type=\"text\" name=\"{$key}\" value=\"$value\"><br>";
}
$server_sign.='&app_key='.$app_key;
$server_sign=md5($server_sign);
echo "[POST]sign<input type=\"text\" name=\"sign\" value=\"$server_sign\"><br>";

?>
<br>
<input type="submit" value="Submit">
</form>