<?php	

require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");


$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_ids = $_POST[d_ids];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);


if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);
	
$t_name  = $temp_data[$t_id][t_name];
$t_cname = $temp_data[$t_id][cname];



$nav_buf = sprintf("/<a href=\"projlist.php\">网站管理中心</a> &gt; <a href=\"templist.php?p_id=%s\">%s</a> &gt; %s(<a href=\"doclist.php?t_id=%s&p_id=%s\">文档</a>) ", $p_id, $proj_data[p_cname], $t_cname, $t_id, $p_id);
if ($ck_u_type != 3){
	$nav_buf .= sprintf(" (<a href=\"temp_edit.php?t_id=%s&p_id=%s\">模板</a>) ", $t_id, $p_id);
	$nav_buf .= sprintf("(<a href=\"tempdeflist.php?t_id=%s&p_id=%s\">模板域</a>) ", $t_id, $p_id);	
}
$nav_buf .= sprintf("&gt; 发布文档");

print_html("发布文档", $nav_buf);

foreach($d_ids as $d_id)
{
	if( publishOneDoc($p_id, $t_id, $d_id) < 0)
	{
		printf("发布文档失败: %s<br>\n", $error_message);
	}
}

?>
