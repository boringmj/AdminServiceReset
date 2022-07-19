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
    protected $_type;

    /**
     * 设置连接地址
     * 
     * @param string $host 主机地址
     * @return void
     */
    public function SetHost($host)
    {
        $this->_host=$host;
    }
    
    /**
     * 设置用户名
     * 
     * @param string $user 用户名
     * @return void
     */
    public function SetUser($user)
    {
        $this->_user=$user;
    }

    /**
     * 设置密码
     * 
     * @param string $passwd 密码
     * @return void
     */
    public function SetPasswd($passwd)
    {
        $this->_passwd=$passwd;
    }

    /**
     * 设置数据库名
     * 
     * @param string $database 数据库名
     * @return void
     */
    public function SetDatabase($database)
    {
        $this->_database=$database;
    }

    /**
     * 获取完整数据表名称
     * 
     * @param string $table 数据表名称
     * @return string
     */
    public function GetTablename($table)
    {
        return "{$this->prefix}{$table}";
    }

    /**
     * 连接数据库
     * 
     * @return boolean
     */
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

    /**
     * 构造函数(如果主机地址、用户名、密码和数据库名均设置,则连接数据库)
     * 
     * @param string $host 主机地址
     * @param string $user 用户名
     * @param string $passwd 密码
     * @param string $database 数据库名
     * @return void|POD
     */
    public function __construct($host='',$user='',$passwd='',$database='')
    {
        $this->_type='mysql';
        $this->_host=$host;
        $this->_user=$user;
        $this->_passwd=$passwd;
        $this->_database=$database;
        if(!empty($host)&&!empty($user)&&!empty($passwd)&&!empty($database))
            return $this->Link();
    }
}

?>