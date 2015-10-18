<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$id = $cgi[id];
if($p_id == "" || $id== "") sys_exit("参数错误");
conProjDB($p_id);

$sqlstr = sprintf("delete from global where id=$id", $cgi[cname], $cgi[content]);
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
sys_jmp("globallist.php?p_id=$p_id");

?>
