<?php

class Security
{
    //记录IP登录次数
    public function RecordLogin()
    {
        //获取数据
        $data=$this->_GetData();
        //先检查是否需要重置
        $server_timestamp=time();
        if($server_timestamp-$data['login_total_time']>CONFIG_SECURITY_IP_LOGIN_TIME)
        {
            $data['login_total_time']=$server_timestamp;
            $data['login_total']=1;
        }
        else
        {
            $data['login_total']++;
        }
        if($server_timestamp-$data['login_error_time']>CONFIG_SECURITY_IP_LOGIN_ERROR_TIME)
        {
            $data['login_error_total']=$server_timestamp;
            $data['login_error_total']=0;
        }
        if($server_timestamp-$data['login_day_time']>60*60*24)
        {
            $data['login_day_time']=$server_timestamp;
            $data['login_day_total']=1;
        }
        else
        {
            $data['login_day_total']++;
        }
        //保存数据
        $this->_SaveData($data);
    }

    //记录IP登录错误次数
    public function RecordLoginError()
    {
        //获取数据
        $data=$this->_GetData();
        //先检查是否需要重置
        $server_timestamp=time();
        if($server_timestamp-$data['login_error_time']>CONFIG_SECURITY_IP_LOGIN_ERROR_TIME)
        {
            $data['login_error_total']=$server_timestamp;
            $data['login_error_total']=1;
        }
        else
        {
            $data['login_error_total']++;
        }
        //保存数据
        $this->_SaveData($data);
    }

    //记录IP注册次数
    public function RecordRegister()
    {
        //获取数据
        $data=$this->_GetData();
        //先检查是否需要重置
        $server_timestamp=time();
        if($server_timestamp-$data['register_time']>CONFIG_SECURITY_IP_REGISTER_TIME)
        {
            $data['register_time']=$server_timestamp;
            $data['register_total']=1;
        }
        else
        {
            $data['register_total']++;
        }
        //保存数据
        $this->_SaveData($data);
    }

    //检查登录
    public function CheckLogin()
    {
        $data=$this->_GetData();
        $server_timestamp=time();
        //先判断当前是否在锁定时间内
        if($data['lock_time']>$server_timestamp)
            return false;
        //判断每天登录次数是否超过限制
        if(CONFIG_SECURITY_IP_LOGIN_TOTAL_DAY!=0&&$data['login_day_total']>CONFIG_SECURITY_IP_LOGIN_TOTAL_DAY)
            return false;
        //判断登录次数是否超过限制
        if(CONFIG_SECURITY_IP_LOGIN_THRESHOLD!=0&&$data['login_total']>CONFIG_SECURITY_IP_LOGIN_THRESHOLD)
        {
            //封禁IP
            $this->_BanIp();
            return false;
        }
        //判断登录错误次数是否超过限制
        if(CONFIG_SECURITY_IP_LOGIN_ERROR_THRESHOLD!=0&&$data['login_error_total']>CONFIG_SECURITY_IP_LOGIN_ERROR_THRESHOLD)
        {
            //封禁IP
            $this->_BanIp();
            return false;
        }
        return true;
    }

    //检查注册
    public function CheckRegister()
    {
        $data=$this->_GetData();
        $server_timestamp=time();
        //先判断当前是否在锁定时间内
        if($data['lock_time']>$server_timestamp)
            return false;
        //判断注册次数是否超过限制
        if(CONFIG_SECURITY_IP_REGISTER_THRESHOLD!=0&&$data['register_total']>CONFIG_SECURITY_IP_REGISTER_THRESHOLD)
        {
            //封禁IP
            $this->_BanIp();
            return false;
        }
        return true;
    }

    private function _BanIp()
    {
        $data=$this->_GetData();
        $data['lock_time']=time()+CONFIG_SECURITY_IP_BAN_TIME;
        $this->_SaveData($data);
    }

    private function _GetData()
    {
        $path=CACHE_PATH.'/api_ip_'.md5(CONFIG_KEY_KEY.REQUEST_IP.CONFIG_USER_SALT.CONFIG_KEY_SALT).'.data.json';
        if(!file_exists($path))
            file_put_contents($path,json_encode(array()));
        $cache_data=json_decode(file_get_contents($path),true);
        $server_timestamp=time();
        //每天登录次数
        $login_day_total=isset($cache_data['login_day_total'])?$cache_data['login_day_total']:0;
        $login_day_time=isset($cache_data['login_day_time'])?$cache_data['login_day_time']:$server_timestamp;
        //登录次数
        $login_total=isset($cache_data['login_total'])?$cache_data['login_total']:0;
        $login_total_time=isset($cache_data['login_total_time'])?$cache_data['login_total_time']:$server_timestamp;
        //登录错误次数
        $login_error_total=isset($cache_data['login_error_total'])?$cache_data['login_error_total']:0;
        $login_error_time=isset($cache_data['login_error_time'])?$cache_data['login_error_time']:$server_timestamp;
        //注册次数
        $register_total=isset($cache_data['register_total'])?$cache_data['register_total']:0;
        $register_time=isset($cache_data['register_time'])?$cache_data['register_time']:$server_timestamp;
        //锁定时间
        $lock_time=isset($cache_data['lock_time'])?$cache_data['lock_time']:0;
        return array(
            'login_day_total'=>$login_day_total,
            'login_total'=>$login_total,
            'login_day_time'=>$login_day_time,
            'login_total_time'=>$login_total_time,
            'login_error_total'=>$login_error_total,
            'login_error_time'=>$login_error_time,
            'register_total'=>$register_total,
            'register_time'=>$register_time,
            'lock_time'=>$lock_time
        );
    }

    private function _SaveData($data)
    {
        $path=CACHE_PATH.'/api_ip_'.md5(CONFIG_KEY_KEY.REQUEST_IP.CONFIG_USER_SALT.CONFIG_KEY_SALT).'.data.json';
        file_put_contents($path,json_encode($data));
    }
}

?>