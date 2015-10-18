<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);



$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$old_t_name = $temp_data[$t_id][t_name];

$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; $t_cname(修改模板)";

if($cgi[edit] != "" && $cgi[cname] != "")
{
	if($cgi[grade] == "") $cgi[grade] = "3";
	if($cgi[showorder] == "") $cgi[showorder] = "0";

        $sqlstr = sprintf("update temp set cname='%s', grade=%s, showorder=%s, t_name='%s', ttype='%s'", mysql_escape_string($cgi[cname]), $cgi[grade], $cgi[showorder], $cgi[t_name], $cgi[ttype]);

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
        $sqlstr .= sprintf(", updatedt=now() where t_id=%ld", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	if($old_t_name != $cgi[t_name])
	{
		$sqlstr = "alter table $old_t_name rename as $cgi[t_name]";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	}
	sys_jmp("temp_edit.php?p_id=$p_id&t_id=$t_id");
}


$tempdef_list = "";
foreach($tempdef_data as $kk=>$row)
{
	if($row[t_id] != $t_id || in_array($row[type], array('Multi_Page', 'Temp_Field', 'Where_Clause', 'PostInPage') ) ) continue;
	$tempdef_list .= sprintf("\${%s}\n\n", $row[cname]);
	if($row[type] == 'Php_List')
	{
		$tempdef_list .= sprintf("\${%s.内容}\n\n", $row[cname]);
		$tempdef_list .= sprintf("\${%s.分页提示}\n\n", $row[cname]);
		$tempdef_list .= sprintf("\${%s.分页页码}\n\n", $row[cname]);
	}
}

foreach($global_data as $kk=>$row)
{
	$tempdef_list .= sprintf("\$G{%s}\n\n", $row[name]);
}


$view_url = sprintf("%s/temp_view.php?p_id=$p_id&t_id=$t_id", $poly_data[1][html_urlbase]);

$sqlstr = "select * from temp where t_id=$t_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$row = mysql_fetch_array($res);
if($row == "") sys_exit("模板不存在", $sqlstr);

$iframe_src = "temp_html.php?p_id=$p_id&t_id=$t_id";

?>

<html>
<head>
<title><?php echo $p_cname ?> -- 修改模板</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<meta http-equiv="Pragma" content="no-cache">

<link href="css/main.css" rel="stylesheet" type="text/css" />
<script language=javascript src=js/gong.js> </script>

<link rel="stylesheet" href="css/richtext.css" type="text/css" />
<script language="javascript" src="js/richtext_temp.js"></script>



<script language=javascript>
var previewed = false;
var WBTB_bTextMode = true;
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

	if(WBTB_bTextMode) return true;


	v_html = "";

	try{
	v_html = getIFrameHTML("WBTB_Composition");
	v_html = v_html.replaceAll("<ghc_write_mark>[\\s\\S]*?</ghc_write_mark>", "");
	}catch(e) { alert(e); return false;}


	
	v_html = html_code_body1 +  v_html + html_code_body2;
	my_form.html_1.value = v_html;

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


function set_sel(sel, vv)
{
	for(i=0; i<sel.options.length; i++)
	{
		if(sel.options[i].value == vv)
		{
			sel.options[i].selected = true;
			break;
		}
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

<table width=350 border=0>
<tr>
<td><a href="doclist.php?t_id=<?php echo $t_id ?>&p_id=<?php echo $p_id ?>">文档列表</a></td>
<td> 编辑模板 </td>
<td><a href="tempdeflist.php?t_id=<?php echo $t_id ?>&p_id=<?php echo $p_id ?>">编辑模板域</a></td>
</tr>
</table>
<br>

<form name=myform method=post action=temp_edit.php onsubmit="return checkForm(this);">
<input type=hidden name=p_id value="<?php echo $p_id ?>" >
<input type=hidden name=t_id value="<?php echo $t_id ?>" >
<input type=hidden name=edit value=1>

<table width=100% border=0 style="border:1px solid #333; background:#eee; padding:15px;">

<tr height=10><td colspan=2 align=right> <a href="temp_upload.php?<?php  print "p_id=$p_id&t_id=$t_id" ?>"> 上传模板</a></td></tr>
<tr>
<td align=center><b>模板名称</b></td>
<td width=90%><input type=text size=40 name=cname value="<?php echo htmlspecialchars($row[cname]); ?>"> <?php echo $notify_notnull ?></td>
</tr>

<tr>
<td align=center><b>显示顺序</b></td>
<td width=90%><input type=text size=3 name=showorder value="<?php echo $row[showorder] ?>"></td>
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
<script language=javascript> set_sel(document.myform.grade, "<?php echo $row[grade] ?>"); </script>

</td>
</tr>

<tr>
<td align=center><b>模板类型</b></td>
<td width=90%>

<select name=ttype>
<option value=0>普通模板
<option value=2>分类数据
</select>
<script language=javascript> set_sel(document.myform.ttype, "<?php echo $row[ttype] ?>"); </script>
</td>
</tr>

<tr>
<td align=center><b>数据表名</b></td>
<td width=90%><input type=input  name=t_name value="<?php echo $row[t_name] ?>"></td>
</tr>

<tr><td colspan=2> &nbsp; </td></tr>


<td align=center><b>默认URL</b> </td>
<td> <input type=text size=60 name=defaulturl_1 value="<?php echo htmlspecialchars($row[defaulturl_1]); ?>"> </td>
</tr>

<tr>
<td align=center><b>HTML代码</b></td>
<td></td>
</tr>

<tr valign=top>

<td align=center>
<span style="font-size:12px; font-weight:bold; color:green;">模板域列表</span><br>
<textarea cols=16 rows=46 readonly style="font-size:12px; width:120px; height:670px;">
${createdatetime}
${createdate}
${projid}
${tempid}
${docid}

<?php echo $tempdef_list ?>
</textarea>
</td>
<td>


<script LANGUAGE="JavaScript">

var p_id = <?php echo $cgi[p_id] ?>;
var t_id = <?php echo $cgi[t_id] ?>;
</script>

<input type="hidden" name="Body" id="Body" value="">



<table ID='WBTB_Container' class="WBTB_Body" style="display:none" width=100% height=700 cellpadding=3 cellspacing=0>

<tr id="WBTB_Toolbars">
<td>
<table cellpadding=0 cellspacing=0 border=0>
<tr id="yToolbar" class="yToolbar">
<td>
<select ID="WBTB_formatSelect" class="WBTB_TBGen" onchange="WBTB_doSelectClick('FormatBlock',this,WBTB_bTextMode,'WBTB_Composition')">
	<option class="heading" selected>段落格式</option>
	<option VALUE="&lt;P&gt;">Normal
	<option VALUE="&lt;H1&gt;">Heading 1
	<option VALUE="&lt;H2&gt;">Heading 2
	<option VALUE="&lt;H3&gt;">Heading 3
	<option VALUE="&lt;H4&gt;">Heading 4
	<option VALUE="&lt;H5&gt;">Heading 5
	<option VALUE="&lt;H6&gt;">Heading 6
	<option VALUE="&lt;H7&gt;">Heading 7
	<option VALUE="&lt;PRE&gt;">Formatted
	<option VALUE="&lt;ADDRESS&gt;">Address

</select>
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="字体颜色" LANGUAGE="javascript" onclick="WBTB_foreColor(WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" src="richtext/images/fgcolor.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn">
<img class="WBTB_Ico" title="字体背景颜色" LANGUAGE="javascript" onclick="WBTB_backColor(WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" src="richtext/images/fbcolor.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td>
<select language="javascript" class="WBTB_TBGen" id="FontName" onfocus="this.selectedIndex=0;" onchange="WBTB_format('fontname',WBTB_bTextMode,'WBTB_Composition',this[this.selectedIndex].value);"> 
<option class="heading" selected>字体
<option value="宋体">宋体
<option value="黑体">黑体
<option value="楷体_GB2312">楷体
<option value="仿宋_GB2312">仿宋
<option value="隶书">隶书
<option value="幼圆">幼圆

<option value="新宋体">新宋体
<option value="细明体">细明体
<option value="Arial">Arial
<option value="Arial Black">Arial Black
<option value="Arial Narrow">Arial Narrow
<option value="Bradley Hand ITC">Bradley Hand ITC
<option value="Brush Script	MT">Brush Script MT
<option value="Century Gothic">Century Gothic
<option value="Comic Sans MS">Comic Sans MS
<option value="Courier">Courier
<option value="Courier New">Courier New
<option value="MS Sans Serif">MS Sans Serif
<option value="Script">Script
<option value="System">System
<option value="Times New Roman">Times New Roman
<option value="Viner Hand ITC">Viner Hand ITC
<option value="Verdana">Verdana

<option value="Wide Latin">Wide Latin
<option value="Wingdings">Wingdings</option>
</select>
<select language="javascript" class="WBTB_TBGen" id="FontSize" onfocus="this.selectedIndex=0;" onchange="WBTB_FontSize(WBTB_bTextMode,'WBTB_Composition',this[this.selectedIndex].value);">
<option class="heading" selected>字号
<option value="1">11px</option>
<option value="2">12px</option>
<option value="3">14px</option>
<option value="4">16px</option>
<option value="5">18px</option>
<option value="6">24px</option>

<option value="7">36px</option>
</select>
<select class="WBTB_TBGen" onchange="WBTB_InsertSymbol('WBTB_Composition',this[this.selectedIndex].innerText);this.selectedIndex=0;">
<option class="heading" selected>符号</option>
<option value="&amp;#162;">&#162;</option>
<option value="&amp;#163;">&#163;</option>
<option value="&amp;#165;">&#165;</option>
<option value="&amp;#166;">&#166;</option>
<option value="&amp;#169;">&#169;</option>
<option value="&amp;#174;">&#174;</option>
<option value="&amp;#176;">&#176;</option>
<option value="&amp;#177;">&#177;</option>
<option value="&amp;#183;">&#183;</option>
<option value="&amp;#171;">&#171;</option>
<option value="&amp;#187;">&#187;</option>

<option value="&amp;#188;">&#188;</option>
<option value="&amp;#189;">&#189;</option>
<option value="&amp;#190;">&#190;</option>
<option value="&amp;#247;">&#247;</option>
<option value="&amp;#8224;">&#8224;</option>
<option value="&amp;#8225;">&#8225;</option>
<option value="&amp;#8364;">&#8364;</option>
<option value="&amp;#8482;">&#8482;</option>
</select>
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" TITLE="alt" LANGUAGE="javascript" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" onclick="WBTB_replace('WBTB_Composition')" src="richtext/images/replace.gif" WIDTH="16" HEIGHT="16"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="上传文件" LANGUAGE="javascript" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" onclick="OpenUpload('file_upload.php',document.myform.p_id.value,'setFrm');" src="richtext/images/upload.gif" WIDTH="16" HEIGHT="16"></td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >

<!-- img class="WBTB_Ico" title="清理代码" LANGUAGE="javascript" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" onclick="WBTB_CleanCode('WBTB_Composition')" src="richtext/images/cleancode.gif" WIDTH="16" HEIGHT="16" --></td>
<td class="WBTB_Btn">
<!-- img class="WBTB_Ico"  title="帮助" LANGUAGE="javascript" onmouseover="this.className= 'WBTB_Ico_Hover'; " onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';" onclick="WBTB_help();" src="richtext/images/help.gif" WIDTH="16" HEIGHT="16" unselectable="on" -->
</td>
</tr>
</table>


<table cellpadding=0 cellspacing=0 >
<tr id="yToolbar" class="yToolbar">
<td class="WBTB_Btn">
<img  title="全选" LANGUAGE="javascript" onclick="WBTB_format1('selectAll','WBTB_Composition');" class="WBTB_Ico" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/selectAll.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img title="剪切" LANGUAGE="javascript" onclick="WBTB_format1('cut','WBTB_Composition');" class="WBTB_Ico" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/cut.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >

<img title="复制" LANGUAGE="javascript" onclick="WBTB_format1('copy','WBTB_Composition');" class="WBTB_Ico" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/copy.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn">
<img  title="粘贴" LANGUAGE="javascript"  onclick="WBTB_format1('paste','WBTB_Composition');" class="WBTB_Ico" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/paste.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn"  >
<img title="撤消" LANGUAGE="javascript" onclick="WBTB_format1('undo','WBTB_Composition');" class="WBTB_Ico" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/undo.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico"  title="恢复" LANGUAGE="javascript" onclick="WBTB_format1('redo','WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/redo.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入表格" LANGUAGE="javascript" onclick="WBTB_fortable(WBTB_bTextMode,'WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/table.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico"  title="插入行" LANGUAGE="javascript" onclick="WBTB_InsertRow('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/insertrow.gif" WIDTH="16" HEIGHT="16">

</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="删除行" LANGUAGE="javascript" onclick="WBTB_DeleteRow('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/deleterow.gif" WIDTH="16" HEIGHT="16">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入列" LANGUAGE="javascript"  onclick="WBTB_InsertColumn('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/insertcolumn.gif" WIDTH="16" HEIGHT="16">
</td>
<td class="WBTB_Btn">
<img class="WBTB_Ico"  title="删除列" LANGUAGE="javascript" onclick="WBTB_DeleteColumn('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/deletecolumn.gif" WIDTH="16" HEIGHT="16">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入超级链接" LANGUAGE="javascript"  onclick="WBTB_forhl('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/wlink.gif" WIDTH="18" HEIGHT="18" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="去掉超级链接" LANGUAGE="javascript" onclick="WBTB_format1('Unlink','WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/unlink.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>


<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入图片" LANGUAGE="javascript"  onclick="WBTB_UserDialog('InsertImage',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/img.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入水平线" LANGUAGE="javascript" onclick="WBTB_format('InsertHorizontalRule',WBTB_bTextMode,'WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/hr.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn">
<img class="WBTB_Ico"  title="插入Flash" LANGUAGE="javascript"  onclick="WBTB_forswf('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/swf.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入Windows Media" LANGUAGE="javascript" onclick="WBTB_forwmv('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/wmv.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入Real Media" LANGUAGE="javascript"  onclick="WBTB_forrm('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/rm.gif" WIDTH="16" HEIGHT="16" unselectable="on">

</td>
</tr>
</table>
<table cellpadding=0 cellspacing=0>
<tr id="yToolbar" class="yToolbar">
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="加粗" LANGUAGE="javascript"  onclick="WBTB_format('bold',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/bold.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="斜体" LANGUAGE="javascript" onclick="WBTB_format('italic',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/italic.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="下划线" LANGUAGE="javascript" onclick="WBTB_format('underline',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/underline.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="上标" LANGUAGE="javascript"  onclick="WBTB_format('superscript',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/superscript.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>

<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="下标" LANGUAGE="javascript"  onclick="WBTB_format('subscript',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/subscript.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="删除线" LANGUAGE="javascript" onclick="WBTB_format('strikethrough',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/strikethrough.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn">
<img class="WBTB_Ico"  title="取消格式" LANGUAGE="javascript" onclick="WBTB_format1('RemoveFormat','WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/removeformat.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="左对齐" NAME="Justify" LANGUAGE="javascript"  onclick="WBTB_format('justifyleft',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/aleft.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="居中" NAME="Justify" LANGUAGE="javascript"  onclick="WBTB_format('justifycenter',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/center.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn"  >

<img class="WBTB_Ico" title="右对齐" NAME="Justify" LANGUAGE="javascript" onclick="WBTB_format('justifyright',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/aright.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico"  title="编号" LANGUAGE="javascript" onclick="WBTB_format('insertorderedlist',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/numlist.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="项目符号" LANGUAGE="javascript" onclick="WBTB_format('insertunorderedlist',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/bullist.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" LANGUAGE="javascript" >
<img class="WBTB_Ico" title="减少缩进量"  onclick="WBTB_format('outdent',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/outdent.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="增加缩进量" LANGUAGE="javascript" onclick="WBTB_format('indent',WBTB_bTextMode,'WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/indent.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
<td><img src="richtext/images/separator.gif"></td>
<td class="WBTB_Btn" >

<img class="WBTB_Ico" title="插入引用" LANGUAGE="javascript" onclick="WBTB_specialtype('WBTB_Composition','<div class=quote>','</div>')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/quote.gif" WIDTH="16" HEIGHT="16">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入代码" LANGUAGE="javascript" onclick="WBTB_code('WBTB_Composition')" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/code.gif" WIDTH="16" HEIGHT="16">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入视频播放代码" LANGUAGE="javascript" onclick="WBTB_InsertVideo('WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/video.gif" WIDTH="21" HEIGHT="12" unselectable="on">
</td>
<td class="WBTB_Btn" >
<img class="WBTB_Ico" title="插入分页" LANGUAGE="javascript" onclick="WBTB_InsertPage('WBTB_Composition');" onmouseover="this.className= 'WBTB_Ico_Hover'; " class="WBTB_Ico"  onmouseout="this.className='WBTB_Ico';" onmousedown="this.className='WBTB_Ico_Down';" onmouseup="this.className='WBTB_Ico_Hover';"  src="richtext/images/insertpage.gif" WIDTH="16" HEIGHT="16" unselectable="on">
</td>
</tr>

<tr><td colspan=20>&nbsp;<a href="http://www.mozillaonline.com/" style="font-size:12px;" target=_blank>使用该编辑器，做好使用firefox浏览器</a></td></tr>
</table>


</td></tr>

<tr><td height="100%">
<input type="hidden" id="richtext" name="richtext">
<input type=button name="setFrm" value='' onclick="WBTB_InsertImageTag('WBTB_Composition',this);" style="display:none;">

<iframe class="WBTB_Composition" ID="WBTB_Composition" src="<?php echo $iframe_src ?>" MARGINHEIGHT="5" MARGINWIDTH="5" width="100%" height="100%"></iframe>

<input type=button name="setFrm" value='' onclick="WBTB_InsertImageTag('WBTB_Composition',this);" style="display:none;">
</td></tr>

<tr><td></td></tr>
</table>

<script language="javascript">WBTB_InitDocument('Body','GB2312',WBTB_bTextMode,'WBTB_Composition');</script>


<script language="javascript">				

if (isIE) 
{
	document.getElementById("WBTB_Composition").attachEvent("onblur",function(){WBTB_CopyData('WBTB_Composition', document.myform.elements['_FORM_PF']);});
} 
else 
{
	document.getElementById("WBTB_Composition").contentDocument.addEventListener("blur", function(){WBTB_CopyData('WBTB_Composition', document.myform.elements['_FORM_PF']);}, true);
}

function ClientValidate(source, arguments)
{
	if (arguments.Value.length>250)
		arguments.IsValid=false;
	else
		arguments.IsValid=true;
}
			
</script>




<table width=100% height=100% cellpadding=3 cellspacing=0 border=0><tr><td>

<TEXTAREA NAME="html_1" COLS="12" ROWS="40" id="_FORM_PF" style="width:950px; height:650px;">
<?php echo htmlspecialchars($row[html_1]); ?>
</TEXTAREA>

<INPUT TYPE="HIDDEN" NAME="_FORM_VF__NOTNULL" VALUE="FALSE">
<INPUT TYPE="HIDDEN" NAME="_FORM_VF__MIN_LENGTH" VALUE="0">

<INPUT TYPE="HIDDEN" NAME="_FORM_VF__MAX_LENGTH" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="_FORM_VF__FTR" VALUE="Text">
<INPUT TYPE="HIDDEN" NAME="_FORM_FCR" VALUE="正文">
<INPUT TYPE="HIDDEN" NAME="_FORM_AP" VALUE="Article.Content">


</td></tr>
</table>


<table cellpadding=0 cellspacing=0 border=0 width='350'>
<tr>
	<td width='10'>&nbsp;</td>
	<td class="WBTB_TabOn" id='WBTB_TabHtml' onclick="if (!WBTB_bTextMode) {WBTB_bTextMode=true; WBTB_setMode(myform['_FORM_PF'],'WBTB_Container','WBTB_TabHtml','WBTB_TabDesign',WBTB_bTextMode,'WBTB_Composition');}" unselectable="on" width='60'>
		<img unselectable="on" SRC="richtext/images/mode.html.gif" ALIGN="absmiddle" width=21 height=20> 源码
	</td>

	<td style="width:10px"></td>
	<td class="WBTB_TabOff" id='WBTB_TabDesign' onclick="if (WBTB_bTextMode) {WBTB_bTextMode=false; WBTB_setMode(myform['_FORM_PF'],'WBTB_Container','WBTB_TabHtml','WBTB_TabDesign',WBTB_bTextMode,'WBTB_Composition');}" unselectable="on" width='60'>
		<img unselectable="on" SRC="richtext/images/mode.design.gif" ALIGN="absmiddle" width=21 height=20> 设计
	</td>
	<td style="width:10px"></td>
	<td class="WBTB_TabOff" id="WBTB_TabView" onclick="WBTB_View('<?php echo $view_url ?>', 'WBTB_Composition', myform['_FORM_PF']);" unselectable="on" width='60'>
		<img unselectable="on" SRC="richtext/images/mode.view.gif" ALIGN="absmiddle" width=21 height=20> 查看
	</td>
	<td align='right'  width='70'>

		<a href="javascript:WBTB_Size(-300,'WBTB_Container',WBTB_bTextMode)" title="减小编辑区域"><img src="richtext/images/minus.gif" unselectable="on" border='0'></a> <a href="javascript:WBTB_Size(300,'WBTB_Container',WBTB_bTextMode)" title="增大编辑区域"><img src="richtext/images/plus.gif" unselectable="on" border='0'></a>
	</td>
</tr>
</table>

<script type="text/javascript">
if (WBTB_bTextMode)
{
//	WBTB_bTextMode=!WBTB_bTextMode;
//	WBTB_setMode(myform['_FORM_PF'],'WBTB_Container','WBTB_TabHtml','WBTB_TabDesign',WBTB_bTextMode,'WBTB_Composition');
}
</script>







</td>
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
