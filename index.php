<?php

$APP_PATH=dirname(__FILE__).'/AdminService';

define('APPLICATION_DEBUG',true);
define('APPLICATION_PATH',$APP_PATH);
define('DATABASE_ENABLE',true);
unset($APP_PATH);

require dirname(__FILE__).'/MoJi/Main.php';

?>