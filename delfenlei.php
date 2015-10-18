<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_id = $cgi[d_id];
$d_ids = $_POST[d_ids];
$parent_id = $cgi[parent_id];


if($p_id == "" || $t_id == "" || ($d_id == "" && $d_ids == "") ) sys_exit("参数错误");

if($d_id != "") $d_ids[] = $d_id;


conProjDB($p_id, $t_id);


$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$t_name = $temp_data[$t_id][t_name];


if( check_priv($p_id, $t_id, $d_id) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);
if($ck_u_type > 2 && $ck_u_type < 100)  sys_exit("对不起，你没有删除权限",   $error_message);

foreach($d_ids as $d_id)
{
	$sqlstr = "select id, cname from $t_name where d_id=$d_id";
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if($row == "") exit("数据不存在: $sqlstr");
	$cname = $row[cname];
	$sqlstr = "select id from $t_name where pid=$row[id] limit 1";
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);

	if($row != "") sys_alert("分类\"$cname\"不能删除，其下还有子分类", "fenleilist.php?t_id=$t_id&p_id=$p_id&parent_id=$parent_id"); 

	$sqlstr = sprintf("delete from %s where d_id=%s", $t_name, $d_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
}

printf("Content-type: text/html\n\n<script type=\"text/javascript\"> window.location = 'fenleilist.php?t_id=%s&p_id=%s&parent_id=%s' </script>", $t_id, $p_id, $parent_id);

?>
