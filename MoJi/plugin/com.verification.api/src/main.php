<?php

/** 一般规范
 * 我们规定,main.php中应该有且只有 info.json->Main 中注册的主类
 * 如果需要自定义类,请将类的类名使用 主类名称+自定义类名称 命名,并存放在插件的src目录中
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
    protected $_data_path;      //数据存放位置
    protected $_config;         //程序配置数据

    //默认调用的方法,请保留该方法且不可修改形参
    public function Start(&$Database,$data_path,$config)
    {
        $this->_Database=$Database;
        $this->_data_path=$data_path;
        $this->_config=$config;
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

        $path=$data_path.'/init.data.json';
        $data=array(
            'code'=>1,
            'msg'=>'初始化内容执行成功',
            'data'=>array(
                'path'=>$path
            )
        );
        file_put_contents($path,json_encode($data));
    }

    //接口安全
    public function ApiSecurity()
    {

    }

    //页面安全
    public function ViewSecurity()
    {

    }

    //请求错误页
    public function RequestError()
    {

    }

    //完成请求
    public function Finish()
    {
        
    }
}

?>