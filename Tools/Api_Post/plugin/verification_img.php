<h1>这里模拟黑客(恶意用户)拦截到的即将提交到服务器的数据</h1>
<form name="input" action="/?type=api" method="POST" enctype="multipart/form-data">
<?php
include "../test_data.php";
$server_variable=array(
    'from'=>"verification_img",
    'ck_kid'=>isset($_GET['ck_kid'])?$_GET['ck_kid']:'',
    'ck_token'=>isset($_GET['ck_token'])?$_GET['ck_token']:'',
);
foreach($server_variable as $key=>$value)
{
    echo "[POST]";
    echo "{$key}<input type=\"text\" name=\"{$key}\" value=\"$value\"><br>";
}

?>
<br>
<input type="submit" value="Submit">
</form>