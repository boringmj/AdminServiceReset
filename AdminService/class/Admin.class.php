<?php

class Admin
{
    public $system_id;      //stid
    public $system_key;     //skey

    protected $_Database;   //数据库对象

    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

    public function CreatePlatform()
    {
        
    }

    public function __construct($system_id='',$system_key='')
    {
        $this->system_id=$system_id;
        $this->system_key=$system_key;
    }
}

?>