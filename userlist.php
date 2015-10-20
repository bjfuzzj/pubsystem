<?php
require_once("plib/head.php");
if($ck_u_type !== "0") exit("无权限进行此操作");

$nav_str .= " &gt 用户列表";


$userlist = "";
$sqlstr = "select * from user order by id desc";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$ga = new PHPGangsta_GoogleAuthenticator();
while($row = mysql_fetch_array($res) )
{
	$u_id = $row[id];
	$u_name = $row[name];
    $login = $row[login];
    $secret=$row['secret'];
    $ga->getQRCodeGoogleUrl('www.17co8.com',$secret,$u_id);
    $imgstr='/images/'.$u_id.".png";
	$userlist .= sprintf("<tr height=27><td><input type=radio name=u_id value=\"%s\"></td><td>%s</td><td><a href=\"edituser.php?u_id=%s\">%s</a>(%s)</td><td><img src=\"%s\"></td></tr>\n", $u_id, $u_id, $u_id, $u_name, $login,$imgstr);
}


?>

<html>
<head>
<title>用户列表</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">

<link href="css/main.css" rel="stylesheet" type="text/css" />
<script language=javascript>
function actionclick(my_form,_action)
{
        len     =       my_form.elements.length;
        var     index   =       0;
        var     ele_checked=false;
        for( index=0; index < len; index++ )
        {
                if( my_form.elements[index].name == "u_id" && my_form.elements[index].checked==true)
                {
                        my_form.action=_action+'?u_id='+my_form.elements[index].value;
                        ele_checked=true;
                        if(_action=="deluser.php"){
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
        if(!ele_checked){ alert("请先选择一个用户");return false;}
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

<table width=350 border=0>
<tr>
<td><a href=adduser.php>新建用户</a></td>
<td><a href=# onclick="actionclick(self.document.myform, 'edituser.php'); return false;">编辑用户</a></td>
<td><a href=# onclick="actionclick(self.document.myform, 'deluser.php'); return false;">删除用户</a></td>
</tr>
</table>
<br>

<form name=myform action="" method=post>
<table border=0>
<?php echo $userlist ?>
</table>
</form>

</body>
</html>
