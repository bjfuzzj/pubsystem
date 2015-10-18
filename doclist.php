<?php

require_once("plib/head.php");


$cgi =  getCGI();

$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

$field = $cgi["field"];
$field_val= $cgi["field_val"];
$sort_field = $cgi["sort_field"];
$sort_desc = $cgi["sort_desc"];
$poly = $cgi["poly"];

if($poly == "") $poly = 1;

$notify_buf = "";
$page_desc = "";
$page = $cgi[page];
$ppage = $cgi[ppage];
$limit_length = $cgi[limit_length];

if($page == "") $page = 0;
if($ppage == "") $ppage = 0;
if($limit_length == "") $limit_length = 20;



if($p_id == "" || $t_id == "") sys_exit("参数错误");
conProjDB($p_id, $t_id);

$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; $t_cname(文档列表)";

$ttype = $temp_data[$t_id][ttype];
if($ttype == 2)
{
	header("Location: fenleilist.php?p_id=$p_id&t_id=$t_id");
	exit;
}

$poly_list = "";
foreach($poly_data as $pmid=>$row)
{
	$poly_list .= sprintf("<option value=\"%d\" %s>%s\n", $pmid, ($poly == $pmid)?"selected":"", $row[pm_name]);
}


$t_name  = $temp_data[$t_id][t_name];
$field_tag = $temp_data[$t_id][field_tag];


$field_tag_name = array();
$field_tag_cname = array();
if(field_tag != "")
{
	$sp = explode(",", $field_tag);
	for($i=0; $i < sizeof($sp); $i++)
	{
		$this_tag = $sp[$i];
		$this_tag = trim($this_tag);
		if( $this_tag == "" ) continue;

		$sqlstr = sprintf("select f_name, cname, type from tempdef where if_into_db='y' and t_id=%s and type <> 'PostInPage' and f_name='%s'", $t_id, $this_tag);
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

		if($row=mysql_fetch_array($res))
		{
			$field_tag_name[] = $row[f_name];
			$field_tag_cname[] = $row[cname];
		}
	}
}



$display_field_list = sprintf("<option value=\"d_id\" %s>文档序号\n", ($field == "d_id")? "selected":"");

foreach ($tempdef_data as $ii=>$row)
{
	if($row[t_id] != $t_id || $row[if_into_db] != 'y' || $row[type] == 'PostInPage') continue;
	$if_select = ($row[f_name] == $field)? "selected" : "";
	$display_field_list .= sprintf("<option value=\"%s\" %s>%s\n", $row[f_name], $if_select, $row[cname]);
}


$field_data = array("d_id"=>"文档序号", "savedatetime"=>"保存日期", "createdatetime"=>"创建日期", "url"=>"文档URL");
foreach($field_data as $kk=>$vv)
{
	if($kk == "d_id") continue;
	$if_select = ($kk == $field)? "selected" : "";
	$display_field_list .= sprintf("<option value=\"%s\" %s>%s\n", $kk, $if_select, $vv);
}



foreach($field_data as $kk=>$vv)
{
	$if_select = ($kk == $sort_field)? "selected" : "";
	$sort_field_list .= sprintf("<option value=\"%s\" %s>%s\n", $kk, $if_select, $vv);
}

foreach ($tempdef_data as $ii=>$row)
{
	if($row[t_id] != $t_id || $row[if_into_db] != 'y' || $row[type] == 'PostInPage') continue;
	$if_select = ($row[f_name] == $sort_field)? "selected" : "";
	
	if(!in_array($row[f_name], $field_tag_name)) continue;
	$sort_field_list .= sprintf("<option value=\"%s\" %s>%s\n", $row[f_name], $if_select, $row[cname]);
}


if($sort_field != "")
{
	$sort_clause = sprintf("order by %s", $sort_field);
        if( $sort_desc )
	{
		$sort_clause .= " desc";
		$desc_str =  "checked";
	}
	else
	{
		$desc_str = "";
        }
}
else
{
	$sort_clause = "order by d_id desc";
	$desc_str = "checked";
}

$clause = "";
if( $field != "" && $field_val != "")
{
	if($field == "d_id")
		$clause .= sprintf(" %s='%s'", $field, $field_val);
	else
		$clause .= sprintf("%s like '%%%s%%'", $field, $field_val);
}
else
{
	$clause = "1=1";
}

