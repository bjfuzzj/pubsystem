<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);



$sqlstr = "select * from temp where t_id=$t_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row = mysql_fetch_array($res);
if($row == "") sys_exit("模板不存在", $sqlstr);

$t_name = $row[t_name];
$sqlstr = "drop table $t_name";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

$sqlstr = "delete from temp where t_id=$t_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

$sqlstr = "delete from tempdef where t_id=$t_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());


sys_jmp("templist.php?p_id=$p_id");

?>
