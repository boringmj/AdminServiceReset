<?php

/** 一般规范
 * 我们规定,main.php中应该有且只有 info.json->Main 中注册的主类
 * 所有依赖请在定义类之前引用
 * 该类应该继承自 Pulgin 抽象类,且不可重写 Start 方法
 * 如果需要自定义类,请将类的类名使用 主类名称+自定义类名称 命名,并存放在插件的src目录或自定义目录中
 * 主类的 Init(&$Database,$data_path) 方法请在主类中实现
 * 主类中不必要的方法允许删除
 * 插件数据请存放入规定的 数据存放目录(protected $_data_path)
 */

//请注意类名需要与 info.json->Main 保持一致,且符合命名规范
class PluginSdkMain extends Pulgin
{
    static public function Init(&$Database,$data_path)
    {
        /** 事件说明
         * 本方法需要严格定义为 公共静态(static public) 修饰且建议不要修改形参
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

    /**
     * 接口安全验证事件(不可修改形参)
     * 
     * @return void
     */
    public function ApiSecurity()
    {

    }

    /**
     * Web接口安全事件(不可修改形参)
     * 
     * @return void
     */
    public function WebSecurity()
    {

    }

    /**
     * 页面安全事件(不可修改形参)
     * 
     * @return void
     */
    public function ViewSecurity()
    {

    }

    /**
     * 请求错误页事件(不可修改形参)
     * 
     * @return void
     */
    public function RequestError()
    {

    }

    /**
     * 完成请求事件(不可修改形参)
     * 
     * @return void
     */
    public function Finish()
    {
        
    }
}

?>