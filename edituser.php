<?php
require_once("plib/head.php");
if($ck_u_type !== "0") exit("无权限进行此操作");

$nav_str .= " &gt <a href=userlist.php>用户列表</a> &gt 修改用户";


$cgi = getCGI();

gsql_esc($cgi);
$u_id = $cgi[u_id];
$username = $cgi[username];
$login =  $cgi[login];
$passwd = $cgi[passwd];
$salt = getSalt();
$passwd = md5($passwd.$salt);
$note = $cgi[note];
$type = $cgi[type];
$allproj = $_POST[allproj][0];
$p_ids = $_POST[p_ids];

	

if($u_id == "") exit("Error parameter");


if($username && $login && $type && $passwd)
{

	if($allproj == "") $allproj = "0";

	$sqlstr = sprintf("update user set name='%s', login='%s', passwd='%s', salt='$salt', type='%s', note='%s', allproj=%s, m_id=%s, updatedt=now() where id=%s",  $username, $login, $passwd, $type, $note, $allproj, $ck_u_id, $u_id);
	$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);

		
	$sqlstr = sprintf("delete from user_priv where u_id=%s", $u_id);
	$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		
	$sqlstr = sprintf("insert into  user_priv values"); 
	$i = 0;
	if(is_array($p_ids))
	{
		foreach($p_ids as $this_p_id)
		{
			if($i==0)
				$sqlstr .= sprintf("(%s, %s)", $u_id, $this_p_id);
			else
				$sqlstr .= sprintf(",(%s, %s)", $u_id, $this_p_id);
			$i++;
		}
	}

	if($i > 0 && $allproj == "0")
	{
		$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	}


	header("Location: userlist.php");
	exit;
}




$sqlstr = sprintf("select  name, login, passwd, type, note, allproj from user where id=%s", $u_id);
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
$row_user = mysql_fetch_array($res, MYSQL_ASSOC);
if($row_user == "") sys_exit("没有记录", $sqlstr);

/*
	username = row[0]? row[0]:"";
	login = row[1]? row[1]:"";
	passwd = row[2]? row[2]:"";
	type = row[3]? row[3]:"4";
	note = row[4]? row[4]:"";
	allproj = row[5]?row[5]:"0";
*/
	


$sqlstr = sprintf("select p_id from user_priv where u_id=%s", $u_id);
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	
$user_priv = array(); 
while($row = mysql_fetch_array($res))
{
	$user_priv["$row[p_id]"] = 1;
}


$sqlstr = sprintf("select p_id, p_cname from proj where validation=0 order by p_id desc");
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
$buff = "";
$i = 0;
while($row = mysql_fetch_array($res, MYSQL_ASSOC))
{
	$proj_checked = "";
	if($user_priv["$row[p_id]"]) $proj_checked= "checked";


	if($i%3 == 0) 
	{
		if(i==0)
			$buff .= "<tr>";
		else
			$buff .= "</tr>\n<tr>";
	}
	$buff .= sprintf("<td><input type=checkbox name=p_ids[] value=\"%s\" %s>%s </td>", $row[p_id], $proj_checked, $row[p_cname]);

	$i++;
}

?>
	

<html>
<head>
<title>edituser</title>
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

<form name=myform action="edituser.php" method=post onsubmit=" return checkForm(this); ">
<input type=hidden name=u_id value="<?php echo $u_id ?>">
<input type=hidden name=u_type value="<?php echo $row_user[type]; ?>">

  <table width=601 align=center border=0>
    <tr>
      <td height=35 align=right>用户名</td>
      <td><input name=username type=text  size=50 value ="<?php echo $row_user[name]; ?>" /></td>
    </tr>

    <tr>
      <td height=35 align=right>类&nbsp;&nbsp;型</td>
      <td>
	<select name=type>
	<option value=101>注册人员
	<option value=3>编辑人员
	<option value=2>二次开发人员
	<option value=1>普通管理员
	<option value=0>超级管理员
	</select>
      </td>
    </tr>
<script language=javascript>
u_type= document.myform.u_type.value;
sel_utype = document.myform.type;
for(i=0; i<sel_utype.options.length; i++)
{
	if(sel_utype.options[i].value == u_type)
	{
		sel_utype.options[i].selected = true;
		break;
	}
	
}
</script>

    <tr>
      <td height=35 align=right>登录名</td>
      <td><input name=login type=text  size=50 value ="<?php echo $row_user[login]; ?>" /></td>
    </tr>
    <tr>
      <td height=35 align=right>密&nbsp;&nbsp;码</td>
      <td><input name=passwd type=text size=50 value ="<?php  ?>" /></td>
    </tr>

    <tr>
      <td width=113 height=125 align=right>备&nbsp;&nbsp;注</td>
      <td width=476><textarea name=note cols=60 rows=8 id=question><?php echo $row_user[note] ?></textarea></td>
    </tr>


    <tr>
      <td colspan=2 height=1 bgcolor=#eedeee >
      </td>
    </tr>

    <tr>
      <td height=35 align=right>权限分配</td>
      <td>&nbsp;</td>
    </tr>


    <tr>
      <td colspan=2>
 	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type=checkbox name=allproj value=1 onclick="allproj_change(this.form, this)"> 所有项目<br>
	<input type=hidden name=allproj_flag value="<?php echo $row_user[allproj] ?>" >
	

	<script language=javascript>
	function allproj_change(myform, allproj )
	{
		if(allproj.checked)
		{
			for(i=0; i<myform.elements.length; i++)
			{
				if(myform.elements[i].name == 'p_ids[]') 
				{
					myform.elements[i].checked= false;
					myform.elements[i].disabled = true;
				}

			}
		}
		else
		{

			for(i=0; i<myform.elements.length; i++)
			{
				if(myform.elements[i].name == 'p_ids[]') 
					myform.elements[i].disabled = false;

			}
		}
	}

	</script>

      </td>
    </tr>

    <tr>
      <td></td>
      <td align=center>
	<table width=100% border=0>
	<?php echo $buff; ?>
	</table>
      </td>
    </tr>

    <tr>
      <td colspan=2 height=1 bgcolor=#eedeee >
      </td>
    </tr>

    <tr>
      <td height=34 colspan=2 align=center><input type=submit name=Submit value=提&nbsp;&nbsp;&nbsp;&nbsp;交 />
      &nbsp;&nbsp;&nbsp;&nbsp;
	  <input type=reset name=Submit2 value=取&nbsp;&nbsp;&nbsp;&nbsp;消 /></td>
    </tr>
  </table>
</form>


<script language=javascript>
if(document.myform.allproj_flag.value == 0)
	 document.myform.allproj.checked = false;
else
	 document.myform.allproj.checked = true;

allproj_change(document.myform, document.myform.allproj);


</script>


</body>
</html>
