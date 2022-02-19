<?php

class Database
{
    public $prefix;
    public $object;
    public $error;

    protected $_host;
    protected $_user;
    protected $_passwd;
    protected $_database;
    protected $_type='mysql';

    //设置连接地址
    public function SetHost($host)
    {
        $this->_host=$host;
    }
    
    //设置连接用户
    public function SetUser($user)
    {
        $this->_user=$user;
    }

    //设置连接密码
    public function SetPasswd($passwd)
    {
        $this->_passwd=$passwd;
    }

    //设置连接库名
    public function SetDatabase($database)
    {
        $this->_database=$database;
    }

    //获取数据表名称
    public function GetTablename($table)
    {
        return "{$this->prefix}{$table}";
    }

    //连接数据库
    public function Link()
    {
        try
        {

            $database_link="{$this->_type}:host={$this->_host};dbname={$this->_database}";
            $this->object=new PDO($database_link,$this->_user,$this->_passwd);
            return 1;
        }
        catch(PDOException $error)
        {
            $this->error=$error->getMessage();
            return 0;
        }
    }

    function __construct($host='',$user='',$passwd='',$database='')
    {
        $this->_host=$host;
        $this->_user=$user;
        $this->_passwd=$passwd;
        $this->_database=$database;
        if(!empty($host)&&!empty($user)&&!empty($passwd)&&!empty($database))
            return $this->Link();
    }
}

?>