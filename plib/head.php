<?php
//set_magic_quotes_runtime(0);
ini_set("magic_quotes_runtime",0);
require_once("config_inc.php");
require_once("global_func.php");
require_once("pub_cookie.php");
require_once("db.php");
require_once("GoogleAuthenticator.php");
require_once("phpqrcode/phpqrcode.php");
$today = date('Y-m-d h:i:s');
$hello_str = "您好,$ck_u_name(<a href=logout.php>退出</a>), 欢迎使用网站管理平台($today)";
$nav_str = "<a href=projlist.php>网站管理中心</a>";
$notify_notnull = "<span style=\"color:#f00;font-size:12px;\">必填</span>";

?>
