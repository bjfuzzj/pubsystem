<?php
require_once("plib/head.php");

$p_id = $_GET[p_id];
$t_id = $_GET[t_id];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);

$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];

$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; <a href=temp_edit.php?p_id=$p_id&t_id=$t_id >$t_cname</a> &gt; 模板上传";

$mess_str = "";
if(  $_FILES['zfile']['name'] != '' )
{
	$tm = time();
	$filepath = $poly_data[1][file_path];

	$fname = sprintf("ghc%s.zip", $tm);

	$filename = $filepath . "/" . $fname;

	if(move_uploaded_file($_FILES['zfile']['tmp_name'], $filename))
	{
		$cmd = "cd $filepath; unzip -o $fname";
		$ret = `$cmd`;
		$mess_str = "<pre>$cmd\n$ret</pre>\n上传才成功!";
	}
	else
	{
		$mess_str = "$filename 上传失败!<br>\n";
		$mess_str .= print_r($_FILES, true);
	}

}

?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<script type="text/javascript">
function checkForm(thisform)
{
	return true;
}
</script>

<link href="css/main.css" rel="stylesheet" type="text/css" />

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


<form method=post name=myform encType="multipart/form-data" action="">
<input type=hidden name=p_id value="<?php echo $p_id ?>"> 
<input type=hidden name=t_id value="<?php echo $t_id ?>"> 
文件：<input  type=file  name="zfile">

<br><br>
<?php echo $mess_str ?><br>
<INPUT TYPE=submit VALUE="提  交"> &nbsp;&nbsp;&nbsp;<INPUT TYPE=reset VALUE="重  写">
</form>

</body>
</html>
