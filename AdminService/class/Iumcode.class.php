<?php

/** Iumcode
 * 该类继承于前一个项目,略微修改
 * 请不要尝试修改任何代码
 */

class Iumcode
{
    /**
     * 加密解密核心
     * 
     * @param string $str 加密字符串
     * @param string $key 加密密钥
     * @return string
     */
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

    /**
     * IUMCODE加密
     * 
     * @param string $str 加密字符串
     * @param string $key 加密密钥
     * @return string
     */
    static public function EncodeIum($str,$key)
    {
        return base64_encode(self::EncodeBase(base64_encode($str),$key));
    }

    /**
     * IUMCODE解密
     * 
     * @param string $str 解密字符串
     * @param string $key 解密密钥
     * @return string
     */
    static public function DecodeIum($str,$key)
    {
        return base64_decode(self::EncodeBase(base64_decode($str),$key));
    }
}

?>