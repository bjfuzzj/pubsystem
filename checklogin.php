<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/db.php");
require_once("plib/global_func.php");

$cgi = getCGI();
gsql_esc($cgi);

$sqlstr = "select * from user where login='$cgi[admin]'";
$res = mysql_query($sqlstr, $pub_mysql) or exit("系统忙， 请稍候再试。" .  $sqlstr . ":\n" . mysql_error());
if(mysql_num_rows($res) == 0) exit("OK");

exit("登录名 \"$cgi[admin]\" 已经存在，请选择其他登录名");
?>
