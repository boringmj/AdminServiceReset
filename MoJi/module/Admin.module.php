<?php

//本模块暂不支持其他语言,可能会在将来进行更新
//本模块暂未优化,仅实现了部分功能

if(isset($GLOBALS["argv"][1])&&REQUEST_IP=="0.0.0.0")
{
    if($GLOBALS["argv"][1]=='plugin')
    {
        if(!isset($GLOBALS["argv"][2]))
            $GLOBALS["argv"][2]="help";
        if($GLOBALS["argv"][2]=="list")
        {
            $plugin_path_array=scandir(PLUGIN_PATH);
            $count=0;
            foreach($plugin_path_array as $plugin_package_name)
            {
                if($plugin_package_name==='.'||$plugin_package_name==='..')
                    continue;
                $count++;
                $plugin_path=PLUGIN_PATH.'/'.$plugin_package_name;
                $plugin_default_info_path=$plugin_path.'/info.json';
                if(empty($GLOBALS["argv"][3])||$GLOBALS["argv"][3]=="dirname")
                    echo "{$count}. {$plugin_package_name}\n\r";
                else if($GLOBALS["argv"][3]=="name")
                {
                    //count. name(package_dir_name)
                    $plugin_name="无法获取";
                    if(is_file($plugin_default_info_path))
                    {
                        $info_default_json;
                        if($info_default_json=json_decode(file_get_contents($plugin_default_info_path)))
                            $plugin_name=isset($info_default_json->Name)?$info_default_json->Name:"无法获取";
                    }
                    echo "{$count}. {$plugin_name}({$plugin_package_name})\n\r";
                }
                else if($GLOBALS["argv"][3]=="info")
                {
                    $is_ok=false;
                    if(is_file($plugin_default_info_path))
                    {
                        //count. name(package_dir_name)[标号:grade,sdk版本:level]
                        //    名称:name(vversion)
                        //    包名:package_dir_name
                        //    注册包名:package_name
                        //    描述:description
                        //    作者:developer(email)
                        //    注册主类:main_class
                        //    最低兼容php版本:compatible
                        //    完整路径:dir_path
                        $info_default_json;
                        if($info_default_json=json_decode(file_get_contents($plugin_default_info_path)))
                        {
                            $plugin_name=isset($info_default_json->Name)?$info_default_json->Name:"无法获取";
                            echo "{$count}. {$plugin_name}({$plugin_package_name})\n\r";
                            echo "    名称:".(isset($info_default_json->Name)?$info_default_json->Name:"无法获取");
                            echo "(v".(isset($info_default_json->Version)?$info_default_json->Version:"无法获取").")";
                            echo "[标号:".(isset($info_default_json->Grade)?$info_default_json->Grade:"无法获取").",";
                            echo "sdk版本:".(isset($info_default_json->Level)?$info_default_json->Level:"无法获取")."]\n\r";
                            echo "    包名:{$plugin_package_name}\n\r";
                            echo "    注册包名:".(isset($info_default_json->PackageName)?$info_default_json->PackageName:"无法获取")."\n\r";
                            echo "    描述:".(isset($info_default_json->Description)?$info_default_json->Description:"无法获取")."\n\r";
                            echo "    作者:".(isset($info_default_json->Developer)?$info_default_json->Developer:"无法获取");
                            echo "(".(isset($info_default_json->Email)?$info_default_json->Email:"无法获取").")\n\r";
                            echo "    注册主类:".(isset($info_default_json->Main)?$info_default_json->Main:"无法获取")."\n\r";
                            echo "    最低兼容php版本:".(isset($info_default_json->Compatible)?$info_default_json->Compatible:"无法获取")."\n\r";
                            echo "    完整路径:{$plugin_path}"."\n\r";
                            $is_ok=true;
                        }
                    }
                    if(!$is_ok)
                        echo "{$count}. 插件异常({$plugin_package_name})\n\r";
                }
                else if($GLOBALS["argv"][3]=="permission")
                {
                    //count. name(package_dir_name)
                    //    ----permission_name
                    //    ----permission_name
                    $is_ok=false;
                    if(is_file($plugin_default_info_path))
                    {
                        $info_default_json;
                        if($info_default_json=json_decode(file_get_contents($plugin_default_info_path)))
                        {
                            $plugin_name=isset($info_default_json->Name)?$info_default_json->Name:"无法获取";
                            echo "{$count}. {$plugin_name}({$plugin_package_name})\n\r";
                            if(is_array($info_default_json->Permission))
                            {
                                foreach($info_default_json->Permission as $permission_name)
                                {
                                    echo "    ----{$permission_name}\n\r";
                                }
                            }
                            else
                            {
                                echo "    ----暂无权限节点\n\r";
                            }
                            $is_ok=true;
                        }
                    }
                    if(!$is_ok)
                        echo "{$count}. 插件异常({$plugin_package_name})\n\r";
                }
            }
            exit();
        }
        else
        {
            exit("plugin 帮助:\n\r-- [help] -- 查看插件系统帮助\n\r-- <list> [dirname] -- 显示所有插件存放目录名称(无论是否合法均显示)\n\r-- <list> <name> -- 显示所有插件名称(无论是否合法均显示)\n\r-- <list> <info> -- 显示所有插件名称(无论是否合法均显示)\n\r-- <list> <permission> -- 显示所有插件所需权限节点名称\n\r!!! 请注意: 所有数据均为实时读取,可能与已解析插件有差异\n\r");
        }
    }
    else if($GLOBALS["argv"][1]=='uninstall')
    {
        $Install=new Install();
        $Install->Uninstall($Database);
        exit();
    }
    else if($GLOBALS["argv"][1]=='webadmin')
    {
        //这是一个非常危险的行为,请谨慎使用)
        /* WEB管理原理介绍
         * 
         * 首先先生成一个随机的地址,确保用户可以通过这个地址访问到管理页面
         * 当用户访问到这个地址时,服务器记录用户的IP,并重置过期时间
         * 当有用户已经访问过这个地址后,其他IP将不能访问到这个地址
         * 当没有用户访问这个链接,链接将会在5分钟后过期
         * 
         * 安全隐患:
         * 可以通过爆破等手段访问到这个随机地址,且可能抢先一步访问到管理页面
         * 用户操作不当也可能导致这个地址被破解(例如用户开启WEB管理功能,但未第一时间访问或用户在访问结束后未手动取消授权)
         * 如果Data目录能被直接访问,则会直接泄露管理地址
         * 
         * 注意:
         * WEB管理最多只能授权一个用户访问,打开新的管理页面将会关闭之前的管理页面
         */
        if(!isset($GLOBALS["argv"][2]))
            $GLOBALS["argv"][2]="open";
        $webadmin_data_path=DATA_PATH.'/webadmin_tmp.data.json';
        if($GLOBALS["argv"][2]=='open')
        {
            //打开
            $rand_string=get_rand_string(32);
            $webadmin_data_json=array(
                "rand_string"=>$rand_string,
                "expire_time"=>time()+60*5,
                "user_ip"=>"NULL"
            );
            file_put_contents($webadmin_data_path,json_encode($webadmin_data_json));
            exit("管理页面地址: /?from=webadmin&id={$rand_string}\n\r");
        }
        else if($GLOBALS["argv"][2]=='cancel')
        {
            //关闭
            unlink($webadmin_data_path);
            unlink(DATA_PATH.'/webadmin.data.json');
            exit("已关闭管理页面\n\r");
        }
    }
    else if($GLOBALS["argv"][1]=='help')
    {
        exit("php index.php 帮助:\n\r-- <help> -- 查看帮助\n\r-- <uninstall> -- 卸载安装\n\r-- <plugin> [...] -- 插件系统管理\n\r-- <webadmin> [open] -- 开启WEB管理\n\r-- <webadmin> <cancel> --关闭WEB管理\n\r请使用完整形式执行命令,如:\n\rphp index.php help\n\r");
    }
    else
    {
        exit("错误:未知的系统命令,请使用 \"php index.php help\" 查看帮助\n\r");
    }
}

