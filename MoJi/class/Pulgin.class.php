<?php

/**
 * 插件抽象类
 */
abstract class Pulgin
{
    protected $_Database;       //数据库对象
    protected $_data_path;      //数据存放目录
    protected $_config;         //程序配置数据

    /**
     * 初始化插件事件(默认调用的方法,请保留该方法且不可修改形参,请不要修改修饰符)
     * 
     * @param Database $Database 数据库对象
     * @param string $data_path 数据存放目录
     * @return void
     */
    abstract static public function Init(&$Database,$data_path);

    /**
     * 启动插件事件(默认调用的方法,请保留该方法且不可修改形参,请不要修改修饰符)
     * 
     * @param Database $Database 数据库对象
     * @param string $data_path 数据存放目录
     * @param array $config 程序配置数据
     * @return void
     */
    final public function Start(&$Database,$data_path,$config)
    {
        $this->_Database=$Database;
        $this->_data_path=$data_path;
        $this->_config=$config;
    }

    /**
     * 获取插件用户配置数据
     * 
     * @param string $name 配置项名称
     * @return mixed
     */
    final public function GetUserConfigValue($name)
    {
        if(isset($this->_config->User->$name->Value))
            return $this->_config->User->$name->Value;
        else
            return null;
    }

    /**
     * 获取插件系统配置数据
     * 
     * @param string $name 配置项名称
     * @return mixed
     */
    final public function GetSystemConfigValue($name)
    {
        if(isset($this->_config->System->Config->$name))
            return $this->_config->System->Config->$name;
        else
            return null;
    }

    /**
     * 保留方法(禁止构造函数)
     */
    final public function __construct()
    {
        //保留方法
    }

    /**
     * 保留方法(禁止构析函数)
     */
    final public function __destruct()
    {
        //保留方法
    }
}

?>