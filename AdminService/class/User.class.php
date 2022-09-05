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

    /**
     * 设置数据库对象
     * 
     * @param Database $Database 数据库对象
     * @return void
     */
    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

    /**
     * 设置用户加密盐
     * 
     * @param string $salt 用户加密盐
     * @return void
     */
    public function SetUserSalt($salt)
    {
        $this->user_salt=$salt;
    }

    /**
     * 获取PDO调试错误信息
     * 
     * @return mixed
     */
    public function DebugGetPdoError()
    {
        return $this->_Database->object->errorInfo();
    }

    /**
     * 添加一个新用户(如果成功返回用户UUID,否则返回0)
     * 
     * @param string $user 用户名
     * @param string $nickname 昵称
     * @param string $password 密码
     * @param string $email 邮箱
     * @return boolean|string
     */
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

    /**
     * 添加一个新用户组(如果成功返回用户组ID,否则返回0)
     * 
     * @param string $group_name 用户组名
     * @param string $group_level 用户组级别
     * @return boolean|string
     */
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

    /**
     * 添加一个用户的身份令牌(如果成功返回身份令牌ID,否则返回0)
     * 
     * @param string $uuid 用户UUID
     * @return boolean|string
     */
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

    /**
     * 通过UUID删除用户
     * 
     * @param string $uuid 用户UUID
     * @return boolean
     */
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
    
    /**
     * 通过用户名删除用户
     * 
     * @param string $user_name 用户名
     * @return boolean
     */
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

    /**
     * 通过用户组ID删除用户组
     * 
     * @param string $group_id 用户组ID
     * @return boolean
     */
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

    /**
     * 删除所有过期未激活用户
     * 
     * @return boolean
     */
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

    /**
     * 删除用户所有身份令牌
     * 
     * @param string $uuid 用户UUID
     * @return boolean
     */
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

    /**
     * 删除所有过期的身份令牌
     * 
     * @return boolean
     */
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

    /**
     * 通过ID检查用户组是否存在
     * 
     * @param string $id 用户组ID
     * @return boolean
     */
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

    /**
     * 通过用户名和密码检查用户是否存在(需要用户处于正常状态),常用于登录检查
     * 
     * @param string $user 用户名
     * @param string $password 密码
     * @return string|null
     */
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

    /**
     * 通过用户名和邮箱检查用户是否存在
     * 
     * @param string $user 用户名
     * @param string $email 邮箱
     * @return boolean
     */
    public function CheckUserExist($user,$email)
    {
        $tab_name=$this->_Database->GetTablename('system_user');
        $sql_statement=$this->_Database->object->prepare("SELECT `id` FROM {$tab_name} WHERE `app_id`=:app_id AND (`user_name`=:user_name OR `email`=:email)");
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

    /**
     * 检查用户身份令牌是否存在
     * 
     * @param string $uuid 用户UUID
     * @param string $token 身份令牌
     * @return boolean
     */
    public function CheckUserToken($uuid,$token)
    {
        $tab_name=$this->_Database->GetTablename('user_token');
        $sql_statement=$this->_Database->object->prepare("SELECT `id` FROM {$tab_name} WHERE `uuid`=:uuid AND `token`=:token AND `expire_time`>=:expire_time");
        $expire_time=time();
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':token',$token);
        $sql_statement->bindParam(':expire_time',$expire_time);
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

    /**
     * 重置用户身份令牌过期时间
     * 
     * @param string $uuid 用户UUID
     * @param string $token 身份令牌
     * @return boolean
     */
    public function ResetUserTokenTime($uuid,$token)
    {
        $tab_name=$this->_Database->GetTablename('user_token');
        $sql_statement=$this->_Database->object->prepare("UPDATE {$tab_name} SET `expire_time`=:expire_time WHERE `uuid`=:uuid AND `token`=:token");
        $expire_time=time()+CONFIG_SECURITY_USER_TOKEN_TIME;
        $sql_statement->bindParam(':uuid',$uuid);
        $sql_statement->bindParam(':token',$token);
        $sql_statement->bindParam(':expire_time',$expire_time);
        if($sql_statement->execute())
            return 1;
        else
            return 0;
    }

    /**
     * 通过身份令牌获取用户信息
     * 
     * @param string $uuid 身份令牌
     * @return null|array
     */
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

    /**
     * 通过用户名获取用户信息
     * 
     * @param string $user 用户名
     * @return null|array
     */
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

    /**
     * 通过邮箱获取用户信息
     * 
     * @param string $email 邮箱
     * @return null|array
     */
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

    /**
     * 通过ID获取用户组信息
     * 
     * @param string $id 用户组ID
     * @return null|array
     */
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

    /**
     * 通过UUId设置用户状态
     * 
     * @param string $uuid 用户UUID
     * @param string $status 用户状态
     * @return boolean
     */
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

    /**
     * 通过UUID设置用户昵称
     * 
     * @param string $uuid 用户UUID
     * @param string $nickname 用户昵称
     * @return boolean
     */
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

    /**
     * 通过UUID设置用户密码
     * 
     * @param string $uuid 用户UUID
     * @param string $password 用户密码
     * @return boolean
     */
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

    /**
     * 构造函数
     * 
     * @param string $app_id 应用ID
     * @param string $user_salt 用户密码加盐
     * @return void
     */
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