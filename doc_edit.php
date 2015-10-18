<?php
require_once("plib/head.php");
require_once("plib/publish.php");
require_once("plib/priv.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$d_id = $cgi[d_id];

if($p_id == "" || $t_id == "" || $d_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);


$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$t_name = $temp_data[$t_id][t_name];




$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; <a href=doclist.php?p_id=$p_id&t_id=$t_id >$t_cname</a> &gt; 修改文档($d_id)";

if($cgi[edit] != "" )
{
	//sys_jmp("doclist.php?p_id=$p_id&t_id=$t_id");


        if( check_priv($p_id, $t_id, $d_id) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);

	upload_pic();

        $t_name  = $temp_data[$t_id][t_name];
        $t_cname = $temp_data[$t_id][cname];
        $nav_buf = sprintf("/<a href=\"projlist.php\">网站管理中心</a> &gt; <a href=\"templist.php?p_id=%s\">%s</a> &gt; %s(<a href=\"doclist.php?t_id=%s&p_id=%s\">文档</a>) (<a href=\"temp_edit.php?t_id=%s&p_id=%s\">模板</a>) (<a href=\"tempdeflist.php?t_id=%s&p_id=%s\">模板域</a>) &gt; 更新文档", $p_id, $proj_data[p_cname], $t_cname, $t_id, $p_id, $t_id, $p_id, $t_id, $p_id );

        print_html("更新文档", $nav_buf);

        printf("更新数据库记录....");


	$sqlstr = sprintf("update %s set savedatetime=now(), mu_id=%s,", $t_name,  $ck_u_id);

	foreach($cgi as $this_name=>$this_value)
	{
		if($this_name == "") continue;
		
		
		$pos = strpos($this_name, $pre_field);
		if($pos !== 0) continue;

		$radio_value = $cgi["radio_$this_name"];
		if($radio_value)
		{
			if($radio_value == "old")
			{
				continue;
			}
			else
			{
			/*
				char command[800];
				char urlbase[800];
				char *p;

				strncpy(urlbase, url);
				for(p=urlbase; *p; p++);
				while(p>urlbase && *p != '/') p--;
				*p = '\0';
				
				sprintf("mv /tmp/%s %s/%s", this_value, urlbase, this_value);
				system(command);
			*/
			}
		}

		$ff = substr($this_name, strlen($pre_field));
		$sqlstr .= sprintf(" %s='%s',", $ff, mysql_escape_string($this_value));
	}

	
	foreach($poly_data as $pm_id=>$this_poly)
	{
		$doc_url = $cgi["doc_url_$pm_id"];
		$sqlstr .= sprintf(" url_%d='%s',", $pm_id, $doc_url);
	}
	
	$slen = strlen($sqlstr) - 1;
	$sqlstr{$slen} = ' ';
	$sqlstr .= sprintf(" where d_id=%s", $d_id);

	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);


	doAboutDataPost($p_id, $t_id, $d_id);
	

	printf("更新数据库完成!<br>\n");


	if(publishOneDoc($p_id, $t_id, $d_id) < 0)
	{
		exit($error_message);
	}
	exit;
	
}

$rel_select_flag = 0;
$rich_text_flag = 0;
$date_input_flag = 0;

foreach($tempdef_data as $kk=>$row)
{
	if($row[type] == 'Date') $date_input_flag = 1;
	if($row[type] == 'RichText') $rich_text_flag = 1;
	if($row[type] == 'Rel_Select') $rel_select_flag = 1;
}


//$formlist = `cd ../../interface/; ./doc_edit.cgi "p_id=$p_id&t_id=$t_id&d_id=$d_id"`;
$formlist = get_doc_edit_html($p_id, $t_id, $d_id);

?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" src=js/gong.js></script>




<?php

if($rich_text_flag)
{
	print <<<END_OF_GHC
<link rel="stylesheet" href="css/richtext.css" type="text/css" />
<script language="javascript" src="js/JSFunc.js"></script>
<script language="javascript" src="js/LibFunc.js"></script>
<script language="javascript" src="js/gfunc.js"></script>
<script language="javascript" src="js/LibDoc.js"></script>
<script language="javascript" src="js/typeset.js"></script>
<script language="javascript" src="js/richtext.js"></script>
END_OF_GHC;

}


if($rel_select_flag)
{

	print "<script language=\"JavaScript\" src=js//html_sel.js></script>\n";
}


if($date_input_flag)
{
	print <<<END_OF_GHC
<script language="javascript" src="js/jquery-1.3.2.min.js"></script>
<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" />
<script type="text/javascript" src="js/ui.datepicker.js"></script>
<script type="text/javascript" src="js/ui.datepicker-zh-CN.js"></script>
END_OF_GHC;

}

?>

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

<form method=post action="view_content.php" name=form_view target=_blank onsubmit="alert(this.content.value); return true;">
	<input type=hidden name=p_id value='<?php echo $p_id ?>'> 
	<input type=hidden name=t_id value='<?php echo $t_id ?>'> 
	<input type=hidden name=content value="">
</form>

<style type="text/css">
#doc_table {border:2px solid #ccc; background:#f1f1f1; padding:15px;}
#url_table {border:2px solid #ccc; background:#f1f1f1; padding:15px;}
</style>

<form method=post name=myform encType="multipart/form-data" action="doc_edit.php" onsubmit ="return checkForm();">
<input type=hidden name=p_id value="<?php echo $p_id ?>"> 
<input type=hidden name=t_id value="<?php echo $t_id ?>"> 
<input type=hidden name=d_id value="<?php echo $d_id ?>"> 
<input type=hidden name=edit value=1 > 

<?php echo $formlist ?>

<br>
<INPUT TYPE=reset VALUE="重  写">
&nbsp;&nbsp;&nbsp;
<INPUT TYPE=submit VALUE="提  交">
</form>
<br><br>


</body>
</html>
