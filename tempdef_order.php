<?php
require_once("plib/head.php");
require_once("plib/priv.php");


$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);

if($ck_u_type > 2 ) sys_exit("对不起，你没有操作权限",   "");
if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);

	
foreach($cgi as $cgi_name=>$cgi_value)
{
		
	$pos = strpos($cgi_name, "showorder_");
	if($pos === 0)
	{
		$f_id = substr($cgi_name, strlen("showorder_"));
		$sqlstr = sprintf("update tempdef set showorder=%s where f_id=%s", $cgi_value, $f_id);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	}
}
	
printf("<script type=\"text/javascript\"> window.location = 'tempdeflist.php?t_id=%s&p_id=%s' </script>\n", $t_id, $p_id);

?>
