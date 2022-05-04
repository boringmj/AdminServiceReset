<?php

class PluginVerificationApiVerification
{
    protected $_ck_kid;      //验证id
    protected $_res_path;    //资源文件目录
    protected $_Database;    //数据库对象
    public $key='';          //Key
    public $expire_time=0;   //过期时间

    public function SetDatabase(&$Database)
    {
        $this->_Database=&$Database;
    }

    public function StartCheck()
    {
        $ck_kid=get_rand_string_id();
        $ck_key=md5(get_rand_string(32));
        $expire_time=time()+$this->expire_time;
        $ck_token=md5(REQUEST_IP.$this->key.REQUEST_FORWARDED."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
        setcookie('ck_kid',$ck_kid,$expire_time);
        setcookie('ck_token',$ck_token,$expire_time);
        setcookie('expire_time',$expire_time,$expire_time);
        return $javascript_code=$this->_GetRandScript($ck_key);
    }

    protected function _GetRandScript($string)
    {
        //来至我之前随便写的一个小脚本 https://github.com/boringmj/php_codeScript
        $string_length=strlen($string);
        $string_array_string=array();
        //这里作为索引必须要为整型(Int)
        $string_length_count=(int)ceil($string_length/10);
        //控制分组不能超过10组
        for($i=0;$i<10;$i++)
        {
            $temp_string="";
            for($j=0;$j<$string_length_count;$j++)
            {
                $temp_count=$i*$string_length_count+$j;
                if($temp_count>=$string_length)
                    break;
                $temp_string.=$string[$temp_count];
            }
            $string_array_string[]=array(rand(0,2),$temp_string);
        }
        $string_variable=get_rand_string(6,'xXoOpPqQ_iIlLvVwWmM'); 
        $string_count=0;
        $string_return="";
        $string_array_return=array();
        foreach($string_array_string as $value_array)
        {
            $string_count++;
            $temp_rand=mt_rand(0,1);
            $variable_name=get_rand_string(1,'xXoOpPqQ_iIlLvVwWmM').get_rand_string(3,'1234567890xXoOpPqQ_iIlLvVwWmM').$string_count.get_rand_string(3,'1234567890xXoOpPqQ_iIlLvVwWmM');
            if($temp_rand)
            {
                //处理之前遗留和这的次字符组
                foreach($string_array_return as $value_temp_array)
                {
                    $temp_string="";
                    if($value_temp_array[0]==1)
                        $temp_string.="+{$value_temp_array[1]}";
                    else if($value_temp_array[0]==2)
                        $temp_string.="+{$value_temp_array[1]}()";
                    else
                        $temp_string.="+\"{$value_temp_array[1]}\"";
                    if(!empty($temp_string))
                    {
                        $string_return.="{$string_variable}={$string_variable}{$temp_string};";
                        $string_array_return=array();
                    }
                }
                if($value_array[0]==1)
                    $string_return.="var {$variable_name}=\"{$value_array[1]}\";{$string_variable}+={$variable_name};";
                else if($value_array[0]==2)
                    $string_return.="var {$variable_name}=function(){return \"{$value_array[1]}\";};{$string_variable}+={$variable_name}();";
                else
                    $string_return.="{$string_variable}+=\"{$value_array[1]}\";";
            }
            else
            {
                if($value_array[0]==1)
                    $string_return.="var {$variable_name}=\"{$value_array[1]}\";";
                else if($value_array[0]==2)
                    $string_return.="var {$variable_name}=function(){return \"{$value_array[1]}\";};";
                else
                    $variable_name=$value_array[1];
                $string_array_return[]=array($value_array[0],$variable_name);
            }
        }
        foreach($string_array_return as $value_temp_array)
        {
            $temp_string="";
            if($value_temp_array[0]==1)
                $temp_string.="+{$value_temp_array[1]}";
            else if($value_temp_array[0]==2)
                $temp_string.="+{$value_temp_array[1]}()";
            else
                $temp_string.="+\"{$value_temp_array[1]}\"";
            if(!empty($temp_string))
            {
                $string_return.="{$string_variable}={$string_variable}{$temp_string};";
                $string_array_return=array();
            }
        }
        $string_return="var {$string_variable}=\"\";{$string_return}return \"".CONFIG_REQUEST_URL."/?from=verification&type=plugin&ck_key=\"+($string_variable);";
        return $string_return;
    }

    public function __construct(&$Database)
    {
        $this->_Database=&$Database;
    }
}

?>