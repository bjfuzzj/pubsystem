<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];

if($p_id == "") sys_exit("参数错误");
conProjDB($p_id);

$p_cname = $proj_data[p_cname];
$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; 全局变量";

$globallist = "";
$this_row = "";
$sqlstr = "select * from global order by id asc";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while($row = mysql_fetch_array($res))
{
	$id = $row[id];

	if($cgi[id] == $id)
	{
		$name_str = "<a href=globallist.php?p_id=$p_id&id=$id><b>$row[name]</b></a>";
		$this_row = $row;
	}
	else
	{
		$name_str = "<a href=globallist.php?p_id=$p_id&id=$id>$row[name]</a>";
	}

	if($row[type] == 'text')
	{
		$type_str = "静态变量";
	}
	else if($row[type] == 'js')
	{
		$type_str = "动态变量";
	}
	else
	{
		$type_str = "其他变量";
	}

	$oper_str = "<a href=global_delete.php?p_id=$p_id&id=$id onclick=\"return confirm('确认要删除该全局变量吗?'); \">删除</a>";

	$globallist .= "<tr class=tdata>
	<td>$id</td>
	<td>$name_str</td>
	<td>$type_str</td>
	<td>$row[content]</td>
	<td>$oper_str</td>
	</tr>\n";
	$i++;
}

$title_str = $cgi[id] == ""? "添加全局变量":"修改全局变量";
$action_str = $cgi[id] == ""? "global_new.php":"global_update.php";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<title><?php echo $p_cname ?> -- 全局变量</title>
<link href="css/main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#tempdef_table {border:2px solid #ccc; background:#f1f1f1; padding:15px; text-align:left;}
#list_table {border:2px solid #ccc; background:#fff;}
#arith_html,#field_html,#table_field {font-size: 12px;}
.theader {background:#ccc; font-weight:bold; height:30px; text-align:center}
.tdata{background:#ccc; background:#eee; height:30px; text-align:center}
</style>

<script language=javascript>
var submit_flag =0;
function checkForm(this_form)
{
	if(submit_flag == 0)
	{

		if(this_form.cname.value.length == 0)
		{
			alert("全局变量名称不能为空");
			this_form.cname.focus();
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



<form name=myform method=post action=<?php echo $action_str; ?> onsubmit="return checkForm(this);">
<input type=hidden name=p_id value="<?php echo $p_id ?>" >
<input type=hidden name=id value="<?php echo $this_row[id] ?>" >

<table width=95% border=0 style="border:2px solid #ccc; background:#eee; padding:15px;">
<tr height="10"><td align="right" colspan="2"> </td></tr>

<tr>
<td align=center>变量名称</td>
<td width=90%><input type=text size=40 name=cname value="<?php echo htmlspecialchars($this_row[name]); ?>"> <span style="color:#f00;font-size:12px;">必填</span></td>
</tr>

<tr>
<td align=center>变量类型</td>
<td width=90%>
<select name=gtype>
<option value="text">静态变量</option>
<option value="js">动态变量</option>
</select>
<font style="font-size:12px; color:green;">模板要使用动态变量，需要在模板的html代码前面的位置嵌入如下代码: <br>
&nbsp;&nbsp;&nbsp;&nbsp;
&lt;script src="/pub_global.js"&gt;&lt;/script&gt;
</font>
</td>
</tr>

<tr>
<td align=center>变量内容</td>
<td></td>
</tr>

<tr valign=top>
<td></td>
<td>
<textarea wrap=soft name=content style="width: 700px; height: 200px;">
<?php  echo htmlspecialchars($this_row[content]) ?>
</textarea>
</td>
</tr>

<tr>
<td align=center colspan=2>
<input type=submit value="<?php echo $title_str; ?>">
<?php echo html_space(25); ?>
</td>
</tr>

</table>
</form>

<script type="text/javascript">
function set_sel_value(this_sel, vv)
{
	for(var i=0; i<this_sel.options.length; i++)
	{
		if(this_sel.options[i].value == vv)
		{
			this_sel.options[i].selected= true;
		}
	}
}
set_sel_value(document.myform.gtype, '<?php echo $this_row[type] == "" && $this_row[name] != ""? "js":$this_row[type] ?>')
</script>



<form action="" name=fields_list_form method=post>
<input name=p_id type=hidden value="<?php echo "$p_id" ?> ">
<table>
<tr>
<td valign=top>

<td>
</tr>
</table>

<table id=list_table width=95% cellspacing=2 cellpadding=3 border=0>
<tr class=tdata style="height:40px;">
<td colspan=5 align=center> 
<span style="font-weight:bold; font-size:18px;">全局变量列表</span>
<span style="float:right"><a href=<?php echo "navlist.php?p_id=$p_id" ?> >导航条管理</a>&nbsp;</span>
</td>

</tr>

<tr class=theader>
<td>编号</td>
<td>变量名称</td>
<td>变量类型</td>
<td>变量内容</td>
<td>操作</td>
</tr>

<?php echo $globallist ?>

</table>

</form>

</body>
</html>
