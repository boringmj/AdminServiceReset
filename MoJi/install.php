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
    protected $data_path=DATA_PATH.'/install.data.json';
    protected $database_tables=array(
        'table'=>'info'
    );

    //默认调用的方法
    public function Start(&$Database='')
    {
        //判断安装是否有价值,没有价值就不进行安装
        if($this->Check($Database))
            return true;
        if(isset($_REQUEST['from'])&&$_REQUEST['from']==='__install')
        {
            //安装
            if(DATABASE_ENABLE)
                $this->Database($Database);
            $this->Other();
            $this->Updata($Database);
            file_put_contents($this->data_path,json_encode(array('grade'=>CONFIG_INFO_GRADE)));
        }
        else if(isset($_REQUEST['from'])&&$_REQUEST['from']==='__update')
        {
            //更新
            $this->Updata($Database);
            file_put_contents($this->data_path,json_encode(array('grade'=>CONFIG_INFO_GRADE)));
        }
        return false;
    }

    //检查是否需要安装
    protected function Check(&$Database)
    {
        if(file_exists($this->data_path))
        {
            if(is_file($this->data_path)&&is_writable($this->data_path))
            {
                $install_info=file_get_contents($this->data_path);
                $install_info_object;
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
                exit($this->data_path.' '.LANGUAGE_INSTALL_DATA_PATH_ERROR);
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
    protected function Database(&$Database)
    {

    }

    //安装其他东西
    protected function Other()
    {

    }

    //卸载安装
    public function Uninstall(&$Database)
    {
        if(DATABASE_ENABLE)
        {
            //卸载数据库已安装内容
        }
    }

    //安装更新内容
    protected function Updata(&$Database)
    {
        if(DATABASE_ENABLE)
        {
            //更新数据库已安装内容
        }
    }
}

?>