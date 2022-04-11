<h1>这里模拟黑客(恶意用户)拦截到的即将提交到服务器的数据</h1>
<form name="input" action="/?type=api" method="POST" enctype="multipart/form-data">
<?php
$server_variable=array(
    'from'=>"user",
    'class'=>"login",
    'app_id'=>"0u725eCmqQr1ZAotJmiyi9y516111521",
    'time'=>"",
    'user'=>isset($_GET['user'])?$_GET['user']:'testuser',
    'password'=>md5(isset($_GET['password'])?$_GET['password']:'123456'),
    'timestamp'=>time(),
    'nonce'=>rand(1000,999999)
);
krsort($server_variable);
$server_sign='';
foreach($server_variable as $key=>$value)
{
    $server_sign.=$server_sign?"&{$key}={$value}":"{$key}={$value}";
    if($key==="from")
    echo "[GET]";
    else
    echo "[POST]";
    echo "{$key}<input type=\"text\" name=\"{$key}\" value=\"$value\"><br>";
}
$server_sign.='&app_key=445b6e6988efb6a117a119e73befa2b3';
$server_sign=md5($server_sign);
echo "[POST]sign<input type=\"text\" name=\"sign\" value=\"$server_sign\"><br>";

?>
<br>
<input type="submit" value="Submit">
</form>