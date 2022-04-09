<?php

echo LANGUAGE_LOG_REQUEST_SUCCESS.'<br>';
echo "当前使用的语言: ".LANGUAGE_NAME;

/* $str="你好,世界!";
$pass="helloworld";
$cipher="AES-128-CBC";
if(in_array($cipher,openssl_get_cipher_methods()))
{
    $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encode_text=openssl_encrypt($str,$cipher,$pass,0,$iv);
    $decode_text=openssl_decrypt($encode_text,$cipher,$pass,0,$iv);
    echo "<br>加密前:{$str}<br>加密后:{$encode_text}<br>解密后:{$decode_text}<br>iv:{$iv}";
}
 */
?>