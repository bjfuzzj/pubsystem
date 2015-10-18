<?php

require_once("plib/head.php");
require_once("plib/priv.php");


$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$f_ids = $_POST[f_id];


if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);

if($ck_u_type > 2 ) sys_exit("对不起，你没有操作权限",   "");
if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);
$t_name = $temp_data[$t_id][t_name];
	
foreach($f_ids as $f_id)
{

	$sqlstr = sprintf("select f_name, type, if_into_db from tempdef where f_id=%s", $f_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res) or exit(mysql_error() . "\n" . $sqlstr);

	if($row == "") continue;
	$f_name= $row[f_name];
	$type= $row[type];
	$if_into_db= $row[if_into_db];
	
	
	$sqlstr = sprintf("delete from tempdef where f_id=%s", $f_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		
	if($if_into_db == "y")
	{
		$sqlstr = sprintf("alter table %s drop %s", $t_name, $f_name);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	}
}

printf("<script type=\"text/javascript\"> window.location = 'tempdeflist.php?t_id=%s&p_id=%s' </script>\n", $t_id, $p_id);

?>
