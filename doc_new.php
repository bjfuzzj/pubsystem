<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");
$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_id = $cgi[d_id];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");
conProjDB($p_id, $t_id);

$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$t_name = $temp_data[$t_id][t_name];

$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; <a href=doclist.php?p_id=$p_id&t_id=$t_id >$t_cname</a> &gt; 添加文档";

if($cgi[edit] != "")
{

	if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);

	upload_pic();

	$t_name  = $temp_data[$t_id][t_name];
	$t_cname = $temp_data[$t_id][cname];
	$nav_buf = sprintf("/<a href=\"projlist.php\">网站管理中心</a> &gt; <a href=\"templist.php?p_id=%s\">%s</a> &gt; %s(<a href=\"doclist.php?t_id=%s&p_id=%s\">文档</a>) (<a href=\"temp_edit.php?t_id=%s&p_id=%s\">模板</a>) (<a href=\"tempdeflist.php?t_id=%s&p_id=%s\">模板域</a>) &gt; 添加文档", $p_id, $proj_data[p_cname], $t_cname, $t_id, $p_id, $t_id, $p_id, $t_id, $p_id );

	print_html("添加文档", $nav_buf);

	printf("添加数据库记录....");

	$sqlstr = sprintf("insert into %s (cu_id, mu_id, createdatetime, savedatetime, published) values(%s, %s, now(), now(), 'n')", $t_name, $ck_u_id, $ck_u_id);

	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统错误,请稍候再试", mysql_error() . "\n" . $sqlstr);
	
	$d_id=mysql_insert_id($proj_mysql);
	
	
	$sqlstr = sprintf("update %s set", $t_name);
	foreach($poly_data as $pm_id=>$this_poly)
	{
		
		$url_radio = $cgi["urlradio_$pm_id"];
		$outer_url = $cgi["outer_url_$pm_id"];
		$default_url=$cgi["default_url_$pm_id"];
		$sqlstr .= sprintf(" url_%d='%s',", $pm_id, $url_radio ==  "default"? $default_url : $outer_url);
	}
	foreach($cgi as $this_name => $this_value)
	{
		if($this_name == "" || $this_value == "") continue;
		
		$mark = sprintf("%spoly_", $pre_field);
		
		$pos = strpos($this_name, $mark);
		if($pos === 0)
		{
			$ff = substr($this_name,strlen($mark));
			foreach($poly_data as $pm_id=>$this_poly)
			{
				$sqlstr .= sprintf( " %s_%d='%s',", $ff, $pm_id,  mysql_escape_string($this_value));
			}
		}
		else
		{
			$pos1 = strpos($this_name, $pre_field);
			if($pos1 !== 0) continue;
			$ff = substr($this_name, strlen($pre_field));
			$sqlstr .= sprintf(" %s='%s',", $ff, mysql_escape_string($this_value));

		}
	}

	$slen = strlen($sqlstr) - 1;
	$sqlstr{$slen} = ' ';
	$sqlstr .= sprintf(" where d_id=%ld", $d_id);

	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

	doAboutDataPost($p_id, $t_id, $d_id);

	printf("完成!<br>\n");
	if(publishOneDoc($p_id, $t_id, $d_id) < 0)
	{
		exit($error_message);
	}

	exit;
}

//$formlist = `cd ../../interface/; ./doc_new.cgi "p_id=$p_id&t_id=$t_id"`;
$formlist = get_doc_new_html($p_id, $t_id);
?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/richtext.css" type="text/css" />

<script language="JavaScript" src=js/gong.js></script>
<script language="JavaScript" src=js//html_sel.js></script>
<script language="javascript" src="js/jquery-1.3.2.min.js"></script> 
<script language="javascript" src="js/JSFunc.js"></script>
<script language="javascript" src="js/LibFunc.js"></script>
<script language="javascript" src="js/gfunc.js"></script>
<script language="javascript" src="js/LibDoc.js"></script>
<script language="javascript" src="js/typeset.js"></script>
<script language="javascript" src="js/richtext.js"></script>


<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" />
<script type="text/javascript" src="js/ui.datepicker.js"></script>
<script type="text/javascript" src="js/ui.datepicker-zh-CN.js"></script>

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

<form method=post name=myform encType="multipart/form-data" action="doc_new.php" onsubmit ="return checkForm();">
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
