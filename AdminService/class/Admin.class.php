<?php

class Admin
{
    public $system_id;      //stid
    public $system_key;     //skey

    protected $Database;    //数据库对象

    public function SetDatabase($Database)
    {
        $this->Database=&$Database;
    }

    public function CreatePlatform()
    {
        
    }

    public function __construct($Database='',$system_id='',$system_key='')
    {
        if(!empty($Database))
            $this->Database=&$Database;
        $this->system_id=$system_id;
        $this->system_key=$system_key;
    }
}

?>