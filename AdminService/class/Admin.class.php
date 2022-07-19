<?php

class Admin
{
    public $app_id;
    public $app_key;

    protected $_Database;   //数据库对象

    /**
     * 设置数据库对象
     * 
     * @param object $Database 数据库对象
     * @return void
     */
    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

    /**
     * 创建一个新应用(新的访问令牌)
     * 
     * @param objiec $_Database 数据库对象
     * @return object
     */
    static public function CreatePlatform(&$_Database)
    {
        //注意,本方法不生成对应的用户组等其他信息,需要调用者自己生成
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

    /**
     * 获取应用秘钥
     * 
     * @param object $_Database 数据库对象
     * @param string $app_id 应用ID
     * @return string
     */
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

    /**
     * 检查随机数是否合法
     * 
     * @param object $_Database 数据库对象
     * @param string $app_id 应用ID
     * @param string $rand_string 随机数
     * @param string $sign 签名
     * @return boolean
     */
    static public function CheckNonce(&$_Database,$app_id,$nonce,$sign)
    {
        $table_name=$_Database->GetTablename('system_nonce');
        $sql_statement=$_Database->object->prepare("SELECT `timestamp` FROM {$table_name} WHERE `app_id`=:app_id AND `nonce`=:nonce AND `sign`=:sign ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':nonce',$nonce);
        $sql_statement->bindParam(':sign',$sign);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['timestamp'])&&$result_sql_temp['timestamp']+600>time())
                return false;
        }
        $timestamp=time();
        $sql_statement=$_Database->object->prepare("INSERT INTO {$table_name}(`app_id`,`timestamp`,`nonce`,`sign`) VALUES (:app_id,:timestamp,:nonce,:sign)");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':timestamp',$timestamp);
        $sql_statement->bindParam(':nonce',$nonce);
        $sql_statement->bindParam(':sign',$sign);
        $sql_statement->execute();
        return true;
    }

    /**
     * 构造函数
     * 
     * @param string $app_id 应用ID
     * @param string $app_key 应用秘钥
     * @return void
     */
    public function __construct($app_id='',$app_key='')
    {
        $this->app_id=$app_id;
        $this->app_key=$app_key;
    }
}

?>