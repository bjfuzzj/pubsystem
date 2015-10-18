<?php
require_once("plib/head.php");


$user_data = array();

$sqlstr = "select * from user";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while($row = mysql_fetch_array($res) )
{
	$user_data["$row[id]"] = $row;
}


$i=1;
$projlist = "";
if($ck_u_type > 100)
{
	$sqlstr = "select * from proj where validation = 0 and u_id=$ck_u_id order by p_id asc";
}
else  if($ck_u_type == 0 || $ck_u_allproj != 0 )
{
	$sqlstr = "select p_id, p_cname from proj where validation=0 order by p_id";
}
else
{
	$sqlstr = sprintf("select p_id, p_cname from proj where validation=0 and (p_id in (0 %s) or u_id=$ck_u_id) order by p_id", $ck_u_priv);
}



$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while($row = mysql_fetch_array($res))
{
	$p_id = $row[p_id];
	$p_cname = $row[p_cname];

	$title_str = sprintf("%s %s", $user_data["$row[u_id]"]['name'], $row[createdt]);

	$projlist .= "<tr height=27><td><input type=radio name=p_id value=\"$p_id\"></td><td>$p_id</td><td><a href=\"templist.php?p_id=$p_id\" title=\"$title_str\">$p_cname</a>  $user_str</td></tr>";

	$i++;
}


$menu_flag = $ck_u_type != 3;
if($projlist == "")
{
	$projlist = "<tr height=40><td> 您的名下没有网站， 请先<a href=proj_new.php>创建网站</a> </td></tr>";
	$menu_flag = false;
}

?>

<html>
<head>
<title>网站列表</title>
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
		if( my_form.elements[index].name == "p_id" && my_form.elements[index].checked==true)
		{
			my_form.action=_action+'?p_id='+my_form.elements[index].value;
			ele_checked=true;
			if(_action=="proj_delete.php"){
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
	if(!ele_checked){ alert("请先选择一个网站");return false;}
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
<?php if($ck_u_type === "0") echo "<td><a href=userlist.php>用户管理</a></td>" ?>
<td><a href=proj_new.php>新建网站</a></td>
<td><a href="" onclick="actionclick(self.document.myform, 'proj_edit.php'); return false;">修改网站</a></td>
<td><a href="" onclick="actionclick(self.document.myform, 'proj_delete.php'); return false;">删除网站</a></td>
</tr>
</table>
<?php } ?>
<br>

<form name=myform action="" method=post>
<table border=0>
<?php echo $projlist ?>
</table>
</form>

</body>
</html>

