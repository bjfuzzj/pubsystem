<?php
require_once("plib/head.php");
$nav_str .= " &gt; 修改网站";
$cgi = getCGI();
$p_id = $cgi[p_id];
if($p_id == "") sys_exit("参数错误");
conProjDB($p_id);

$sqlstr = "select * from proj where p_id=$p_id";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row_proj = mysql_fetch_array($res);

$sqlstr = "select * from polymorphic where pm_id=1";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row_poly = mysql_fetch_array($res);

print "is_window: $is_window\n";


if($cgi[edit] != "" && $cgi[p_cname] != "")
{
	$sqlstr = sprintf("update proj set p_cname='%s', updatedt=now() where p_id=$p_id", mysql_escape_string($cgi[p_cname]));
	$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . "\n" . mysql_error());

	$sqlstr = sprintf("update polymorphic set html_urlbase='%s', file_path='%s'  where pm_id=1", mysql_escape_string($cgi[domain]), mysql_escape_string($cgi[file_path]) );
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . "\n" . mysql_error());

	if($row_poly[file_path] != $cgi[file_path])
	{

		$file_path = $cgi[file_path];
		if($file_path == "") $file_path = "$file_base/$db_name/pc";

		pub_mkdir("$file_path/upload");
	
		$cmd = "cd $file_path; rm -f  temp_view.php; ln -s  $root_path/temp_view.php temp_view.php";
		system($cmd);

		$cmd = "cd $file_path; rm -f addmess.php; ln -s  $root_path/addmess.php addmess.php";
		system($cmd);

		$cmd = "cd $file_path;  ln -s  $root_path/plib plib";
		system($cmd);

		$cmd = "cd $file_path;  ln -s  $root_path/pagelib pagelib";
		system($cmd);
	}

	system("cd $root_path; /usr/bin/php gen_domain.php");
	sys_jmp("projlist.php");
	exit;
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<meta http-equiv="Pragma" content="no-cache">
<title>修改网站</title>

<link href="css/main.css" rel="stylesheet" type="text/css" />

<script language=javascript>
var submit_flag =0;
function check_form(this_form)
{
	if(submit_flag == 0)
	{

		if(this_form.p_cname.value.length == 0)
		{
			alert("网站名称不能为空");
			this_form.p_cname.focus();
			return false;
		}
		submit_flag = 1;
		return true;
	}
	else
	{
		alert("该页面已经提交，请等待");
		return false;
	}
}

</script>


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
<br>
<br>


<form action="proj_edit.php" name=myform method=post onsubmit="return check_form(this);">
<input type=hidden name=edit value=1>
<input type=hidden name=p_id value=<?php echo $p_id ?> >

<table style="border:1px solid #333; background:#eee; padding:15px;">

<tr height=40>
<td align=right><b>网站名称:</b></td>
<td><input type=text name="p_cname" size=60 value="<?php echo htmlspecialchars($row_proj[p_cname]); ?>"></td>
<td><?php echo $notify_notnull ?></td>
</tr>

<tr height=40>
<td align=right height=30><b>网站域名:</b></td>
<td><input type=text name="domain" size=60 value="<?php echo htmlspecialchars($row_poly[html_urlbase]); ?>"></td>
<td></td>
</tr>

<tr height=40>
<td align=right height=30><b>网站存放路径:</b></td>
<td><input type=text name="file_path" size=60 value="<?php echo $row_poly[file_path] ?>"></td>
<td><span style="color:#f00; font-size:12px;"></td>
</tr>

<tr valign=top>
<td align=right></td>
<td><span style="color:#f00; font-size:12px;">可以为空，如果为空，则按照默认规则存放在网站默认根路径下</span></td>
<td></td>
</tr>

<tr> <td colspan=3 align=center> <input type=submit value="提  交"> &nbsp;&nbsp;&nbsp;<input type=reset value="重  写">
</td>
</tr>
</table>

</form>


</body>
</html>
