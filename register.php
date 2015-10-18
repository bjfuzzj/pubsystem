<?php
require_once("plib/db.php");
require_once("plib/global_func.php");

$cgi = getCGI();
gsql_esc($cgi);

if($cgi[admin] == "") sys_exit("登录名不能为空");
if($cgi[pwd] == "") sys_exit("登录名不能为空");
if($cgi[linkman] == "") sys_exit("联系人不能为空");
if($cgi[phone] == "") sys_exit("联系电话不能为空");
$salt = getSalt();
$password = md5($cgi['pwd'].$salt);
$sqlstr = "insert into user set name='注册用户:$cgi[linkman]', passwd='$password', login='$cgi[admin]', linkman='$cgi[linkman]', phone='$cgi[phone]', type=101, createdt=now(), salt='$salt'";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

?>

<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
	<meta http-equiv="Pragma" content="no-cache">
	<title>注册成功</title>

	<link href="css/main.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
	<center><br><br><br>
	<table style="border:1px solid #333; background:#eee; padding:0px; width:350px;"  cellpadding=0 cellspacing=0>
	<tr height=25 bgcolor=#bbbbbb><td align=center colspan=3><b>注册成功</b></td></tr>
	<tr height=10><td align=center colspan=3></td></tr>
	<tr height=80>

	<td width=10></td>
	<td align=center>

	账户注册成功，您的登录名是: "<font color=red><?php echo $cgi[admin] ?></font>"<br><br><br>
<a href=/>
请返回登录
</a>

	</td>
	
	<td width=10></td>
	</tr>

	<tr height=10><td align=center colspan=3></td></tr>
	</table>
	</body>
	</html>

