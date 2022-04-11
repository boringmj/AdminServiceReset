<?php

/* class User
* 
* 注意:
* 本类大部分方法由Copilot自动生成,仅进行过初步检查,可能存在一些问题,请自行检查
* 本类基于Moji框架,单独使用可能出现其他问题
*/

class User
{
    public $app_id;
    public $user_salt;
    protected $_Database;

    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

    public function SetUserSalt($salt)
    {
        $this->user_salt=$salt;
    }

    public function DebugGetPdoError()
    {
        return $this->_Database->object->errorInfo();
    }

    public function AddUser($user,$nickname,$password,$email)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("INSERT INTO {$tab_name}(`app_id`,`timestamp`,`uuid`,`user_name`,`password`,`nickname`,`user_group`,`email`,`head_portraits`,`status`) VALUES (:app_id,:timestamp,:uuid,:user_name,:password,:nickname,:user_group,:email,:head_portraits,:status)");
        $timestamp=time();
        $uuid=get_rand_string_id();
        //默认用户组
        $user_group=CONFIG_USER_USER_DEFAULT_GROUP_ID;
        $status=CONFIG_USER_USER_DEFAULT_STATUS;
        $head_portraits=CONFIG_USER_HEAD_PORTRAIT;
        $password=md5($password.$this->user_salt);
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':timestamp',$timestamp);
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':user_name',$user);
        $sql_statement->bindParam(':password',$password);
        $sql_statement->bindParam(':nickname',$nickname);
        $sql_statement->bindParam(':user_group',$user_group);
        $sql_statement->bindParam(':email',$email);
        $sql_statement->bindParam(':head_portraits',$head_portraits);
        $sql_statement->bindParam(':status',$status);
        if($sql_statement->execute())
            return $uuid;
        else
            return 0;
    }

    public function AddUserGroup($group_name,$group_level)
    {
        $tab_name=$this->_Database->GetTablename('system_user_group');
        $sql_statement=$this->_Database->object->prepare("INSERT INTO {$tab_name}(`app_id`,`timestamp`,`group_name`,`group_level`) VALUES (:app_id,:timestamp,:group_name,:group_level)");
        $timestamp=time();
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':timestamp',$timestamp);
        $sql_statement->bindParam(':group_name',$group_name);
        $sql_statement->bindParam(':group_level',$group_level);
        if($sql_statement->execute())
            return $this->_Database->object->lastInsertId();
        else
            return 0;
    }

    public function AddUserToken($uuid)
    {
        $tab_name=$this->_Database->GetTablename('user_token');
        $sql_statement=$this->_Database->object->prepare("INSERT INTO {$tab_name}(`app_id`,`timestamp`,`uuid`,`token`,`expire_time`) VALUES (:app_id,:timestamp,:uuid,:token,:expire_time)");
        $timestamp=time();
        $token=get_rand_string_id();
        $expire_time=time()+CONFIG_SECURITY_USER_TOKEN_TIME;
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':timestamp',$timestamp);
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':token',$token);
        $sql_statement->bindParam(':expire_time',$expire_time);
        if($sql_statement->execute())
            return $token;
        else
            return 0;
    }

    public function RemoveUser($uuid)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE uuid=:uuid");
        $sql_statement->bindParam(':uuid',$uuid);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }
    
    public function RemoveUserByUserName($user_name)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE user_name=:user_name");
        $sql_statement->bindParam(':user_name',$user_name);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function RemoveGroup($id)
    {
        $tab_name=$this->_Database->GetTablename('system_user_group');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE id=:id");
        $sql_statement->bindParam(':id',$id);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function RemoveExpiredUser()
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE timestamp<:timestamp AND status=2");
        $timestamp=time()-CONFIG_USER_VERIFY_OVERDUE_TIME;
        $sql_statement->bindParam(':timestamp',$timestamp);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function RemoveUserToken($uuid)
    {
        $tab_name=$this->_Database->GetTablename('user_token');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE uuid=:uuid");
        $sql_statement->bindParam(':uuid',$uuid);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function RemoveExpiredToken()
    {
        $tab_name=$this->_Database->GetTablename('user_token');
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$tab_name} WHERE expire_time<:expire_time");
        $expire_time=time();
        $sql_statement->bindParam(':expire_time',$expire_time);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function CheckUserGroup($id)
    {
        $tab_name=$this->_Database->GetTablename('system_user_group');
        $sql_statement=$this->_Database->object->prepare("SELECT `id` FROM {$tab_name} WHERE `app_id`=:app_id AND `id`=:id");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':id',$id);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['id']))
                return 1;
            else
                return 0;
        }
        else
            return 0;
    }

    public function CheckUser($user,$password)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        //只有状态为1的用户才允许
        $sql_statement=$this->_Database->object->prepare("SELECT `uuid` FROM {$tab_name} WHERE `app_id`=:app_id AND `user_name`=:user_name AND `password`=:password AND `status`=1 ORDER BY `id` DESC LIMIT 0,1");
        $password=md5($password.$this->user_salt);
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':user_name',$user);
        $sql_statement->bindParam(':password',$password);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['uuid']))
                return $result_sql_temp['uuid'];
        }
        return '';
    }

    public function CheckUserExist($user,$email)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("SELECT `id` FROM {$tab_name} WHERE `app_id`=:app_id AND `user_name`=:user_name OR `email`=:email");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':user_name',$user);
        $sql_statement->bindParam(':email',$email);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['id']))
                return 1;
            else
                return 0;
        }
        else
            return 0;
    }

    public function GetUserInfo($uuid)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("SELECT * FROM {$tab_name} WHERE `app_id`=:app_id AND `uuid`=:uuid ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':uuid',$uuid);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['uuid']))
                return $result_sql_temp;
        }
        return '';
    }

    public function GetUserInfoByUser($user)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("SELECT * FROM {$tab_name} WHERE `app_id`=:app_id AND `user_name`=:user_name ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':user_name',$user);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['uuid']))
                return $result_sql_temp;
        }
        return '';
    }

    public function GetUserInfoByEmail($email)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("SELECT * FROM {$tab_name} WHERE `app_id`=:app_id AND `email`=:email ORDER BY `id` DESC LIMIT 0,1");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':email',$email);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['uuid']))
                return $result_sql_temp;
        }
        return '';
    }

    public function GetUserGroup($id)
    {
        $tab_name=$this->_Database->GetTablename('system_user_group');
        $sql_statement=$this->_Database->object->prepare("SELECT `group_name`,`group_level` FROM {$tab_name} WHERE `app_id`=:app_id AND `id`=:id");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':id',$id);
        if($sql_statement->execute())
        {
            $result_sql_temp=$sql_statement->fetch();
            if(!empty($result_sql_temp['group_name']))
                return $result_sql_temp;
        }
        return '';
    }

    public function SetUserStatus($uuid,$status)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("UPDATE {$tab_name} SET `status`=:status WHERE `app_id`=:app_id AND `uuid`=:uuid");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':status',$status);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function SetUserNickname($uuid,$nickname)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("UPDATE {$tab_name} SET `nickname`=:nickname WHERE `app_id`=:app_id AND `uuid`=:uuid");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':nickname',$nickname);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function SetUserPassword($uuid,$password)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("UPDATE {$tab_name} SET `password`=:password WHERE `app_id`=:app_id AND `uuid`=:uuid");
        $sql_statement->bindParam(':app_id',$this->app_id);
        $sql_statement->bindParam(':uuid',$uuid);
        $password=md5($password.$this->user_salt);
        $sql_statement->bindParam(':password',$password);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    public function __construct($app_id='',$user_salt='')
    {
        if(!empty($app_id))
            $this->app_id=$app_id;
        if(!empty($user_salt))
            $this->user_salt=$user_salt;
        else
            $this->user_salt=CONFIG_USER_SALT;
        if(empty($app_id)&&isset($_REQUEST['app_id']))
            $this->app_id=$_REQUEST['app_id'];
    }
}

?>