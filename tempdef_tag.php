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


$tag_data = array();
foreach($f_ids as $f_id)
{
	$f_name = $tempdef_data[$f_id][f_name];
	$tag_data[] = $f_name;
}

$field_tag = join(",", $tag_data);

$sqlstr = sprintf("update temp set field_tag='%s' where t_id=%s", $field_tag, $t_id);
$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

printf("<script type=\"text/javascript\"> window.location = 'tempdeflist.php?t_id=%s&p_id=%s' </script>\n", $t_id, $p_id);

?>
