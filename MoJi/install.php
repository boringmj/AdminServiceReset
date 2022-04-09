<?php

/* 安装文件的使用说明
 * 
 * 请您将应用环境的安装和检查统一写到这里
 * 系统将会检查安装,如果未安装将会调用本文件
 * 主线安装将会拦截并取消本次用户请求,用户将会被引导到安装页
 * 安装一切信息都将会直接输入到网页内,不记入日志内,且不分调试
 */

class Install
{
    public $error_info=array();
    protected $_data_path;
    protected $_database_tables=array(
        'system_info'=>'(`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `app_id`          VARCHAR(32)   NOT NULL,
            `app_key`         VARCHAR(32)   NOT NULL,
            `timestamp`       INT(10)       NOT NULL
        )',
        'system_user'=>'(`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `app_id`          VARCHAR(32)   NOT NULL,
           ` timestamp`       INT(10)       NOT NULL,
            `uuid`            VARCHAR(36)   NOT NULL,
            `user_name`       VARCHAR(32)   NOT NULL,
            `password`        VARCHAR(32)   NOT NULL,
            `nickname`        VARCHAR(32)   NOT NULL,
            `user_group`      INT(5)        NOT NULL,
            `email`           VARCHAR(64)   NOT NULL,
            `head_portraits`  VARCHAR(255)  NOT NULL,
            `status`          INT(5)        NOT NULL
        ) AUTO_INCREMENT=1000',
        'system_user_group'=>'(`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `app_id`          VARCHAR(32)   NOT NULL,
            `timestamp`       INT(10)       NOT NULL,
            `group_name`      VARCHAR(32)   NOT NULL,
            `group_level`     INT(5)        NOT NULL
        )',
        'system_nonce'=>'(`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `app_id`          VARCHAR(32)   NOT NULL,
            `timestamp`       INT(10)       NOT NULL,
            `nonce`           VARCHAR(12)   NOT NULL,
            `sign`            VARCHAR(32)   NOT NULL
        )'
    );

    //默认调用的方法
    public function Start(&$Database='')
    {
        //判断安装是否有价值,没有价值就不进行安装
        if($this->_Check($Database))
            return true;
        if(isset($_REQUEST['from'])&&$_REQUEST['from']==='__install')
        {
            //安装
            if(DATABASE_ENABLE)
                $this->_Database($Database);
            $this->_Other();
            $this->_Updata($Database);
            file_put_contents($this->_data_path,json_encode(array('grade'=>CONFIG_INFO_GRADE)));
        }
        else if(isset($_REQUEST['from'])&&$_REQUEST['from']==='__update')
        {
            //更新
            $this->_Updata($Database);
            file_put_contents($this->_data_path,json_encode(array('grade'=>CONFIG_INFO_GRADE)));
        }
        return false;
    }

    //检查是否需要安装
    protected function _Check(&$Database)
    {
        if(file_exists($this->_data_path))
        {
            if(is_file($this->_data_path)&&is_writable($this->_data_path))
            {
                $install_info=file_get_contents($this->_data_path);
                if($install_info_object=json_decode($install_info))
                {
                    if(CONFIG_INFO_GRADE>$install_info_object->grade)
                    {
                        //更新引导
                        $url=CONFIG_REQUEST_URL.'/?from=__update'.(CURRENT_LANGUAGE!=DEFAULT_LANGUAGE?'&language='.CURRENT_LANGUAGE:'');
                        echo LANGUAGE_INSTALL_NOT_UPDATE." <a href='{$url}'>".LANGUAGE_INSTALL_NOT_UPDATE_CLICK."</a>";
                        return false;
                    }
                }
                else
                    exit(LANGUAGE_INSTALL_DATA_ERROR);
            }
            else
                exit($this->_data_path.' '.LANGUAGE_INSTALL_DATA_PATH_ERROR);
            return true;
        }
        //安装引导
        if(!isset($_REQUEST['from']))
        {
            $url=CONFIG_REQUEST_URL.'/?from=__install'.(CURRENT_LANGUAGE!=DEFAULT_LANGUAGE?'&language='.CURRENT_LANGUAGE:'');
            echo LANGUAGE_INSTALL_NOT_INSTALL." <a href='{$url}'>".LANGUAGE_INSTALL_NOT_INSTALL_CLICK."</a>";
        }
        return false;
    }

    //数据库类信息安装
    protected function _Database(&$Database)
    {
        $server_timestamp=time();
        //随机生成app_id和app_key
        $app_id=get_rand_string(22).time();
        $app_key=md5(get_rand_string(32));
        //默认用户组名称和级别
        $user_group_default=array(
            'group_name'=>CONFIG_USER_DEFAULT_GROUP_NAME,
            'group_level'=>CONFIG_USER_DEFAULT_GROUP_LEVEL
        );
        //创建数据表
        foreach($this->_database_tables as $table=>$info)
        {
            $table_name=$Database->GetTablename($table);
            $sql_statement=$Database->object->prepare("CREATE TABLE {$table_name}{$info}");
            echo $table_name.($sql_statement->execute()?'数据表创建成功':'数据表创建失败或已创建').'<br>';
        }
        //插入默认数据
        $table_name=$Database->GetTablename('system_info');
        $sql_statement=$Database->object->prepare("INSERT INTO {$table_name}(`app_id`,`app_key`,`timestamp`) VALUES (:app_id,:app_key,:timestamp)");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':app_key',$app_key);
        $sql_statement->bindParam(':timestamp',$server_timestamp);
        echo $sql_statement->execute()?"{$table_name}数据表初始数据已插入,<font color=red>App_id={$app_id},APP_key={$app_key}</font><br>":"";
        //插入默认用户组
        $table_name=$Database->GetTablename('system_user_group');
        $sql_statement=$Database->object->prepare("INSERT INTO {$table_name}(`app_id`,`timestamp`,`group_name`,`group_level`) VALUES (:app_id,:timestamp,:group_name,:group_level)");
        $sql_statement->bindParam(':app_id',$app_id);
        $sql_statement->bindParam(':timestamp',$server_timestamp);
        $sql_statement->bindParam(':group_name',$user_group_default['group_name']);
        $sql_statement->bindParam(':group_level',$user_group_default['group_level']);
        echo $sql_statement->execute()?"{$table_name}数据表初始数据已插入,<font color=red>App_id={$app_id},Group_name={$user_group_default['group_name']},Group_level={$user_group_default['group_level']}</font><br>":"";
    }

    //安装其他东西
    protected function _Other()
    {

    }

    //卸载安装(请在命令行调用该方法)
    public function Uninstall(&$Database)
    {
        if(DATABASE_ENABLE)
        {
            //卸载数据库已安装内容
            foreach($this->_database_tables as $table=>$info)
            {
                $table_name=$Database->GetTablename($table);
                $sql_statement=$Database->object->prepare("DROP TABLE {$table_name}");
                echo $table_name.($sql_statement->execute()?'数据表删除成功':'数据表删除失败或已删除')."\n\r";
            }
        }
        //这里懒得判断是否成功了
        if(file_exists($this->_data_path))
            unlink($this->_data_path);
        $this->_DeleteDir(DATA_PATH);
        echo LANGUAGE_INSTALL_UNINSTALL_SUCCESS."\n\r";
    }

    //安装更新内容
    protected function _Updata(&$Database)
    {
        if(DATABASE_ENABLE)
        {
            //更新数据库已安装内容
        }
    }

    //删除目录和目录下所有文件
    protected function _DeleteDir($dir)
    {
        if(!is_dir($dir))
            return false;
        $handle=opendir($dir);
        while(false!==($file=readdir($handle)))
        {
            if($file!='.'&&$file!='..')
            {
                $dir2=$dir.'/'.$file;
                is_dir($dir2)?$this->_DeleteDir($dir2):unlink($dir2);
            }
        }
        closedir($handle);
        return rmdir($dir);
    }

    //使用构造方法兼容5.x版本
    public function __construct()
    {
        $this->_data_path=DATA_PATH.'/install.data.json';
    }
}

?>