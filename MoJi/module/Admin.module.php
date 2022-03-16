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
            }
            exit();
        }
        else
        {
            exit("plugin 帮助:\n\r-- [help] -- 查看插件系统帮助\n\r-- <list> [dirname]-- 显示插件存放目录名称(无论是否合法均显示)\n\r-- <list> <name>-- 显示插件名称(无论是否合法均显示)\n\r-- <list> <info>-- 显示插件名称(无论是否合法均显示)\n\r");
        }
    }
    else if($GLOBALS["argv"][1]=='help')
    {
        exit("php index.php 帮助:\n\r-- <help> -- 查看帮助\n\r-- <plugin> -- 插件系统管理\n\r请使用完整形式执行命令,如:\n\rphp index.php help\n\r");
    }
    else
    {
        exit("错误:未知的系统命令,请使用 \"php index.php help\" 查看帮助\n\r");
    }
}

?>