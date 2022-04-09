<?php

//设置头为image/png
header("content-type:image/png");

if(isset($_REQUEST['action'])&&$_REQUEST['action']=='default')
{
    //直接输出用户头像
    echo file_get_contents(RES_PATH.'/default_user_head_portrait.png');
}

?>