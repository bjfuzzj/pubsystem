<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_id = $cgi[d_id];
$d_ids = $_POST[d_ids];


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
	$sqlstr = sprintf("delete from %s where d_id=%s", $t_name, $d_id);
	 $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

    //删除理财产品时 删除对应的理财标签详情信息
    if($t_id==2){
        $sqlstr = sprintf("delete from %s where lc_id=%s", 'lctag_detail', $d_id);
        $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
    }
    if($t_id==3){
        $sqlstr = sprintf("delete from %s where dk_id=%s", 'dktag_detail', $d_id);
        $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
    }
}

printf("Content-type: text/html\n\n<script type=\"text/javascript\"> window.location = 'doclist.php?t_id=%s&p_id=%s' </script>", $t_id, $p_id);

?>
