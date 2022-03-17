<?php

/** 一般规范
 * 我们规定,main.php中应该有且只有 info.json->Main 中注册的主类
 * 如果需要自定义类,请将类的类名使用 主类名称+自定义类名称 命名,并存放在插件的src目录或自定义目录中
 * 主类请务必保留 Start(&$Database,$data_path,$config) 方法,且不可修改其形参
 * 主类的 Init(&$Database,$data_path) 方法请使用 公共静态(static public) 修饰,且同样需要保留,也不可修改其形参
 * 主类中不必要的方法允许删除,但请注意上两条
 * 三个保留字段请保留
 * 插件数据请存放入规定的 数据存放目录(protected $_data_path)
 */

//请注意类名需要与 info.json->Main 保持一致,且符合命名规范
class PluginVerificationApi
{
    protected $_Database;       //数据库对象
    protected $_data_path;      //数据存放目录
    protected $_config;         //程序配置数据

    //默认调用的方法,请保留该方法且不可修改形参
    public function Start(&$Database,$data_path,$config)
    {
        $this->_Database=$Database;
        $this->_data_path=$data_path;
        $this->_config=$config;
        include __DIR__.'/../lib/PluginVerificationApiVerification.class.php';
    }

    //插件被初始化,请使用公共静态修饰,请保留该方法且不可修改形参
    static public function Init(&$Database,$data_path)
    {
        /** 事件说明
         * 这里是是给开发者预留的事件,常用于初始化插件或者安装内容
         * 安装状态和内容可以输出到数据存放目录内
         * 下面是一段存放数据代码
         * 本方法请使用公共静态修饰且需要保留
         * 不可修改本方法形参
         * 请注意不要使用 $this
         */

        $plugin_data=$data_path.'/../com.verification.api.data.json';
        $plugin_data_json=json_decode(file_get_contents($plugin_data));
        if($plugin_data_json->Program->User->AutoKey->Options->Default[1])
            $plugin_data_json->Program->User->Key->Options->Text=get_rand_string(32);
        $plugin_data_json->System->State=true;
        file_put_contents($plugin_data,json_encode($plugin_data_json));
    }

    //接口安全
    public function ApiSecurity()
    {
        //目前先挖一个坑,以后补
    }

    //页面安全
    public function ViewSecurity()
    {
        if($_REQUEST["from"]=="verification")
        {
            if(!empty($_GET['ck_key']))
            {
                $ck_kid=isset($_COOKIE['ck_kid'])?$_COOKIE['ck_kid']:"";
                $ck_key=isset($_GET['ck_key'])?$_GET['ck_key']:"";
                $expire_time=isset($_COOKIE['expire_time'])?$_COOKIE['expire_time']:"";
                $ck_token=md5(REQUEST_IP.$this->_config->User->Key->Options->Text.REQUEST_FORWARDED."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
                if($ck_token!=(isset($_COOKIE['ck_token'])?$_COOKIE['ck_token']:"")||time()>$expire_time)
                    exit("验证失败或请求已过期!<br>如果您禁止了Cookie,我们将无法为您正常提供服务");
                setcookie('ck_key',$ck_key,$expire_time);
                header("Location: ".CONFIG_REQUEST_URL.(empty($_COOKIE['url'])?"/":$_COOKIE['url']));
                exit();
            }
            else
            {
                $javascript_code=file_get_contents(__DIR__.'/../res/verification.js');
                $javascript_script=new PluginVerificationApiVerification($Database);
                $javascript_script->key=$this->_config->User->Key->Options->Text;
                $javascript_script->expire_time=$this->_config->User->Expiration->Options->Text;
                $javascript_tmp=$javascript_script->StartCheck();
                $content_array=array(
                    'javascript_script'=>$javascript_tmp
                );
                echo "<html><script>";
                echo javascript_encode(variable_load($content_array,$javascript_code));
                echo "</script></html>";
                exit();
            }
        }
        else
        {
            //环境补偿,用于 Php 不符合配置要求的情况(仅补偿Cookie)
            if(isset($_COOKIE['ck_token']))
            $_REQUEST['ck_token']=$_COOKIE['ck_token'];
            if(isset($_COOKIE['ck_kid']))
            $_REQUEST['ck_kid']=$_COOKIE['ck_kid'];
            if(isset($_COOKIE['ck_key']))
            $_REQUEST['ck_key']=$_COOKIE['ck_key'];
            if(isset($_COOKIE['expire_time']))
            $_REQUEST['expire_time']=$_COOKIE['expire_time'];
            $request_url=REQUEST_URI;
            //我想了又想,最终决定还是基于 $_REQUEST 接收参数
            if(empty($_REQUEST['ck_token'])||empty($_REQUEST['ck_kid'])||empty($_REQUEST['ck_key'])||empty($_REQUEST['expire_time']))
            {
                header("Location: ".CONFIG_REQUEST_URL."/?from=verification");
                setcookie("url",$request_url);
                exit();
            }
            //开始验证结果
            $ck_kid=$_REQUEST['ck_kid'];
            $ck_key=$_REQUEST['ck_key'];
            $expire_time=$_REQUEST['expire_time'];
            $ck_token=md5(REQUEST_IP.$this->_config->User->Key->Options->Text.REQUEST_FORWARDED."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
            if($ck_token!=$_REQUEST['ck_token']||time()>$expire_time)
            {
                header("Location: ".CONFIG_REQUEST_URL."/?from=verification");
                setcookie("url",$request_url);
                exit();
            }
        }
    }
}

?>