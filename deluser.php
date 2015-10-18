<?php
require_once("plib/head.php");
if($ck_u_type !== "0") exit("无权限进行此操作");
$cgi = getCGI();
$u_id = $cgi[u_id];
if($u_id == "") exit("Error parameter");

$sqlstr = sprintf("delete from  user where id=%s", $u_id);
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);

$sqlstr = sprintf("delete from  user_priv where u_id=%s", $u_id);
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);

header("Location: userlist.php");

?>
