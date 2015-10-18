<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$parent_id = $cgi[parent_id];


if($cgi[cname] == "") sys_alert("分类名称不能为空", "fenleilist.php?p_id=$p_id&t_id=$t_id&parent_id=$parent_id");

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");
if($parent_id == "") $parent_id = 0;

conProjDB($p_id, $t_id);

if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);
$t_name  = $temp_data[$t_id][t_name];

$id = get_fenlei_id($t_name, $parent_id);
//exit("== $id ==");

$sqlstr = sprintf("insert into %s (cu_id, mu_id, createdatetime, savedatetime, published) values(%s, %s, now(), now(), 'n')", $t_name, $ck_u_id, $ck_u_id);

$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统错误,请稍候再试", mysql_error() . "\n" . $sqlstr);
$d_id=mysql_insert_id($proj_mysql);
	
$cname = mysql_escape_string($cgi[cname]);
$sqlstr = "update $t_name set pid='$parent_id', id='$id', cname='$cname' where d_id=$d_id";
$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

header("Location: fenleilist.php?p_id=$p_id&t_id=$t_id&parent_id=$parent_id");
exit;

//--------------------------------------------------------------------------------------

function get_fenlei_id($t_name, $parent_id)
{
	global $proj_mysql;
	$sqlstr = "select id from $t_name where pid=$parent_id order by d_id desc limit 1";
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_assoc($res);
	if($row == "")
	{
		if($parent_id == "0") return "101";
		$id = "000";
	}
	else
	{
		$id = $row[id];
	}

	$id1 = substr($id, 0, strlen($id) - 3);
	$id2 = substr($id, strlen($id) - 3);

	if($id1 == "" && $parent_id != "0")
	{
		$id1 = $parent_id;
	}

	$id2 = intval($id2);
	$id2++;

	$id = sprintf("%s%03d", $id1, $id2);
	return $id;
}
?>
