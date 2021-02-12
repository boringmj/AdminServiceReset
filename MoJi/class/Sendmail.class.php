<?php

class Sendmail
{
    protected $_smtp_port=465;      //smtp端口(可能需要放行该端口)
    protected $_smtp_host='';       //smtp地址
    protected $_smtp_user='';       //smtp用户(邮箱用户名)
    protected $_smtp_pass='';       //smtp密码或授权码
    protected $_from_email='';      //发送邮件的电子邮箱
    protected $_from_name='';       //发件邮箱的昵称
    protected $_reply_email='';     //回复邮箱(一般为空)
    protected $_reply_name='';      //回复昵称(一般为空)
    protected $_ssl=false;          //是否打开ssl
    public $error_info=array();     //错误信息

    //设置smtp端口
    public function SetSmtpPort($smtp_port)
    {
        $this->_smtp_port=$smtp_port;
    }

    //设置smtp主机地址
    public function SetSmtpHost($smtp_host)
    {
        $this->_smtp_host=$smtp_host;
    }

    //设置smtp用户
    public function SetSmtpUser($smtp_user)
    {
        $this->_smtp_user=$smtp_user;
    }

    //设置smtp密码
    public function SetSmtpPass($smtp_pass)
    {
        $this->_smtp_pass=$smtp_pass;
    }

    //设置发送邮箱
    public function SetFromEmail($from_email)
    {
        $this->_from_email=$from_email;
    }

    //设置发送昵称
    public function SetFromName($from_name)
    {
        $this->_from_name=$from_name;
    }

    //设置回复邮箱
    public function SetReplyEmail($reply_email)
    {
        $this->_reply_email=$reply_email;
    }

    //设置回复昵称
    public function SetReplyName($reply_name)
    {
        $this->_reply_name=$reply_name;
    }

    //设置是否打开ssl
    public function SetSsl($ssl)
    {
        $this->_ssl=$ssl;
    }

    //整体设置smtp信息
    public function SetSmtpConfig($smtp_port,$smtp_host,$smtp_user,$smtp_pass)
    {
        $this->_smtp_port=$smtp_port;
        $this->_smtp_host=$smtp_host;
        $this->_smtp_user=$smtp_user;
        $this->_smtp_pass=$smtp_pass;
    }

    //整体设置发送邮箱信息
    public function SetFromConfig($from_email,$from_name)
    {
        $this->_from_email=$from_email;
        $this->_from_name=$from_name;
    }

    //整体设置回复邮箱信息
    public function SetReplyConfig($reply_email,$reply_name)
    {
        $this->_reply_email=$reply_email;
        $this->_reply_name=$reply_name;
    }

    //发送邮件至邮箱
    public function Send($title,$content,$to_email,$to_name='')
    {
        if($this->_Check())
        {
            $PHPMailer=new PHPMailer();                         //PHPMailer对象
            $attachment='';                                     //多个附件使用数组           
            $PHPMailer->CharSet='UTF-8';                        //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
            $PHPMailer->IsSMTP();                               //设定使用SMTP服务
            $PHPMailer->IsHTML(true);                           //html邮件
            $PHPMailer->SMTPDebug=0;                            //关闭SMTP调试功能 1=errors and messages2=messages only
            $PHPMailer->SMTPAuth=true;                          //启用 SMTP 验证功能
            if($this->_ssl)
                $PHPMailer->SMTPSecure='ssl';                   //使用安全协议
            $PHPMailer->Host=$this->_smtp_host;                 //SMTP 服务器
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
            if($PHPMailer->Send())
            {
                return true;
            }
            else
            {
                $this->error_info['SendMail']=$PHPMailer->ErrorInfo;
                return false;
            }
        }
    }

    //检查是否配置完成
    protected function _Check()
    {
        //自动为QQ邮箱或腾讯企业邮箱的465端口设置为ssl方式
        if($this->_smtp_port==465&&($htis->_smtp_host='smtp.qq.com'||$htis->_smtp_host='smtp.exmail.qq.com'))
            $this->_ssl=true;
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
        if(empty($this->_reply_email))
            return false;
        if(empty($this->_reply_name))
            return false;
        //检查依赖
        if(class_exists('PHPMailer'))
            return true;
        else
            return false;
    }

}

?>