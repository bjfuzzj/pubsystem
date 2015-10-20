<?php
require_once("plib/head.php");
if($ck_u_type !== "0") exit("无权限进行此操作");

$nav_str .= " &gt <a href=userlist.php>用户列表</a> &gt 添加用户";

$cgi = getCGI();

gsql_esc($cgi);
$username = $cgi[username];
$login =  $cgi[login];
$passwd = $cgi[passwd];
$note = $cgi[note];
$type = $cgi[type];



if($username && $login && $type && $passwd)
{
    $salt=getSalt();
    $passwd=md5($passwd.$salt);
    $ga= new PHPGangsta_GoogleAuthenticator();
    $secret = $ga->createSecret();
	$sqlstr = sprintf("insert into user set name='%s', login='%s', passwd='%s', type='%s', note='%s',c_id=%s,secret='%s',salt='%s',createdt=now()",  $username, $login, $passwd, $type, $note, $ck_u_id,$secret,$salt);

	$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	header("Location: userlist.php");
	exit;
}

?>


<html>
<head>
<title>adduser</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />

<script language=javascript>
function checkForm(my_form)
{
	if(my_form.username.value == "")
	{
		alert("请填写用户名");
		my_form.username.focus();
		return false;
	}

	if(my_form.login.value == "")
	{
		alert("请填写登录名");
		my_form.login.focus();
		return false;
	}

	if(my_form.passwd.value == "")
	{
		alert("请填写密码");
		my_form.passwd.focus();
		return false;
	}

	return true;
}
</script>
</head>

<body>


<table width=100% border=0>
<tr valign=bottom>
<td><?php echo $nav_str ?></td>
<td align=right valign=bottom><?php echo $hello_str; ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#808080></td></tr>
</table>
<br>

<center>

<form name=myform action="adduser.php" method=post onsubmit=" return checkForm(this); ">
  <table width=601 align=center>
    <tr>
      <td height=35 align=right>用户名</td>
      <td><input name=username type=text  size=50 /></td>
    </tr>

    <tr>
      <td height=35 align=right>类&nbsp;&nbsp;型</td>
      <td>
	<select name=type>
	<option value=3>编辑人员
	<option value=2>二次开发人员
	<option value=1>普通管理员
	<option value=0>超级管理员
	</select>
      </td>
    </tr>

    <tr>
      <td height=35 align=right>登录名</td>
      <td><input name=login type=text  size=50 /></td>
    </tr>
    <tr>
      <td height=35 align=right>密&nbsp;&nbsp;码</td>
      <td><input name=passwd type=text size=50 /></td>
    </tr>

    <tr>
      <td width=113 height=125 align=right>备&nbsp;&nbsp;注</td>
      <td width=476><textarea name=note cols=60 rows=8 id=question></textarea></td>
    </tr>

    <tr>
      <td height=34 colspan=2 align=center><input type=submit name=Submit value=提&nbsp;&nbsp;&nbsp;&nbsp;交 />
      &nbsp;&nbsp;&nbsp;&nbsp;
	  <input type=reset name=Submit2 value=取&nbsp;&nbsp;&nbsp;&nbsp;消 /></td>
    </tr>
  </table>
</form>

</body>
</html>
