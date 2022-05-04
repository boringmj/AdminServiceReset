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

//所有默认权限均在此处注册
$default_permission=array(
    'request'=>array(
        'view'=>array(
            'error'=>true,
            '__install'=>true,
            'main'=>true,
            'user'=>array(
                'head_portrait'=>true
            )
        ),
        'api'=>array(
            'main'=>true
        ),
        'plugin'=>array(
            '*'=>true
        )
    )
);

//需要Permission类的支持,否则无法检查权限
if(class_exists('Permission')){
    //权限检查(权限检查仅检查权限,不验证app_id真实性)
    $Permission=new Permission(empty($_REQUEST['app_id'])?'':$_REQUEST['app_id']);
    $Permission->SetDefaultPermission($default_permission);
    if(!$Permission->CheckPermission())
    {
        $permission_name=$Permission->GetPermissionName();
        exit(LANGUAGE_PERMISSION_ERROR_NO_PERMISSION_ONE.': '.($permission_name?$permission_name:'?'));
    }
}
?>