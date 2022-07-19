<?php

class Permission
{
    private $_app_id;
    private $_data_path;
    private $_default_permission;

    /**
     * 设置APP_ID
     * 
     * @param string $app_id APP_ID
     * @return void
     */
    public function SetAppId($app_id)
    {
        $this->_app_id=$app_id;
    }

    /**
     * 设置默认权限
     * 
     * @param array $permission 默认权限数组
     * @return void
     */
    public function SetDefaultPermission($permission)
    {
        $this->_default_permission=$permission;
    }

    /**
     * 获取权限数组(app_id=default_permission 为默认数组)
     * 
     * @param string $app_id APP_ID
     * @return array
     */
    final protected function _GetPermissionInfo($app_id)
    {
        //app_id=default_permission 为默认权限
        $data_path=$this->_data_path.'/'.md5(CONFIG_KEY_SALT.$app_id.CONFIG_KEY_KEY).'.data.json';
        if(!file_exists($data_path))
            return $this->_default_permission;
        //这里并不与默认权限合并返回,所以在检查权限时,如果没有权限则应该调用默认权限
        $permission_info=json_decode(file_get_contents($data_path),true);
        if(!is_array($permission_info))
            return $this->_default_permission;
        return $permission_info;
    }

    /**
     * 获取当前权限节点名称
     * 
     * @return boolean|string
     */
    static public function GetPermissionName()
    {
        //需与Request模块中的默认值保持一致
        $request_type=(empty($_REQUEST['type'])?'view':$_REQUEST['type']);
        $request_from=(empty($_REQUEST['from'])?'main':$_REQUEST['from']);
        $request_class=(empty($_REQUEST['class'])?'':$_REQUEST['class']);
        //请求类型严格检查
        if(!in_array($request_type,array('api','view','plugin','web')))
            return false;
        //from和class开放性检查,数字字母下划线组成即可通过
        if(!preg_match("/^[A-Za-z0-9_]+$/",$request_from))
            return false;
        if($request_class&&!preg_match("/^[A-Za-z0-9_]+$/",$request_class))
            return false;
        return 'request.'.$request_type.'.'.$request_from.($request_class?'.'.$request_class:'');
    }

    /**
     * 检查节点权限
     * 
     * @param string $permission_name 节点名称
     * @return boolean
     */
    public function CheckPermission($permission_name='')
    {
        if(empty($permission_name))
            $permission_name=self::GetPermissionName();
        $request_app_id=$this->_app_id;
        $permission_info=self::_GetPermissionInfo($request_app_id);
        $permission_name_array=explode('.',$permission_name);
        $permission=$this->GetPermissionByArray($permission_name_array,$permission_info);
        return $permission;
    }

    /**
     * 根据节点数组获取权限
     * 
     * @param array $permission_name_array 节点数组
     * @param array $permission_info 权限数组
     * @param boolean $is_default 是否为默认节点(为True将不再检查默认节点)
     * @return boolean
     */
    public function GetPermissionByArray($permission_name_array,$permission_info,$is_default=false)
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
            $permission=$this->GetPermissionByArray($permission_name_array,$this->_default_permission,true);
        return $permission;
    }

    /**
     * 构造函数
     * 
     * @param integer $app_id APP_ID
     * @return void
     */
    public function __construct($app_id='')
    {
        $this->_app_id=$app_id;
        $this->_data_path=DATA_PATH.'/permission';
    }
}

?>