//拦截所有来至命令行的请求
if(REQUEST_IP=="0.0.0.0")
    exit("请使用 \"php index.php help\" 查看帮助\n\r");

//拦截WEB管理请求(该请求优先级应高于权限模块)
if(isset($_REQUEST['from'])&&$_REQUEST['from']=="webadmin")
{
    //先检查是否已经开启WEB管理(包含访问是否合法)
    $webadmin_data_path=DATA_PATH.'/webadmin.data.json';
    $webadmin_tmp_data_path=DATA_PATH.'/webadmin_tmp.data.json';
    //这里主要是对权限不足的情况进行处理(毕竟执行php index.php命令的用户不一定是web用户)
    if(file_exists($webadmin_tmp_data_path)&&!file_exists($webadmin_data_path))
        file_put_contents($webadmin_data_path,file_get_contents($webadmin_tmp_data_path));
    if(!file_exists($webadmin_data_path)||empty($_REQUEST['id']))
        exit("错误:WEB管理未开启\n\r");
    $webadmin_data_json=json_decode(file_get_contents($webadmin_data_path),true);
    if(!isset($webadmin_data_json["rand_string"])||!isset($webadmin_data_json["expire_time"])||!isset($webadmin_data_json["user_ip"]))
        exit("错误:WEB管理未开启\n\r");
    if($webadmin_data_json["rand_string"]!=$_REQUEST['id']||time()>$webadmin_data_json["expire_time"])
        exit("错误:WEB管理未开启\n\r");
    if($webadmin_data_json["user_ip"]!="NULL"&&$webadmin_data_json["user_ip"]!=REQUEST_IP)
        exit("错误:WEB管理未开启\n\r");
    //没有人访问过,则记录访问者IP
    if($webadmin_data_json["user_ip"]=="NULL")
    {
        //默认给予3个小时的访问权限
        $webadmin_data_json["user_ip"]=REQUEST_IP;
        $webadmin_data_json["expire_time"]=time()+60*60*3;
        file_put_contents($webadmin_data_path,json_encode($webadmin_data_json));
    }
    exit("hello world!");
}

?>