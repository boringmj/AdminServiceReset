<?php

//请注意类名需要与 info.json->Main 保持一致,且符合命名规范
class PluginSdkMain
{
    protected $_Database;       //数据库对象
    protected $_data_path;      //数据存放位置
    protected $_config;         //程序配置数据

    //默认调用的方法,请保留该方法且不可修改形参
    public function Start(&$Database,$data_path,$config)
    {
        $this->_Database=$Database;
        $this->_data_path=$data_path;
        $this->_config=$config;
    }

    //接口安全
    public function ApiSecurity()
    {

    }

    //页面安全
    public function ViewSecurity()
    {

    }

    //请求错误页
    public function RequestError()
    {

    }

    //完成请求
    public function Finish()
    {
        
    }
}

?>