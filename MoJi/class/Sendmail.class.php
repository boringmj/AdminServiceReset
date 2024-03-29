<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Sendmail
{
    protected $_smtp_port;       //smtp端口(可能需要放行该端口)
    protected $_smtp_host;       //smtp地址
    protected $_smtp_user;       //smtp用户(邮箱用户名)
    protected $_smtp_pass;       //smtp密码或授权码
    protected $_from_email;      //发送邮件的电子邮箱
    protected $_from_name;       //发件邮箱的昵称
    protected $_reply_email;     //回复邮箱(一般为空)
    protected $_reply_name;      //回复昵称(一般为空)
    protected $_ssl;             //是否打开ssl
    public $error_info;          //错误信息

    /**
     * 设置smtp端口
     * 
     * @param int $smtp_port smtp端口
     * @return void
     */
    public function SetSmtpPort($smtp_port)
    {
        $this->_smtp_port=$smtp_port;
    }

    /**
     * 设置smtp主机地址
     * 
     * @param string $smtp_host smtp主机地址
     * @return void
     */
    public function SetSmtpHost($smtp_host)
    {
        $this->_smtp_host=$smtp_host;
    }

    /**
     * 设置smtp用户名
     * 
     * @param string $smtp_user smtp用户名
     * @return void
     */
    public function SetSmtpUser($smtp_user)
    {
        $this->_smtp_user=$smtp_user;
    }

    /**
     * 设置smtp密码或授权码
     * 
     * @param string $smtp_pass smtp密码或授权码
     * @return void
     */
    public function SetSmtpPass($smtp_pass)
    {
        $this->_smtp_pass=$smtp_pass;
    }

    /**
     * 设置发件邮箱的电子邮箱
     * 
     * @param string $from_email 发件邮箱的电子邮箱
     * @return void
     */
    public function SetFromEmail($from_email)
    {
        $this->_from_email=$from_email;
    }

    /**
     * 设置发件邮箱的昵称
     * 
     * @param string $from_name 发件邮箱的昵称
     * @return void
     */
    public function SetFromName($from_name)
    {
        $this->_from_name=$from_name;
    }

    /**
     * 设置回复邮箱(一般为空)
     * 
     * @param string $reply_email 回复邮箱
     * @return void
     */
    public function SetReplyEmail($reply_email)
    {
        $this->_reply_email=$reply_email;
    }

    /**
     * 设置回复昵称(一般为空)
     * 
     * @param string $reply_name 回复昵称
     * @return void
     */
    public function SetReplyName($reply_name)
    {
        $this->_reply_name=$reply_name;
    }

    /**
     * 设置是否打开ssl
     * 
     * @param bool $ssl 是否打开ssl
     * @return void
     */
    public function SetSsl($ssl)
    {
        $this->_ssl=$ssl;
    }

    /**
     * 整体设置smtp信息
     * 
     * @param string $smtp_port smtp端口
     * @param string $smtp_host smtp主机地址
     * @param string $smtp_user smtp用户名
     * @param string $smtp_pass smtp密码或授权码
     * @return void
     */
    public function SetSmtpConfig($smtp_port,$smtp_host,$smtp_user,$smtp_pass)
    {
        $this->_smtp_port=$smtp_port;
        $this->_smtp_host=$smtp_host;
        $this->_smtp_user=$smtp_user;
        $this->_smtp_pass=$smtp_pass;
    }

    /**
     * 整体设置发件邮箱信息
     * 
     * @param string $from_email 发件邮箱的电子邮箱
     * @param string $from_name 发件邮箱的昵称
     * @return void
     */
    public function SetFromConfig($from_email,$from_name)
    {
        $this->_from_email=$from_email;
        $this->_from_name=$from_name;
    }

    /**
     * 整体设置回复邮箱信息
     * 
     * @param string $reply_email 回复邮箱
     * @param string $reply_name 回复昵称
     * @return void
     */
    public function SetReplyConfig($reply_email,$reply_name)
    {
        $this->_reply_email=$reply_email;
        $this->_reply_name=$reply_name;
    }

    /**
     * 发送邮件至邮箱
     * 
     * @param string $title 邮件标题
     * @param string $content 邮件内容
     * @param string $to_email 收件邮箱
     * @param string $to_name 收件昵称(可以为空)
     * @return bool
     */
    public function Send($title,$content,$to_email,$to_name='')
    {
        if($this->_Check())
        {
            $PHPMailer=new PHPMailer();                             //PHPMailer对象
            try
            {
                $attachment='';                                     //多个附件使用数组           
                $PHPMailer->CharSet='UTF-8';                        //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
                $PHPMailer->IsSMTP();                               //设定使用SMTP服务
                $PHPMailer->IsHTML(true);                           //html邮件
                $PHPMailer->SMTPDebug=0;                            //关闭SMTP调试功能 1=errors and messages2=messages only
                $PHPMailer->SMTPAuth=true;                          //启用 SMTP 验证功能
                if($this->_ssl)
                    $PHPMailer->SMTPSecure='ssl';                   //使用安全协议
                $PHPMailer->Host=$this->_smtp_host;                 //SMTP服务器
                $PHPMailer->Port=$this->_smtp_port;                 //SMTP服务器的端口号
                $PHPMailer->Username=$this->_smtp_user;             //SMTP服务器用户名
                $PHPMailer->Password=$this->_smtp_pass;             //SMTP服务器密码
                $PHPMailer->SetFrom($this->_from_email,$this->_from_name);
                $replyEmail=$this->_reply_email;
                $replyName=$this->_reply_name;
                $PHPMailer->AddReplyTo($replyEmail,$replyName);
                $PHPMailer->Subject=$title;
                $PHPMailer->MsgHTML($content);
                $PHPMailer->AddAddress($to_email,$to_name);
                if(is_array($attachment))
                { 
                    //添加附件
                    foreach ($attachment as $file)
                    {
                        if(is_array($file))
                            is_file($file['path'])&&$PHPMailer->AddAttachment($file['path'],$file['name']);
                        else
                            is_file($file)&&$PHPMailer->AddAttachment($file);
                    }
                }
                else
                {
                    is_file($attachment)&&$PHPMailer->AddAttachment($attachment);
                }
                $PHPMailer->Send();
                return true;
            }
            catch(Exception $err)
            {
                $this->error_info['SendMail']=$PHPMailer->ErrorInfo;
            }
        }
        else
        {
            $this->error_info['SendMail']='请完成配置!';
        }
    }

    /**
     * 检查配置是否完成
     * 
     * @return bool
     */
    protected function _Check()
    {
        if(empty($this->_smtp_port))
            return false;
        if(empty($this->_smtp_host))
            return false;
        if(empty($this->_smtp_user))
            return false;
        if(empty($this->_smtp_pass))
            return false;
        if(empty($this->_from_email))
            return false;
        if(empty($this->_from_name))
            return false;
        //自动为QQ邮箱或腾讯企业邮箱的465端口设置为ssl方式
        if($this->_smtp_port==465&&($this->_smtp_host=='smtp.qq.com'||$this->_smtp_host=='smtp.exmail.qq.com'))
            $this->_ssl=true;
        //检查依赖
        if(class_exists("PHPMailer\PHPMailer\Exception"))
            return true;
        else
            return false;
    }

    /**
     * 构造函数
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_smtp_port=465;
        $this->_smtp_host='smtp.exmail.qq.com';
        $this->_ssl=false;
        $this->error_info=array();
    }
}

?>