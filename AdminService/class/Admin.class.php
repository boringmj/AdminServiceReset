<?php

class Admin
{
    public $app_id;
    public $app_key;

    protected $_Database;   //数据库对象

    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

     static public function CreatePlatform(&$_Database)
    {
        $server_timestamp=time();
        //随机生成app_id和app_key
        $app_id=get_rand_string(22).time();
        $app_key=md5(get_rand_string(32));
        //插入数据
        $table_name=$_Database->GetTablename('system_info');
        $sql_statement=$_Database->object->prepare("INSERT INTO {$table_name}(`app_id`,`app_key`,`timestamp`) VALUES (:app_id,:app_key,:timestamp)");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':app_key',$app_key);
        $sql_statement->bindParam(':timestamp',$server_timestamp);
        if($sql_statement->execute())
            return array('app_id'=>$app_id,'app_key'=>$app_key);
        else
            return 0;
    }

    static public function GetAppKey(&$_Database,$app_id)
    {
        $table_name=$_Database->GetTablename('system_info');
        $sql_statement=$_Database->object->prepare("SELECT `app_key` FROM {$table_name} WHERE `app_id`=:app_id ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$app_id);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['app_key']))
                return $result_sql_temp['app_key'];
        }
        return '';
    }

    static public function CheckNonce(&$_Database,$app_id,$nonce,$sign)
    {
        $table_name=$_Database->GetTablename('ststem_nonce');
        $sql_statement=$_Database->object->prepare("SELECT `nonce`,`sign` FROM {$table_name} WHERE `app_id`=:app_id AND `nonce`=:nonce AND `sign`=:sign ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':nonce',$nonce);
        $sql_statement->bindParam(':sign',$sign);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(empty($result_sql_temp['nonce']))
                return true;
        }
        return false;
    }

    public function __construct($app_id='',$app_key='')
    {
        $this->app_id=$app_id;
        $this->app_key=$app_key;
    }
}

?>