<?php
require_once("plib/head.php");

$cgi = getCGI();
gsql_esc($cgi);
$p_id = $cgi[p_id];
$ids = $cgi[ids];

if($p_id == "" || $ids == "") sys_exit("参数错误");

conProjDB($p_id);

$sp = explode(",", $ids);
foreach($sp as $kk=>$id)
{
	$name = $cgi["name$id"];
	$url = $cgi["url$id"];
	$showorder = $cgi["showorder$id"];

	if($showorder  == "") $showorder = "0";

	$sqlstr = sprintf("update navigation set name='%s', url='%s', showorder='%s' where id=$id", $name, $url, $showorder, $id);
	print "$sqlstr";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
}

gen_nav();
sys_jmp("navlist.php?p_id=$p_id");
?>
