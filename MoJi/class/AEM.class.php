<?php

class AEM
{
    /**
     * 创建秘钥文件(含秘钥和iv)
     * 
     * @param string $path 秘钥文件路径
     * @return boolean
     */
    static function CreateKeyFile($path)
    {
        $cipher='AES-128-CBC';
        if(!is_writeable(dirname($path))||!in_array($cipher,openssl_get_cipher_methods()))
            return false;
        $key=openssl_random_pseudo_bytes(32);
        $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        file_put_contents($path,bin2hex($key)."\n".bin2hex($iv));
        return true;
    }
    
    /**
     * 通过文件名获取秘钥和iv
     * 
     * @param string $path 秘钥文件路径
     * @return array
     */
    static function GetKeyFile($path)
    {
        $key_array=array();
        $cipher='AES-128-CBC';
        if(in_array($cipher,openssl_get_cipher_methods()))
        {
            $file=file_get_contents($path);
            $file_array=explode("\n",$file);
            $key_array['key']=hex2bin($file_array[0]);
            $key_array['iv']=hex2bin($file_array[1]);
        }
        return $key_array;
    }

    /**
     * AEM加密
     * 
     * @param string $str 待加密字符串
     * @param string $key 秘钥
     * @param string $iv iv
     * @return string
     */
    static function Encrypt($str,$key,$iv)
    {
        $encode_text='';
        $cipher='AES-128-CBC';
        if(in_array($cipher,openssl_get_cipher_methods()))
            $encode_text=openssl_encrypt(bin2hex($str),$cipher,$key,0,$iv);
        return $encode_text;
    }

    /**
     * AEM解密
     * 
     * @param string $str 待解密字符串
     * @param string $key 秘钥
     * @param string $iv iv
     * @return string
     */
    static function Decrypt($str,$key,$iv)
    {
        $decode_text='';
        $cipher='AES-128-CBC';
        if(in_array($cipher,openssl_get_cipher_methods()))
            $decode_text=hex2bin(openssl_decrypt($str,$cipher,$key,0,$iv));
        return $decode_text;
    }
}

?>