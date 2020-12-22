<?php

class Database
{
    public $prefix;
    public $object;
    public $error;

    protected $host;
    protected $user;
    protected $passwd;
    protected $database;
    protected $type='mysql';

    //设置连接地址
    public function SetHost($host)
    {
        $this->host=$host;
    }
    
    //设置连接用户
    public function SetUser($user)
    {
        $this->user=$user;
    }

    //设置连接密码
    public function SetPasswd($passwd)
    {
        $this->passwd=$passwd;
    }

    //设置连接库名
    public function SetDatabase($database)
    {
        $this->database=$database;
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

            $database_link="{$this->type}:host={$this->host};dbname={$this->database}";
            $this->object=new PDO($database_link,$this->user,$this->passwd);
            return 1;
        }
        catch(PDOException $err)
        {
            $this->error=$err->getMessage();
            return 0;
        }
    }

    public function __construct($host='',$user='',$passwd='',$database='')
    {
        $this->host=$host;
        $this->user=$user;
        $this->passwd=$passwd;
        $this->database=$database;
        if(!empty($host)&&!empty($user)&&!empty($passwd)&&!empty($database))
            return $this->Link();
    }
}

?>