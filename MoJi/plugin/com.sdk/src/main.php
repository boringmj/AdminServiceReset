<?php

//请注意类名需要与 info.json->Main 保持一致,且符合命名规范
class SdkMain
{
    protected $Database;    //数据库对象
    protected $data_path;   //数据存放位置

    //默认调用的方法,请保留该方法且不可修改形参
    public function Start(&$Database,$data_path)
    {
        $this->Database=$Database;
        $this->data_path=$data_path;
    }
}

?>