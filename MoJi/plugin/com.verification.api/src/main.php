<?php

/** 一般规范
 * 我们规定,main.php中应该有且只有 info.json->Main 中注册的主类
 * 如果需要自定义类,请将类的类名使用 主类名称+自定义类名称 命名,并存放在插件的src目录或自定义目录中
 * 主类请务必保留 Start(&$this->_Database,$data_path,$config) 方法,且不可修改其形参
 * 主类的 Init(&$this->_Database,$data_path) 方法请使用 公共静态(static public) 修饰,且同样需要保留,也不可修改其形参
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
        include __DIR__.'/../lib/PluginVerificationApiVerifyImg.class.php';
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

        //创建数据表
        $database_tables=array(
            'PluginVerificationApi_token'=>'(`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `ck_kid`          VARCHAR(36)   NOT NULL,
                `ck_key`          VARCHAR(32)   NOT NULL,
                `timestamp`       INT(10)       NOT NULL,
                `value`           VARCHAR(32)   NOT NULL,
                `number`          INT(10)       NOT NULL
            )',
        );
        foreach($database_tables as $table=>$info)
        {
            $table_name=$Database->GetTablename($table);
            $sql_statement=$Database->object->prepare("CREATE TABLE {$table_name}{$info}");
            $sql_statement->execute();
        }
    }

    //接口安全
    public function ApiSecurity()
    {
        //csrf新增验证内容,该项仅作为额外的验证手段,不作为核心使用,该项不是强制检查的,如果用户不能提供Referer(来源地址),则不会检查
        if(!empty($_SERVER['HTTP_REFERER']))
        {
            if(preg_match('/^(((https?|file):)?\/\/)?(?<host>[a-zA-Z0-9\.]+\.[a-zA-Z0-9]+)(:[0-9]+)?(\/)?.*$/',$_SERVER['HTTP_REFERER'],$matches))
            {
                //目前无法兼容localhost等方式访问,只能通过符合规则的ip或域名访问(域名不支持中文域名)
                $referer_host=$matches['host'];
                if($referer_host!=CONFIG_HTTP_HOST)
                {
                    $GLOBALS['return_data']=array(
                        'code'=>1025,
                        'msg'=>'非法请求',
                        'data'=>array()
                    );
                    echo_return_data();
                }
                //获取请求信息(优先通过Apache的方式获取请求头,如果失败使用nginx获取,如果其他服务没对该项提供支持,将无法通过验证)
                $request_headers=array();
                if(function_exists('apache_request_headers'))
                    $request_headers=apache_request_headers();
                $henader_info=array(
                    'Token'=>(empty($request_headers['Token'])?(empty($_SERVER['HTTP_TOKEN'])?'':$_SERVER['HTTP_TOKEN']):$request_headers['Token']),
                    'Token_Expire_Time'=>(empty($request_headers['Token_Expire_Time'])?(empty($_SERVER['HTTP_TOKEN_EXPIRE_TIME'])?'':$_SERVER['HTTP_TOKEN_EXPIRE_TIME']):$request_headers['Token_Expire_Time']),
                );
                $token_sign=(empty($request_headers['Token_Sign'])?(empty($_SERVER['HTTP_TOKEN_SIGN'])?'':$_SERVER['HTTP_TOKEN_SIGN']):$request_headers['Token_Sign']);
                unset($request_headers);
                //验证签名,签名通过视为token有效
                $sign=md5(sign($henader_info,$this->_config->User->Key->Options->Text).REQUEST_IP.REQUEST_FORWARDED);
                if($sign!=$token_sign)
                {
                    $GLOBALS['return_data']=array(
                        'code'=>1026,
                        'msg'=>'非法请求',
                        'data'=>array()
                    );
                    echo_return_data();
                }
                //验证Token是否已经过期
                if(empty($henader_info['Token_Expire_Time'])||time()>$henader_info['Token_Expire_Time'])
                {
                    $GLOBALS['return_data']=array(
                        'code'=>1027,
                        'msg'=>'令牌已过期',
                        'data'=>array()
                    );
                    echo_return_data();
                }
            }
            else
            {
                $GLOBALS['return_data']=array(
                    'code'=>1024,
                    'msg'=>'非法请求',
                    'data'=>array()
                );
                echo_return_data();
            }
        }
        //API图片验证码相关,以下代码均不验证存储是否成功
        if(!in_array($this->GetRequestInfo(),explode(',',$this->_config->User->NoVerification->Options->Text)))
            return;
        $table_name=$this->_Database->GetTablename('PluginVerificationApi_token');
        //删除过期的数据
        $sql_statement=$this->_Database->object->prepare("DELETE FROM {$table_name} WHERE timestamp<:timestamp OR number<=0");
        $expire_time=time()-$this->_config->User->Expiration->Options->Text;
        $sql_statement->bindValue(':timestamp',$expire_time);
        $sql_statement->execute();
        if($_REQUEST['from']=='verification_img'&&!empty($_REQUEST['ck_kid'])&&!empty($_REQUEST['ck_token']))
        {
            //验证请求是否存在
            $sql_statement=$this->_Database->object->prepare("SELECT * FROM {$table_name} WHERE `ck_kid`=:ck_kid AND `ck_key`=:ck_key");
            $sql_statement->bindParam(':ck_kid',$_REQUEST['ck_kid']);
            $sql_statement->bindParam(':ck_key',$_REQUEST['ck_token']);
            $sql_statement->execute();
            $result=$sql_statement->fetch();
            if(empty($result['timestamp']))
            {
                $GLOBALS['return_data']=array(
                    'code'=>1023,
                    'msg'=>'错误: 非法请求',
                    'data'=>array('from'=>$_REQUEST['from'])
                );
                echo_return_data();
            }
            if(!function_exists('imagecreate'))
            {
                $GLOBALS['return_data']=array(
                    'code'=>1022,
                    'msg'=>'错误: 暂不支持图片验证码',
                    'data'=>array('from'=>$_REQUEST['from'])
                );
                echo_return_data();
            }
            //生成两个随机数
            $rand_one=rand(1,9);
            $rand_two=rand(1,9);
            $value=-1;
            if($rand_one>$rand_two)
                $value=$rand_one-$rand_two;
            else
                $value=$rand_one+$rand_two;
            $table_name=$this->_Database->GetTablename('PluginVerificationApi_token');
            $sql_statement=$this->_Database->object->prepare("UPDATE {$table_name} SET `value`=:value WHERE `ck_kid`=:ck_kid");
            $sql_statement->bindParam(':ck_kid',$_REQUEST['ck_kid']);
            $sql_statement->bindParam(':value',$value);
            $sql_statement->execute();
            PluginVerificationApiVerifyImg::get($rand_one,$rand_two);
        }
        else if($_REQUEST['from']=='verification_get')
        {
            $expire_time=time()+$this->_config->User->Expiration->Options->Text;
            $ck_kid=get_rand_string_id();
            $ck_key=$this->_config->User->Key->Options->Text;
            $timestamp=time();
            $value=-1;
            $number=5;
            $ck_token=md5(REQUEST_IP.$this->_config->User->Key->Options->Text.REQUEST_FORWARDED."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
            $sql_statement=$this->_Database->object->prepare("INSERT INTO {$table_name}(`ck_kid`,`ck_key`,`timestamp`,`value`,`number`) VALUES (:ck_kid,:ck_key,:timestamp,:value,:number)");
            $sql_statement->bindParam(':ck_kid',$ck_kid);
            $sql_statement->bindParam(':ck_key',$ck_token);
            $sql_statement->bindParam(':timestamp',$timestamp);
            $sql_statement->bindParam(':value',$value);
            $sql_statement->bindParam(':number',$number);
            $sql_statement->execute();
            $GLOBALS['return_data']=array(
                'code'=>1,
                'msg'=>'成功',
                'data'=>array(
                    'ck_kid'=>$ck_kid,
                    'expire_time'=>$expire_time,
                    'ck_token'=>$ck_token
                )
            );
            echo_return_data();
        }
        else if(!empty($_REQUEST['verification_value'])&&!empty($_REQUEST['ck_kid'])&&!empty($_REQUEST['ck_token']))
        {
            //验证请求是否存在
            $sql_statement=$this->_Database->object->prepare("SELECT * FROM {$table_name} WHERE `ck_kid`=:ck_kid AND `ck_key`=:ck_key");
            $sql_statement->bindParam(':ck_kid',$_REQUEST['ck_kid']);
            $sql_statement->bindParam(':ck_key',$_REQUEST['ck_token']);
            $sql_statement->execute();
            $result=$sql_statement->fetch();
            if(empty($result['timestamp']))
            {
                $GLOBALS['return_data']=array(
                    'code'=>1023,
                    'msg'=>'错误: 非法请求',
                    'data'=>array('from'=>$_REQUEST['from'])
                );
                echo_return_data();
            }
            if($_REQUEST['verification_value']==-1||$result['value']!=$_REQUEST['verification_value'])
            {
                //扣除次数
                $sql_statement=$this->_Database->object->prepare("UPDATE {$table_name} SET `number`=:number WHERE `ck_kid`=:ck_kid");
                $number=$result['number']-1;
                $sql_statement->bindParam(':number',$number);
                $sql_statement->bindParam(':ck_kid',$_REQUEST['ck_kid']);
                $sql_statement->execute();
                $GLOBALS['return_data']=array(
                    'code'=>1002,
                    'msg'=>'错误: 验证码错误',
                    'data'=>array('from'=>$_REQUEST['from'])
                );
                echo_return_data();
            }
            //删除请求
            $sql_statement=$this->_Database->object->prepare("DELETE FROM {$table_name} WHERE `ck_kid`=:ck_kid");
            $sql_statement->bindParam(':ck_kid',$_REQUEST['ck_kid']);
            $sql_statement->execute();
        }
        else
        {
            $GLOBALS['return_data']=array(
                'code'=>1001,
                'msg'=>'错误: 暂无验证信息',
                'data'=>array('from'=>$_REQUEST['from'])
            );
            echo_return_data();
        }
    }

    //页面安全
    public function ViewSecurity()
    {
        //所有页面均需要返回一个随机Token到Header中
        $token_info=array(
            'Token'=>get_rand_string_id(),
            'Token_Expire_Time'=>time()+$this->_config->User->Expiration->Options->Text
        );
        //计算出签名,防止数据被篡改(需要验证请求者ip)
        $sign=md5(sign($token_info,$this->_config->User->Key->Options->Text).REQUEST_IP.REQUEST_FORWARDED);
        $token_info['Token_Sign']=$sign;
        foreach($token_info as $key=>$value)
            header("{$key}: {$value}");
        if(in_array($this->GetRequestInfo(),explode(',',$this->_config->User->NoVerification->Options->Text)))
            return;
        //csrf安全更新内容,该检查仅在用户浏览器提交本参数的情况下运行且本项仅作为一种额外保护浏览器安全的手段(首页默认放行)
        if(!empty($_SERVER['HTTP_REFERER'])&&$_REQUEST['from']!='main')
        {
            if(preg_match('/^(((https?|file):)?\/\/)?(?<host>[a-zA-Z0-9\.]+\.[a-zA-Z0-9]+)(:[0-9]+)?(\/)?.*$/',$_SERVER['HTTP_REFERER'],$matches))
            {
                //目前无法兼容localhost等方式访问,只能通过符合规则的ip或域名访问(域名不支持中文域名)
                $referer_host=$matches['host'];
                if($referer_host!=CONFIG_HTTP_HOST)
                {
                    exit('
                        <html>
                            <meta charset="utf-8">
                            <head><title>请求拦截-CSRF</title></head>
                            <body>
                                <h1 style="text-align:center">风险警告</h1>
                                <hr>
                                <p style="text-align:center">
                                    高危警告: 您当前有被<a href="https://baike.baidu.com/item/%E8%B7%A8%E7%AB%99%E8%AF%B7%E6%B1%82%E4%BC%AA%E9%80%A0/13777878?fr=aladdin">CSRF攻击</a>风险,我们将会拦截本次请求!<br><br>
                                    请您确认您的目标地址无误后点击下面的链接继续<br>
                                    您继续访问该目标地址将会视为您自愿无视本次风险且造成的所有影响由您自己承担!<br><br><br>
                                    <a href="'.CONFIG_REQUEST_URL.REQUEST_URI.'">'.CONFIG_REQUEST_URL.REQUEST_URI.'</a><br><br><br>
                                    您也可以放弃本次请求选择<br><br><br>
                                    <a href="'.CONFIG_REQUEST_URL.'">前往首页</a> | <a href="javascript:history.back(-1)">返回源地址</a>
                                </p>
                            </body>
                        </html>
                    ');
                }
            }
            else
            {
                exit('内部错误!(com.verification.api.ViewSecurity)');
            }
        }
        if($_REQUEST['from']=='verification')
        {
            if(!empty($_GET['ck_key']))
            {
                $ck_kid=isset($_COOKIE['ck_kid'])?$_COOKIE['ck_kid']:'';
                $ck_key=isset($_GET['ck_key'])?$_GET['ck_key']:'';
                $expire_time=isset($_COOKIE['expire_time'])?$_COOKIE['expire_time']:'';
                $ck_token=md5(REQUEST_IP.$this->_config->User->Key->Options->Text.REQUEST_FORWARDED."&ck_kid={$ck_kid}&ck_key={$ck_key}&expire_time={$expire_time}");
                if($ck_token!=(isset($_COOKIE['ck_token'])?$_COOKIE['ck_token']:'')||time()>$expire_time)
                    exit('验证失败或请求已过期!<br>如果您禁止了Cookie,我们将无法为您正常提供服务');
                setcookie('ck_key',$ck_key,$expire_time);
                header('Location: '.CONFIG_REQUEST_URL.(empty($_COOKIE['url'])?'/':$_COOKIE['url']));
                exit();
            }
            else
            {
                $javascript_code=file_get_contents(__DIR__.'/../res/verification.js');
                $javascript_script=new PluginVerificationApiVerification($this->_this->_Database);
                $javascript_script->key=$this->_config->User->Key->Options->Text;
                $javascript_script->expire_time=$this->_config->User->Expiration->Options->Text;
                $javascript_tmp=$javascript_script->StartCheck();
                $content_array=array(
                    'javascript_script'=>$javascript_tmp
                );
                echo '<html><script>';
                echo javascript_encode(variable_load($content_array,$javascript_code));
                echo '</script></html>';
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
                header('Location: '.CONFIG_REQUEST_URL.'/?from=verification&type=plugin');
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
                header('Location: '.CONFIG_REQUEST_URL.'/?from=verification&type=plugin');
                setcookie('url',$request_url);
                exit();
            }
        }
    }

    public function GetRequestInfo(){
        //本次请求信息(默认值同 Request 模块)
        $request_type=(empty($_REQUEST['type'])?'view':$_REQUEST['type']);
        $request_from=(empty($_REQUEST['from'])?'main':$_REQUEST['from']);
        $request_class=(empty($_REQUEST['class'])?'':$_REQUEST['class']);
        return "{$request_type}:{$request_from}".(empty($request_class)?'':"/{$request_class}");
    }
}

?>