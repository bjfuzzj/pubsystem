<?php
require_once("plib/head.php");

$cgi = getCGI();
gsql_esc($cgi);
$p_id = $cgi[p_id];
if($p_id == "") sys_exit("参数错误");
conProjDB($p_id);

$sqlstr = sprintf("insert into global set name='%s', content='%s', type='$cgi[gtype]'", $cgi[cname], $cgi[content]);
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
sys_jmp("globallist.php?p_id=$p_id");


?>