if($ci_u_type > 2 && $proj_data[$p_id][proj_docu_flag] )
{
	$clause .= sprintf(" and cu_id=%s", $ck_u_id);
}



$sqlstr = "select count(*) total from $t_name  where $clause";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row = mysql_fetch_array($res);
$total_rec= $row[total];


$page_para = "p_id=$p_id&t_id=$t_id&field=$field&field_val=$field_val&sort_field=$sort_field&sort_desc=$sort_desc&poly=$poly&limit_length=$limit_length";
genPage($total_rec, $page, $ppage,  $_SERVER['PHP_SELF'], $page_para);

$sqlstr = "select *  from $t_name where $clause $sort_clause  limit $limit_begin, $limit_length";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

$doclist = "";
$urlbase = $poly_data[$poly][html_urlbase];

while($row = mysql_fetch_array($res))
{
	$url = $row["url_$poly"];
	$d_id = $row[d_id];

	if(substr($urlbase, -1) == "/")  $urlbase = substr($urlbase, 0, -1);
	if(substr($url, 0, 1) != "/") $urlbase .= "/";	
	if(strpos($url, "http://") === 0 ) $urlbase = "";
	if(urlbase == "") $urlbase = sprintf("/gen/%s/%s", $proj_data["$p_id"]['db_name'], $poly_data[$poly][html_path]);

	$field_tag_data = "";
	foreach($field_tag_name as $ii=>$ff)
	{
		$field_tag_value = $row[$ff];
		$field_tag_value = cn_substr($field_tag_value, 40);
		if($ii == 0 && $url != "")
		{
			$field_tag_data .= "<td align=center><a href=${urlbase}${url} target=_blank>$field_tag_value</a></td>\n";
		}
		else
		{
			$field_tag_data .= "<td align=center>$field_tag_value</td>\n";
		}
	}


 	if($row[published] == 'y')
	{
		$url_str = sprintf("<td align=center><a href=\"%s%s\" target=_blank>%s</a></td>", $urlbase, $url, $url);
	}
	else
	{
		$url_str = "<td align=center>$url</td>";
		$no_published_list .=  sprintf("%s;", $row[d_id]);
	}

	if($field_tag_data == "") $field_tag_data = $url_str;

	//$view_str = sprintf("<a href='doc_view.php?d_id=%s&t_id=%s&p_id=%s' target=_blank>查看</a>", $d_id, $t_id, $p_id);
	$del_str = sprintf("<a href='doc_delete.php?d_id=%s&t_id=%s&p_id=%s' onclick='return del_action();'>删除</a>", $d_id, $t_id, $p_id);
	$update_str = sprintf("<a href='doc_edit.php?d_id=%s&t_id=%s&p_id=%s'>修改</a>", $d_id, $t_id, $p_id);
	$docid_str  = sprintf("<a href='doc_edit.php?d_id=%s&t_id=%s&p_id=%s'><b>%s</b></a>", $d_id, $t_id, $p_id, $d_id);

	$doclist .= "<tr class=tdata>
<td nowrap align=center><input type=checkbox name=d_ids[] value=$d_id><b>$docid_str</b> </td>
$field_tag_data
<td align=center> $row[createdatetime] </td>
<td align=center> $update_str &nbsp;$view_str &nbsp;$del_str </td>
</tr>\n";

}


$field_tag_head = "";
foreach($field_tag_cname as $kk=>$cname)
{
	$field_tag_head .= "<td align=center> $cname </td>";
}

if($field_tag_head == "")
{
	$field_tag_head = "<td align=center>URL</td>";
}


$doc_new_str = sprintf("<a href=\"doc_new_face.cgi?t_id=%s&p_id=%s\">添加文档</a>", $t_id, $p_id);




$menu_flag = $ck_u_type != 3;
if($doclist == "")
{
        $doclist = "<tr height=40><td><b>$p_cname</b>下没有文档, 请先<a href=doc_new.php?p_id=$p_id&t_id=$t_id>创建文档</a> </td></tr>";
        //$menu_flag = false;
}

?>

<html>
<head>
<title>文档列表</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link href="css/main.css" rel="stylesheet" type="text/css" />
<script language=javascript src=js/gong.js></script>

