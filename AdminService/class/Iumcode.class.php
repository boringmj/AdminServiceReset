<?php

/** Iumcode
 * 该类继承于前一个项目,略微修改
 * 请不要尝试修改任何代码
 */

class Iumcode
{
    /*
    *简单的异或加密解密,秘钥长度不够会被循环使用
    */
    static public function XorEnc($str,$key)
    {
        //预定义结果
        $ret='';
        $keylen=strlen($key);
        for($i=0;$i<strlen($str);$i++)
        {
            $k=$i%$keylen;
            $ret.=$str[$i]^$key[$k];
        }
        return $ret;
    }

    /*
    *在异或的基础上加强加密强度,暂时取名叫IUM加密
    */
    static public function EncodeIum($str,$key)
    {
        return base64_encode(Iumcode::XorEnc(base64_encode($str),base64_encode(md5($key))));
    }

    /*
    *解密加密的数据
    */
    static public function DecodeIum($str,$key)
    {
        return base64_decode(Iumcode::XorEnc(base64_decode($str),base64_encode(md5($key))));
    }
}


?>