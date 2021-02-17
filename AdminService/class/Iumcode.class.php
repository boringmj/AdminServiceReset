<?php

/** Iumcode
 * 该类继承于前一个项目,略微修改
 * 请不要尝试修改任何代码
 */

class Iumcode
{
    static public function EncodeBase($str,$key)
    {
        $ret='';
        $key=md5(base64_encode(md5($key)));
        $keylen=strlen($key);
        $len=strlen($str);
        for($i=0;$i<$len;$i++)
        {
            $k=$i%$keylen;
            $ret.=$str[$i]^$key[$k];
        }
        return $ret;
    }

    static public function EncodeIum($str,$key)
    {
        return base64_encode(self::EncodeBase(base64_encode($str),$key));
    }

    static public function DecodeIum($str,$key)
    {
        return base64_decode(self::EncodeBase(base64_decode($str),$key));
    }
}


?>