<script LANGUAGE="JavaScript">
function SelectUnSelectAll( flag, my_form, field_name)
{
	len     =       my_form.elements.length;
	
	var     index   =       0;
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == field_name )
		{
			my_form.elements[index].checked=flag;
		}
	}
	return true;
}

function SelectNoPublished(flag,my_form, field_name)
{
	SelectUnSelectAll(false, my_form, field_name);
	
	var no_published_list = ";5;4;";
	
	len     =       my_form.elements.length;
	
	var     index   =       0;
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == field_name && no_published_list.indexOf(";"+my_form.elements[index].value+";")!= -1)
		{
			my_form.elements[index].checked=flag;
		}
	}
	return true;
}

function actionclick(my_form,_action)
{
	len     =       my_form.elements.length;
	var     index   =       0;
	var     ele_checked=false;
	
	my_form.action=_action+'?'+my_form.pt_id.value;
	
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == "d_ids[]" && my_form.elements[index].checked==true)
		{
			ele_checked=true;
			if(_action=="doc_delete.php"){
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
	if(!ele_checked){alert("请先选择一个文档.");return false;}
}

function my_actionclick(my_form, args)
{
	my_form.action='docu.cgi?'+args;
	my_form.submit();
	return false;
}

function del_action()
{
	if(confirm("请确认是否真的删除?"))
	return true;
	else
	return false;
}

</SCRIPT>


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

<?php if($menu_flag)
{
?>
<table width=350 border=0>
<tr>
<td>文档列表</td>
<td> <a href="temp_edit.php?t_id=<?php echo $t_id ?>&p_id=<?php echo $p_id ?>">编辑模板</a> </td>
<td><a href="tempdeflist.php?t_id=<?php echo $t_id ?>&p_id=<?php echo $p_id ?>">编辑模板域</a></td>
</tr>
</table>
<br>

<?php
}
?>


<form name=form1 action="<?php printf("%s?p_id=%s&t_id=%s", $_SERVER['PHP_SELF'], $p_id, $t_id); ?>" method=post>
<table   border=0 style="border:2px solid #ccc; background:#efefef; padding:15px;" width=90%>
<tr><td align=center>
<select name=field>
<?php echo $display_field_list ?>

</select> = <input type=text  name=field_val value="<?php echo $field_val; ?>" size=30>

样式<select name=poly>
<?php echo $poly_list ?>
</select>,

按<select name=sort_field>
<?php echo $sort_field_list ?>
</select>

<input type=checkbox name=sort_desc value="yes" <?php echo $desc_str ?> >倒排,
每页显示<input type=text name=limit_length value="<?php echo $limit_length ?>" size=3>
<input type=submit value="显示">
</td>
</tr>

<tr> <td>
<hr>
</td> </tr>

<tr><td align=center style="font-size:13px;">
<?php echo $page_desc ?>
&nbsp;&nbsp;&nbsp;
<?php echo $notify_buf ?>
</td> </tr>
</table>

</form>

<form  method=post action="" name=myform>
<input type=hidden name=pt_id value="<?php print "t_id=$t_id&p_id=$p_id" ?>">
<table width=100% border=0 NOSHADE>
<tr>
<td valign=top align=center nowrap>
<a href="doc_new.php?<?php echo "t_id=$t_id&p_id=$p_id" ?>">添加文档</a>
<input type=button  value="发  布" onclick="actionclick(self.document.myform,'doc_publish.php');return false;">

<input type=button  value="删  除" onclick="actionclick(self.document.myform,'doc_delete.php');return false;">
<input type=button value="选中未发文档" onClick="SelectNoPublished( true, self.document.myform, 'd_ids[]');">
<input type=button value="全 选" onClick="SelectUnSelectAll( true, self.document.myform, 'd_ids[]');">
<input type=button value="取消选择" onClick="SelectUnSelectAll( false, self.document.myform, 'd_ids[]');">
</td></tr>
</table>

<style type="text/css">
.theader {background:#ccc; font-weight:bold; height:30px;}
.tdata{background:#ccc; background:#eee; height:30px;}
</style>

<table width=100% cellspacing=2 cellpadding=2 style="border:2px solid #ccc; background:#fff;">
<tr class=theader>
<td nowrap align=center>序 号</td>
<?php echo $field_tag_head ?>
<td align=center>创建日期</td>
<td align=center>操 作</td>
</tr>

<?php echo $doclist ?>


</table>
</form>
</body>
</html>
