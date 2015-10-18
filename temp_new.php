<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
if($p_id == "") sys_exit("参数错误");

conProjDB($p_id);


$p_cname = $proj_data[p_cname];
$nav_str .= " &gt; <a href=templist.php?p_id=$p_id >$p_cname</a> &gt; 新建模板";

if($cgi[edit] != "" && $cgi[cname] != "")
{
	if($cgi[grade] == "") $cgi[grade] = "3";
	if($cgi[showorder] == "") $cgi[showorder] = "0";

        $sqlstr = sprintf("insert into temp set cname='%s',  grade=%s, showorder=%s, ttype='%s', createdt=now()", mysql_escape_string($cgi[cname]), $cgi[grade], $cgi[showorder], $cgi[ttype]);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

        $t_id=mysql_insert_id($proj_mysql);

	if($cgi[t_name] == "")
	{
		$table_name = sprintf("sp_t%d", $t_id);
	}
	else
	{
		$table_name = $cgi[t_name];
	}

        $sqlstr = sprintf("update temp set t_name='%s'", $table_name);

	foreach($poly_data as $pm_id=>$row)
        {

		$ff= "html_" . $pm_id;
		$html = $cgi[$ff];

		$ff= "defaulturl_" . $pm_id;
		$url = $cgi[$ff];

                $filename = sprintf("%s/tmpl_%d_%d.htm", $row[file_path], $t_id, $pm_id);
		writeFile($filename, $html);

                $sqlstr .= sprintf(", html_%d='%s', defaulturl_%d='%s'",
                $pm_id, mysql_escape_string($html), $pm_id, $url);
        }
        $sqlstr .= sprintf(" where t_id=%ld", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());


        $sqlstr = sprintf("create table %s (
        d_id int(12) unsigned auto_increment primary key,
        cu_id int(10),
        creator Varchar(40),
        mu_id int(10),
        mender varchar(40),
        createdatetime datetime not null,
        savedatetime datetime,
        published char(1) default 'n',
        INDEX(createdatetime),
        INDEX(cu_id)
        ) DEFAULT CHARSET=%s", $table_name, DB_CHARSET);

	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	if(sizeof($poly_data) > 0)
	{
                $sqlstr = sprintf("alter table %s ", $table_name);
		foreach($poly_data as $pm_id=>$row)
		{
                	$sqlstr .= sprintf("add url_%d varchar(255),", $pm_id);
		}
		$sqlstr = substr($sqlstr, 0, strlen($sqlstr) - 1);
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	}

	sys_jmp("templist.php?p_id=$p_id");
}


$tempdef_list = "";
foreach($global_data as $kk=>$row)
{
	$tempdef_list .= sprintf("\$G{%s}\n\n", $row[name]);
}


?>

<html>
<head>
<title><?php echo $p_cname ?> -- 新建模板</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<meta http-equiv="Pragma" content="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />


<script language=javascript src=/js/gong.js> </script>

<script language=javascript>
var previewed = false;
function actionclick(my_form,_action)
{
	my_form._action.value=_action;
	if(_action=="deletefield")
	{
		if(prompt("Are you sure to delete this field?(yes/no)","no")=="yes")
		{
			my_form.submit();
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		my_form.submit();
		return true;
	}
}

function checkForm(my_form)
{

	if(my_form.cname.value == "")
	{
		alert("模板名称不能为空");
		my_form.cname.focus();
		return false;
	}


	return true;
}

function copyfieldnameclick()
{
	id = document.myform.fieldlist.selectedIndex;
	if(id>=0)
	{
		document.myform.copyfieldname.value = document.myform.fieldlist.options[id].text;
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


<form name=myform method=post action=temp_new.php onsubmit="return checkForm(this);">
<input type=hidden name=p_id value="<?php echo $p_id ?>" >
<input type=hidden name=edit value=1>

<table width=95% border=0 style="border:1px solid #333; background:#eee; padding:15px;">
<tr height="10"><td align="right" colspan="2"> </td></tr>

<tr>
<td align=center><b>模板名称</b></td>
<td width=90%><input type=text size=40 name=cname value=""> <?php echo $notify_notnull ?></td>
</tr>

<tr>
<td align=center><b>显示顺序</b></td>
<td width=90%><input type=text size=3 name=showorder value="0"></td>
</tr>

<tr>
<td align=center><b>模板级别</b></td>
<td width=90%>

<select name=grade>
	<option value=3>编辑人员
	<option value=2>二次开发人员
	<option value=1>普通管理员
	<option value=0>超级管理员
	</select>
<script language=javascript>
grade_v = "";
grade = document.myform.grade.value;
sel_grade = document.myform.grade;
for(i=0; i<sel_grade.options.length; i++)
{
	if(sel_grade.options[i].value == grade_v)
	{
		sel_grade.options[i].selected = true;
		break;
	}
	
}
</script>

</td>
</tr>

<tr>
<td align=center><b>模板类型</b></td>
<td width=90%>

<select name=ttype>
<option value=0>普通模板
<option value=2>分类数据
</select>

</td>
</tr>

<tr>
<td align=center><b>数据表名</b></td>
<td width=90%><input type=input  name=t_name value=""></td>
</tr>


<tr>
<td colspan=2>&nbsp;</td>
</tr>


<tr>
<td align=center><b>默认URL</b> </td>
<td> <input type=text size=60 name=defaulturl_1 value=""> </td>
</tr>

<tr>
<td align=center><b>HTML代码</b></td>
<td></td>
</tr>

<tr valign=top>
<td align=center>
<span style="font-size: 12px; font-weight: bold; color: green;">模板域列表</span>
<textarea style="font-size: 12px; width: 120px; height: 620px;" readonly="" rows="46" cols="16">
${createdatetime}

${createdate}

${projid}

${tempid}

${docid}

<?php echo $tempdef_list ?>
</textarea>

</td>
<td> <textarea wrap=soft name=html_1 style="width: 950px; height: 650px;"></textarea> </td>
</tr>

<tr>
<td align=center colspan=2>
<input type=button VALUE="返 回" onclick="history.go(-1);"> &nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit VALUE="提 交">
</td>
</tr>

</table>


</form>
</body>
</html>
