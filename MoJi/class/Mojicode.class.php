<?php

class Mojicode
{
    static public function GetKey($key_path)
    {
        $key='';
        if(is_file($key_path))
            $key=file_get_contents($key_path);
        return $key;
    }

    static public function CreateKey($str)
    {
        $key=md5(base64_encode(md5($str)));
        $ret='';
        $len=strlen($key);
        for($i=0;$i<$len;$i+=2)
        {
            $tmp_main_str=base64_encode(($key[$i]^$key[$i+1]).($key[$i]^$key[0]).($key[$i]^$key[$len-1]));
            $tmp_salt_str=mb_substr(md5($key[$i]),0,5);
            $ret.=$key[$i+1].mb_substr(base64_encode($tmp_salt_str),0,5).mb_substr($tmp_salt_str,0,2).$tmp_main_str.$key[$i];
        }
        return $ret;
    }
}

?>