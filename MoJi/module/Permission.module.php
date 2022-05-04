<?php

/* 权限模块
 * 
 * 关于
 * 本模块是介于 Request 与 App_Id 之间的权限管理模块
 * 权限模块将会检查每一个 App_Id 在请求到达时的权限(不提供 App_Id 则使用默认权限)
 * 权限不足时请求模块将有权拦截请求(这个行为优先级非常高,可能部分插件的模拟请求也会被拦截)
 * 
 * 父级权限继承
 * 当某个节点的权限不存在时,将会从父级节点继承权限
 * 父级节点的权限不存在时,将会继承父级节点的父级节点的权限,直到获取到权限或根节点不存在时结束
 * 当根节点的权限不存在时,将会继承默认权限,默认权限同样具备父级权限的继承特性
 * 如果依旧无法获取到权限,将会默认为无权限
 */

class Permission
{
    private $_app_id;
    private $_data_path;
    private $_default_permission;

    public function SetAppId($app_id)
    {
        $this->_app_id=$app_id;
    }

    final protected function _GetPermissionInfo($app_id)
    {
        $data_path=$this->_data_path.'/'.md5($app_id).'data.json';
        if(!file_exists($data_path))
            return $this->_default_permission;
        //这里并不与默认权限合并返回,所以在检查权限时,如果没有权限则应该调用默认权限
        $permission_info=json_decode(file_get_contents($data_path),true);
        if(!is_array($permission_info))
            return $this->_default_permission;
        return $permission_info;
    }

    //获取当前权限节点名称
    static public function GetPermissionName()
    {
        $request_type=(empty($_REQUEST['type'])?'view':$_REQUEST['type']);
        $request_from=(empty($_REQUEST['from'])?'main':$_REQUEST['from']);
        $request_class=(empty($_REQUEST['class'])?'':$_REQUEST['class']);
        //请求类型严格检查,仅允许为 api 或 view
        if(!in_array($request_type,array('api','view')))
            return false;
        //from和class开放性检查,数字字母下划线组成即可通过
        if(!preg_match("/^[A-Za-z0-9_]+$/",$request_from))
            return false;
        if($request_class&&!preg_match("/^[A-Za-z0-9_]+$/",$request_class))
            return false;
        return 'request.'.$request_type.'.'.$request_from.($request_class?'.'.$request_class:'');
    }

    //检查节点权限
    public function CheckPermission($permission_name='')
    {
        if(empty($permission_name))
            $permission_name=self::GetPermissionName();
        $request_app_id=$this->_app_id;
        $permission_info=self::_GetPermissionInfo($request_app_id);
        $permission_name_array=explode('.',$permission_name);
        $permission=$this->_GetPermissionByArray($permission_name_array,$permission_info);
        return $permission;
    }

    //根据节点数组获取权限
    private function _GetPermissionByArray($permission_name_array,$permission_info,$is_default=false)
    {
        if(!is_array($permission_name_array))
            return false;
        $array_lenght=count($permission_name_array);
        $permission_yes=false;
        $permission_father=empty($permission_info['*'])?false:true;
        $permission=empty($permission_info['*'])?false:($permission_info['*']?true:false);
        if(empty($permission_name_array)||$permission_name_array[0]=='*')
            return $permission;
        $permission_info_temp=$permission_info;
        $permission_count=0;
        foreach($permission_name_array as $key)
        {
            //当遇到通配权限或已经找到权限时,不在继续查找
            if($key=='*'||$permission_yes)
                break;
            //有权限节点
            if(isset($permission_info_temp[$key]))
            {
                $permission_father=true;
                if(is_array($permission_info_temp[$key]))
                {
                    //如果本级节点有通配权限,则记录通配权限
                    if(isset($permission_info_temp[$key]['*']))
                        $permission=$permission_info_temp[$key]['*']?true:false;
                    //先确定是否还需要往下查找,如果还需要查找,则需要准备下一个查找的权限节点
                    if($permission_count==$array_lenght-1)
                        $permission_yes=true;
                    else
                        $permission_info_temp=$permission_info_temp[$key];
                }
                else
                {
                    //当权限无下级节点时,直接返回当前权限
                    $permission=$permission_info_temp[$key]?true:false;
                    $permission_yes=true;
                }
            }
            else
                break;
            $permission_count++;
        }
        //没找到权限则前往默认权限节点中查找
        if(!$permission_yes&&!$is_default&&!$permission_father)
            $permission=$this->_GetPermissionByArray($permission_name_array,$this->_default_permission,true);
        return $permission;
    }

    public function __construct($app_id='')
    {
        $this->_app_id=$app_id;
        $this->_data_path=DATA_PATH.'/permission';
        //所有默认权限均在此处注册
        $this->_default_permission=array();
    }
}

?>