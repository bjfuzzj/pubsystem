<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
if($p_id == "") sys_exit("参数错误");
conProjDB($p_id);


$p_cname = $proj_data[p_cname];
$nav_str .= " &gt; $p_cname";

$i=1;
$templist = "";
$sqlstr = "select * from temp order by showorder asc, t_id asc";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while($row = mysql_fetch_array($res))
{
	$t_id = $row[t_id];
	$t_cname = $row[cname];

	$templist .= "<tr height=27><td><input type=radio name=t_id value=\"$t_id\"></td><td>$t_id</td><td><a href=\"doclist.php?p_id=$p_id&t_id=$t_id\">$t_cname</a></td></tr>";

	$i++;
}


$menu_flag = $ck_u_type != 3;
if($templist == "")
{
	$templist = "<tr height=40><td><b>$p_cname</b>下没有模板, 请先<a href=temp_new.php?p_id=$p_id>创建模板</a> </td></tr>";
	$menu_flag = false;
}

?>

<html>
<head>
<title><?php echo $p_cname ?> -- 模板列表</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<!-- meta http-equiv="Pragma" content="no-cache" -->

<link href="css/main.css" rel="stylesheet" type="text/css" />
<script language=javascript>
function actionclick(my_form,_action)
{
	len     =       my_form.elements.length;
	var     index   =       0;
	var     ele_checked=false;
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == "t_id" && my_form.elements[index].checked==true)
		{
			my_form.action=_action+'&t_id='+my_form.elements[index].value;
			ele_checked=true;
			if(_action.indexOf("temp_delete.php") >= 0 ){
				if(confirm("请确认是否真的删除?")){
					my_form.submit();return true;
					}else{
					return false;
				}
			}else
			{
				my_form.submit();return true;
			}
		}
	}
	if(!ele_checked){ alert("请先选择一个模板");return false;}
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

<?php if($menu_flag) { ?>
<table width=350 border=0>
<tr>
<td><a href=temp_new.php?p_id=<?php echo $p_id; ?> >新建模板</a></td>
<td><a href="" onclick="actionclick(self.document.myform, 'temp_edit.php?p_id=<?php echo $p_id ?>'); return false;">修改模板</a></td>
<td><a href="" onclick="actionclick(self.document.myform, 'temp_delete.php?p_id=<?php echo $p_id ?>'); return false;">删除模板</a></td>
<td><a href="<?php printf("globallist.php?p_id=%s", $p_id); ?>">全局变量</a></td>
</tr>
</table>
<?php } ?>
<br>

<form name=myform action="" method=post>
<table border=0>
<?php echo $templist ?>
</table>
</form>

</body>
</html>

