<h1>这里模拟黑客(恶意用户)拦截到的即将提交到服务器的数据</h1>
<form name="input" action="/?type=api" method="POST" enctype="multipart/form-data">
<?php
include "../test_data.php";
echo "com.verification.api插件需要修改,否则请求将会被作为web非法请求拦截<br>";
$server_variable=array(
    'from'=>"verification_get",
    'app_id'=>$app_id
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