<?php

//该模块需要Database类的支持,如有缺失请前往开源库自行获取
if(DATABASE_ENABLE&&class_exists('Database'))
{
    debug_log(LANGUAGE_DATABASE_NAME,LANGUAGE_REQUEST_SUCCESS,__FILE__);
    $Database=new Database();
    $Database->SetHost(CONFIG_DATABASE_HOST);
    $Database->SetUser(CONFIG_DATABASE_USER);
    $Database->SetPasswd(CONFIG_DATABASE_PASSWORD);
    $Database->SetDatabase(CONFIG_DATABASE_DATABASE);
    $Database->prefix=CONFIG_DATABASE_PREFIX;
    $CONFIG_DATABASE=null;
    if(!$Database->Link())
    {
        write_log(LANGUAGE_DATABASE_ERROR_TITLE,$Database->error,__FILE__,20);
        if(APPLICATION_DEBUG)
            exit(LANGUAGE_DATABASE_ERROR_DEBUG);
        else
            exit(LANGUAGE_DATABASE_ERROR);
    }
}
else
{
    //不支持或未开启处理
    $Database=null;
}

?>