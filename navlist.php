<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];

if($p_id == "") sys_exit("参数错误");
conProjDB($p_id);

$p_cname = $proj_data[p_cname];
$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; <a href=globallist.php?p_id=$p_id>全局变量</a> &gt; 导航条管理";

$list = "";
$sqlstr = "select * from navigation order by showorder asc, id asc";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$ids_str = "";
while($row = mysql_fetch_array($res))
{
	$id = $row[id];

	$oper_str = "<a href=nav_delete.php?p_id=$p_id&id=$id onclick=\"return confirm('确认要删除该导航条目吗'); \">删除</a>";

	$list .= "<tr class=tdata>
	<td><input name=name$id value=\"$row[name]\" size=20></td>
	<td><input name=url$id value=\"$row[url]\" size=40></td>
	<td><input name=showorder$id value=\"$row[showorder]\" size=5></td>
	<td>$oper_str</td>
	</tr>\n";

	if($ids_str == "")
	{
		$ids_str = $id;
	}
	else
	{
		$ids_str .= "," . $id;
	}
}


$code_data = array();

$sqlstr = "select * from navcode";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while( $row = mysql_fetch_array($res) )
{
	$code_data["$row[name]"] = $row[content];
}


?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<title><?php echo $p_cname ?> -- 导航条管理</title>
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
			alert("导航条管理名称不能为空");
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


<form name=myform method=post action="navcode_update.php">
<input type=hidden name=p_id value="<?php echo $p_id ?>" >

<table width=95% border=0 style="border:2px solid #ccc; background:#eee; padding:15px;">
<tr height="10"><td align="right" colspan="2"> </td></tr>

<tr>
<td width=25></td>
<td>导航栏目条目代码(未选中状态下)</td>
</tr>

<tr valign=top>
<td width=20></td>
<td>
<textarea wrap=soft name=code1 style="width: 600px; height: 80px;">
<?php  echo htmlspecialchars($code_data[code1]) ?>
</textarea> </td>
</tr>

<tr>
<td width=25></td>
<td>导航栏目条目代码(选中状态下)</td>
</tr>

<tr valign=top>
<td width=20></td>
<td>
<textarea wrap=soft name=code2 style="width: 600px; height: 80px;">
<?php  echo htmlspecialchars($code_data[code2]) ?>
</textarea> </td>
</tr>

<tr>
<td width=25></td>
<td>导航栏目条目间隔代码</td>
</tr>

<tr valign=top>
<td width=20></td>
<td>
<textarea wrap=soft name=code4 style="width: 600px; height: 80px;">
<?php  echo htmlspecialchars($code_data[code4]) ?>
</textarea> </td>
</tr>

<tr>
<td width=25></td>
<td>导航栏目外围代码</td>
</tr>

<tr valign=top>
<td width=20></td>
<td>
<textarea wrap=soft name=code3 style="width: 600px; height: 80px;">
<?php  echo htmlspecialchars($code_data[code3]) ?>
</textarea> </td>
</tr>

<tr>
<td colspan=2 align=center>
<input type=submit value="修改导航条条目代码">
<?php echo html_space(25); ?>
</td>
</tr>
</table>
</form>



<script type="text/javascript">
function additem(thisform)
{
	thisform.action = 'nav_new.php';
	thisform.submit();
}
function edititem(thisform)
{
	thisform.action = 'nav_update.php';
	thisform.submit();
}
</script>



<form action="" name=fields_list_form method=post>
<input name=p_id type=hidden value="<?php echo "$p_id" ?>" >
<input name=ids  type=hidden value="<?php echo $ids_str ?>" >
<table>
<tr>
<td valign=top>

<td>
</tr>
</table>

<table id=list_table width=95% cellspacing=2 cellpadding=3 border=0>
<tr class=tdata style="height:40px;"><td colspan=10 align=center> 
<span style="font-weight:bold; font-size:18px;">导航条栏目列表</span>
</td>

</tr>

<tr class=theader>
<td>栏目名称</td>
<td>栏目URL</td>
<td>显示顺序</td>
<td>操作</td>
</tr>

<?php echo $list ?>

<tr class=tdata style="height:50px;">
<td><input name=name value="" size=20></td>
<td><input name=url value="" size=40></td>
<td><input name=showorder value="0" size=5></td>
<td><input type=button value="新增栏目" onclick="additem(this.form);"></td>
</tr>


<?php
if($list != "")
{
?>

<tr class=tdata><td colspan=4> <input type=button value="修改导航条条目" onclick="edititem(this.form);"></td></tr>

<?php
}
?>

</table>


</form>
</body>
</html>
