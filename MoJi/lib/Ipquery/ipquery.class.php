<?php

//基于 http://ip-api.com/ 网页提供的Api接口实现IP查询

class Ipquery
{
    public function Get($ip)
    {
        $headerArray =array("Content-type:application/json;","Accept:application/json");
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://ip-api.com/json/{$ip}?lang=zh-CN");
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output=curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

?>