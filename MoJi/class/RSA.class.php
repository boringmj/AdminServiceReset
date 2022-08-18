<?php

class RSA
{
    private $public_key_resource = '';  //公钥资源
    private $private_key_resource = ''; //私钥资源

    /**
     * 构造函数
     * 
     * @param string $public_key 公钥数据字符串
     * @param string $private_key 私钥数据字符串
     */
    public function __construct($public_key,$private_key)
    {
        $this->public_key_resource=!empty($public_key)?openssl_pkey_get_public($this->get_public_key($public_key)):false;
        $this->private_key_resource=!empty($private_key)?openssl_pkey_get_private($this->get_private_key($private_key)):false;
    }

    /**
     * 创建秘钥对
     * 
     * @param string $dir_path 秘钥存放目录(可写)
     * @param int $key_length 秘钥长度(默认:1024,可选:512,1024,2048)
     * @param string $cnf_path 秘钥配置文件路径(可选)
     * @return boolean
     */
    public function create_key($dir_path,$key_length=1024,$cnf_path=null)
    {
        if(!is_dir($dir_path)||!is_writable($dir_path))
            return false;
        if(!in_array($key_length,array(512,1024,2048)))
            $key_length=1024;
        $config=array(
            "digest_alg"        =>  "sha512",
            "private_key_bits"  =>  $key_length,
            "private_key_type"  =>  OPENSSL_KEYTYPE_RSA
        );
        if($cnf_path!=null&&is_file($cnf_path))
            $config['config']=$cnf_path;
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res,$privKey,null,$config);
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];
        file_put_contents("privkey.pem",$privKey);
        file_put_contents("pubkey.pem",$pubKey);
        return true;
    }

	/**
     * 获取私有key字符串 重新格式化 为保证任何key都可以识别
     * 
     * @param string $private_key 公钥数据字符串
     * @return string
	 */
	public function get_private_key($private_key)
    {
		$search = [
			"-----BEGIN RSA PRIVATE KEY-----",
			"-----END RSA PRIVATE KEY-----",
            "-----BEGIN PRIVATE KEY-----",
            "-----END PRIVATE KEY-----",
			"\n",
			"\r",
			"\r\n"
		];
		$private_key=str_replace($search,"",$private_key);
		return $search[0].PHP_EOL.wordwrap($private_key,64,"\n",true).PHP_EOL.$search[1];
	}

	/**
     * 获取公共key字符串 重新格式化 为保证任何key都可以识别
     * 
     * @param string $public_key 公钥数据字符串
     * @return string
	 */
	public function get_public_key($public_key)
    {
		$search = [
			"-----BEGIN PUBLIC KEY-----",
			"-----END PUBLIC KEY-----",
			"\n",
			"\r",
			"\r\n"
		];
		$public_key=str_replace($search,"",$public_key);
		return $search[0].PHP_EOL.wordwrap($public_key,64,"\n",true).PHP_EOL.$search[1];
	}

    /**
     * 用私钥加密
     * 
     * @param string $input 待加密数据
     * @return string
     */
    public function private_encrypt($input)
    {
        openssl_private_encrypt($input,$output,$this->private_key_resource);
        return base64_encode($output);
    }

    /**
     * 解密 私钥加密后的密文
     * 
     * @param string $input 加密后的密文
     * @return string
     */
    public function public_decrypt($input)
    {
        openssl_public_decrypt(base64_decode($input),$output,$this->public_key_resource);
        return $output;
    }

    /**
     * 用公钥加密
     * 
     * @param string $input 待加密的数据
     * @return string
     */
    public function public_encrypt($input)
    {
        openssl_public_encrypt($input,$output,$this->public_key_resource,OPENSSL_PKCS1_OAEP_PADDING);
        return base64_encode($output);
    }

    /**
     * 解密 公钥加密后的密文
     * 
     * @param string $input 加密后的密文
     * @return string
     */
    public function private_decrypt($input)
    {
        openssl_private_decrypt(base64_decode($input),$output,$this->private_key_resource,OPENSSL_PKCS1_OAEP_PADDING);
        return $output;
    }
}

?>