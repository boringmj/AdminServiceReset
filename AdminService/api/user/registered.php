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

//一切准备就绪
echo 'ok!'

?>