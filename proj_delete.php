<?php
require_once("plib/head.php");
$nav_str .= " &gt; 修改网站";
$cgi = getCGI();
$p_id = $cgi[p_id];
if($p_id == "") sys_exit("参数错误");

$sqlstr = "select * from proj where p_id=$p_id";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row_proj = mysql_fetch_array($res);
if($row_proj == "")
{
	sys_jmp("projlist.php");
}

if($row_proj[u_id] != $ck_u_id && $ck_u_type != 0) sys_exit("对不起，你没有对该网站的删除权限。", "");

$sqlstr = "drop database $row_proj[db_name]";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error(), false);

$sqlstr = "delete from proj where p_id=$p_id";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

$db_name = trim($row_proj[db_name]);

if($db_name != "")
{
//	$cmd = sprintf("rm -rf $file_base/%s", $db_name);
//	system($cmd);
}

system("cd $root_path/cgi; ./gen_domain.cgi");
sys_jmp("projlist.php");
?>
