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
 * 
 * 特殊情况处理
 * 当查询到某个不存在的权限节点时,会逐级向上查询,直到找到权限节点或根节点不存在时查找默认节点(同父级权限继承)
 * 当某个权限节点不存在子节点时,将会默认继承最近一级父节点的权限(父节点不存在权限时将会向上查找父节点的父节点)
 * 当节点组中出现通配权限节点(“ * ”)时,将不再继续向下查找子节点,所以可以是用通配权限节点(“ * ”)查询根节点权限
 * 当根节点不存在时,将会查询默认权限,当默认权限同样不存在根节点时,将会视为无权限
 * 
 * 权限树与权限查询原理
 * 本程序不对权限树结构做出过多要求,也不会提供的节点参数做过多要求,反正我查询原理都放下边了,结果也只有两个结果:有无权限
 * 权限树结构:
 * 权限树的值为一个数组,数组中的每一个元素是一个节点,节点的值允许为数组(有下级节点)或布尔值(无下级节点)
 * 虽然规定无下级节点为一个布尔值,但这并不是硬性要求,也可以是空值或者数字
 * 每个元素的键(Key)为节点的名称,值(Value)为节点的权限值(Boolean)或下级节点(Array)
 * 如果想要表示一个有下级节点的节点权限,可以在其下级节点中使用通配权限节点(“ * ”)作为键
 * 查询原理:
 * 查询权限节点是一个遍历权限节点名称组的过程
 * 值得一提,提供的权限节点名称组是一个数组,数组第一个元素不为通配权限节点(“ * ”),则表示根节点下的第一个节点名
 * 此后每一个元素都表示为上一个元素的子节点
 * 如果在遍历过程中查询到了通配权限节点(“ * ”),则遍历结束,不在继续查询
 * 否则先判断该节点是否存在,存在则下判断该节点是否有下级节点,无下级节点直接返回值,有下级节点则需要判断是否需要继续遍历
 * 当这个过程中没有查询到节点,并且这个节点所有父级节点没设置权限时,则会使用使用上面这个方法查找默认权限
 * 当默认权限也没设置时,就代表这个节点是无权限的(查询不到默认无权限)
 * 这里需要说明一下无权限和没设置(不存在)权限的区别
 * 无权限是指节点的值以及确定了,结果是没有权限,而没设置则是这个节点找不到权限,需要使用父级或默认权限(都没有则是无权限)
 */

//所有默认权限均在此处注册
$default_permission=array(
    'request'=>array(
        'view'=>array(
            'error'=>true,
            '__install'=>true,
            '__update'=>true,
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

//如果可写且文件不存在,则将默认权限写入文件
$default_permission_path=DATA_PATH.'/permission'.'/'.md5(CONFIG_KEY_SALT.'default_permission'.CONFIG_KEY_KEY).'.data.json';
if(is_writable(DATA_PATH.'/permission')&&!file_exists($default_permission_path))
    file_put_contents($default_permission_path,json_encode($default_permission));
//如果可读,则优先从文件读取权限
if(is_file($default_permission_path)&&is_readable($default_permission_path))
    $default_permission=json_decode(file_get_contents($default_permission_path),true);

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