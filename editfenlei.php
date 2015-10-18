<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_id = $cgi[d_id];

if($p_id == "" || $t_id == "" || $d_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);


$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$t_name = $temp_data[$t_id][t_name];




$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; <a href=doclist.php?p_id=$p_id&t_id=$t_id >$t_cname</a> &gt; 修改文档($d_id)";

if($cgi[edit] != "" )
{
        if( check_priv($p_id, $t_id, $d_id) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);
        $t_name  = $temp_data[$t_id][t_name];
        $t_cname = $temp_data[$t_id][cname];

	$sqlstr = sprintf("update %s set savedatetime=now(), mu_id=%s,", $t_name,  $ck_u_id);
	foreach($cgi as $this_name=>$this_value)
	{
		if($this_name == "" || $this_value == "") continue;
		
		
		$pos = strpos($this_name, $pre_field);
		if($pos !== 0) continue;

		$radio_value = $cgi["radio_$this_name"];
		if($radio_value)
		{
			if($radio_value == "old") continue;
		}

		$ff = substr($this_name, strlen($pre_field));
		$sqlstr .= sprintf(" %s='%s',", $ff, mysql_escape_string($this_value));
	}

	
	foreach($poly_data as $pm_id=>$this_poly)
	{
		$doc_url = $cgi["doc_url_$pm_id"];
		$sqlstr .= sprintf(" url_%d='%s',", $pm_id, $doc_url);
	}
	
	$slen = strlen($sqlstr) - 1;
	$sqlstr{$slen} = ' ';
	$sqlstr .= sprintf(" where d_id=%s", $d_id);

	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

        $sqlstr = "select id, cname from $t_name where d_id=$d_id";
        $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
        $row = mysql_fetch_array($res);
        if($row == "") exit("数据不存在: $sqlstr");
	$parent_id = $row[pid];
        if($row != "") sys_alert("分类更新成功", "fenleilist.php?t_id=$t_id&p_id=$p_id&parent_id=$parent_id");

	exit;
}

$rel_select_flag = 0;
$rich_text_flag = 0;
$date_input_flag = 0;

foreach($tempdef_data as $kk=>$row)
{
	if($row[type] == 'Date') $date_input_flag = 1;
	if($row[type] == 'RichText') $rich_text_flag = 1;
	if($row[type] == 'Rel_Select') $rel_select_flag = 1;
}


//$formlist = `cd ../../interface/; ./doc_edit.cgi "p_id=$p_id&t_id=$t_id&d_id=$d_id"`;
$formlist = get_doc_edit_html($p_id, $t_id, $d_id);

?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" src=js/gong.js></script>




<?php

if($rich_text_flag)
{
	print <<<END_OF_GHC
<link rel="stylesheet" href="/css/richtext.css" type="text/css" />
<script language="javascript" src="/js/JSFunc.js"></script>
<script language="javascript" src="/js/LibFunc.js"></script>
<script language="javascript" src="/js/gfunc.js"></script>
<script language="javascript" src="/js/LibDoc.js"></script>
<script language="javascript" src="/js/typeset.js"></script>
<script language="javascript" src="/js/richtext.js"></script>
END_OF_GHC;

}


if($rel_select_flag)
{

	print "<script language=\"JavaScript\" src=/js//html_sel.js></script>\n";
}


if($date_input_flag)
{
	print <<<END_OF_GHC
<script language="javascript" src="/js/jquery-1.3.2.min.js"></script>
<link rel="stylesheet" href="/css/ui.datepicker.css" type="text/css" />
<script type="text/javascript" src="/js/ui.datepicker.js"></script>
<script type="text/javascript" src="/js/ui.datepicker-zh-CN.js"></script>
END_OF_GHC;

}

?>

</head>

<body>

<center>
<table width=100% border=0>
<tr valign=bottom>
<td><?php echo $nav_str ?></td>
<td align=right valign=bottom><?php echo $hello_str; ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#808080></td></tr>
</table>

<form method=post action="view_content.php" name=form_view target=_blank onsubmit="alert(this.content.value); return true;">
	<input type=hidden name=p_id value='<?php echo $p_id ?>'> 
	<input type=hidden name=t_id value='<?php echo $t_id ?>'> 
	<input type=hidden name=content value="">
</form>

<style type="text/css">
#doc_table {border:2px solid #ccc; background:#f1f1f1; padding:15px;}
#url_table {border:2px solid #ccc; background:#f1f1f1; padding:15px;}
</style>

<form method=post name=myform encType="multipart/form-data" action="editfenlei.php" onsubmit ="return checkForm();">
<input type=hidden name=p_id value="<?php echo $p_id ?>"> 
<input type=hidden name=t_id value="<?php echo $t_id ?>"> 
<input type=hidden name=d_id value="<?php echo $d_id ?>"> 
<input type=hidden name=edit value=1 > 

<?php echo $formlist ?>

<br>
<INPUT TYPE=reset VALUE="重  写">
&nbsp;&nbsp;&nbsp;
<INPUT TYPE=submit VALUE="提  交">
</form>
<br><br>


</body>
</html>
