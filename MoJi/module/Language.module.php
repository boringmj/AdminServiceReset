<?php

/* 语言模块
 * 翻译来源于谷歌翻译和有道翻译
 * 如果您想要提供更多的语言支持可以向wuliaodemoji@wuliaomj.com发送邮件并且注明语言
 * 如果您的语言模块审核通过,我们将会匿名新增到新的版本中作为可选语言支持
 * 语言模块仅允许使用字符串(String)作为值
 *
 * Language module
 * Translation comes from Google Translate and Youdao Translate
 * If you want to provide more language support, you can send an email to wuliaodemoji@wuliaomj.com and indicate the language
 * If your language module is approved, we will add it to the new version anonymously as optional language support
 * The language module only allows to use string(String) as a value
 */

$language_path=RES_PATH.'/language/'.DEFAULT_LANGUAGE.'.php';
$language_path_temp=RES_PATH.'/language/'.CURRENT_LANGUAGE.'.php';
if(is_file($language_path_temp))
    $language_path=$language_path_temp;
include $language_path;
include RES_PATH.'/language/_log.php';
config_examine('LANGUAGE');
config_examine('LANGUAGE_LOG');

?>