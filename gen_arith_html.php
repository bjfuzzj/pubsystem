<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$f_id = $cgi[f_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");
conProjDB($p_id, $t_id);

$t_name = $temp_data[$t_id][t_name];
$row_old = $tempdef_data["$f_id"];

if($cgi[type] == "Where_Clause" ||  $cgi[type] == "Temp_Field" || $cgi[type] == 'Rel_Result' || $cgi[type] == 'DataPost')
{

	$arithmetic = $row_old[arithmetic];
	print <<< END_OF_GHC
算法:<br>
<textarea name=arithmetic style="font-size:14px;width:500px;height:120px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}
else if($cgi[type] == "Select" || $cgi[type] == "Checkbox")
{

	$arithmetic = $row_old[arithmetic];
	if($arithmetic == "")
	{
		$arithmetic = "#值1,选项1\n#值2,选项2\n#值3,选项3";
	}

	print <<< END_OF_GHC
下拉框数据项:<br>
<textarea name=arithmetic cols=90 rows=15>$arithmetic</textarea>
END_OF_GHC;
	exit;
}




else if($cgi[type] == "Rel_Select")
{

	$sel_list = "";
	foreach($temp_data as $kk=>$row)
	{
		if($kk == $t_id) continue;
		$sel_list .= "<option value=$row[t_id]>$row[cname]</option>\n";
	}

	$parent_cname_data = array();
	foreach($tempdef_data as $kk=>$row)
	{
		if($row[t_id] != $t_id) continue;
		if($row[type] != 'Rel_Select') continue;
		$this_arith = $row[arithmetic];
		if($this_arith == "") continue;
		$pos = strpos($this_arith, "\n#parent:");
		if($pos === false ) continue; 
		if($kk == $f_id) continue;

		$this_arith = substr($this_arith,  $pos + strlen("\n#parent:"));
		$pos = strpos($this_arith, "\n");
		if($pos)
		{
			$this_arith = substr($this_arith,  0, $pos);
		}
		$this_arith = trim($this_arith);
		$parent_cname_data[] = $this_arith;
	}

	foreach($tempdef_data as $kk=>$row)
	{
		if($row[t_id] != $t_id) continue;
		if($row[type] != 'Rel_Select') continue;
		if(in_array($row[cname], $parent_cname_data)) continue;
		$parent_sel_list .= "<option value=\"$row[cname]\">$row[cname]</option>\n";
	}


	$arithmetic = $row_old[arithmetic];

	print <<< END_OF_GHC
数据来源:<select name=this_t_id onchange = "gen_field_html(this.form)">
<option value="">----------</option>
$sel_list
</select>

上级下拉框:<select name=parent_cname onchange = "gen_code_html(this.form)">
<option value="">----------</option>
$parent_sel_list
</select>

<br>提取字段<br><div id=field_html> </div><br>

<br>提取条件<br><div id=cond_html> </div><br>

生成算法:<br>
<textarea name=arithmetic cols=90 rows=4 style="font-size:14px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}





else if($cgi[type] == "Sql_Result")
{

	$sel_list = "";

	foreach($temp_data as $kk=>$row)
	{
		//if($kk == $t_id) continue;
		$sel_list .= "<option value=$row[t_id]>$row[cname]</option>\n";
	}

	$arithmetic = htmlspecialchars($row_old[arithmetic]);

	print <<< END_OF_GHC
文章来源:<select name=this_t_id onchange="gen_field_html(this.form)">
<option>----------</option>
$sel_list
</select>
开始位置:<input name=limit_start value="0" size=6 onblur="gen_code_html(this.form);">
提取条数:<input name=limit_length value="6" size=6 onblur="gen_code_html(this.form);" ><br>
提取字段<br><div id=field_html> </div><br>
提取条件:
<br><div id=cond_html></div><br>
生成算法:<br>
<textarea name=arithmetic cols=80 rows=15 style="font-size:14px;">$arithmetic</textarea>
 
END_OF_GHC;
	exit;
}

else if($cgi[type] == "Php_List")
{

	$sel_list = "";
	foreach($temp_data as $kk=>$row)
	{
		if($kk == $t_id) continue;
		$sel_list .= "<option value=$row[t_id]>$row[cname]</option>\n";
	}

	$arithmetic = htmlspecialchars($row_old[arithmetic]);

	print <<< END_OF_GHC
文章来源:<select name=this_t_id onchange="gen_field_html(this.form)">
<option>----------</option>
$sel_list
</select>
提取条数:<input name=limit_length value="20" size=6 onblur="gen_code_html(this.form);" ><br>
提取字段<br><div id=field_html> </div><br>
提取条件:
<br><div id=cond_html></div><br>
生成算法:<br>
<textarea name=arithmetic cols=80 rows=15 style="font-size:14px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}

else if($cgi[type] == "Form_List")
{

	$sel_list = "";

	foreach($temp_data as $kk=>$row)
	{
		if($kk == $t_id) continue;
		$sel_list .= "<option value=\"$row[t_id]\">$row[cname]</option>\n";
	}

	$arithmetic = htmlspecialchars($row_old[arithmetic]);

	print <<< END_OF_GHC
数据来源:<select name=this_t_id onchange="gen_code_html(this.form)">
<option>----------</option>
$sel_list
</select><br>
生成算法:<br>
<textarea name=arithmetic cols=80 rows=15 style="font-size:14px;">$arithmetic</textarea>
 
END_OF_GHC;
	exit;
}

else if($cgi[type] == "Multi_Page")
{

	$arithmetic = $row_old[arithmetic];

	$sel_list = "";
	foreach($tempdef_data as $kk=>$row)
	{
		if($row[t_id]  != $t_id) continue;
		if(!in_array($row[type], array('Text','RichText', 'AutoTypeset_Text')) ) continue;
		$sel_list .= "<option value=$row[cname]>$row[cname]</option>\n";
	}

	print <<< END_OF_GHC
分页字段: <select name=fieldname onchange="gen_code_html(this.form)">
<option>----------</option>
$sel_list
</select>
自动分页每页长度:<input name=plength value="15000" size=6 onblur="gen_code_html(this.form);">
自动分页最大长度:<input name=max_length value="20000" size=6 onblur="gen_code_html(this.form);" ><br>
生成算法:<br>
<textarea name=arithmetic cols=90 rows=15 style="font-size:12px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}

else if($cgi[type] == "PostInPage")
{

	$arithmetic = $row_old[arithmetic];

	$sel_list = "";
	foreach($temp_data as $kk=>$row)
	{
		$sel_list .= "<option value=$row[cname]>$row[cname]</option>\n";
	}

	print <<< END_OF_GHC
触发模板: <select name=tname onchange="gen_code_html(this.form)">
<option>----------</option>
$sel_list
</select>
<br>生成算法:<br>
<textarea name=arithmetic cols=90 rows=15 style="font-size:12px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}

else if($cgi[type] == "File")
{

	$arithmetic = $row_old[arithmetic];

	print <<< END_OF_GHC
文件类型: 
<input type=radio name=itype value="" onclick="gen_code_html(this.form);" checked>普通文件
<input type=radio name=itype value="image" onclick="gen_code_html(this.form);">图片
<input type=radio name=itype value="flash" onclick="gen_code_html(this.form);">FLASH
<br><br><span style="color:#0a0">如果上传的文件是图片或者FLASH，可以在此设置显示参数</span><br>
宽度: <input name=iwidth value="" size=6 onblur="gen_code_html(this.form);">
高度: <input name=iheight value="" size=6 onblur="gen_code_html(this.form);">
边框: <input name=iborder value="" size=6 onblur="gen_code_html(this.form);" ><br>
生成算法:<br>
<textarea name=arithmetic cols=90 rows=15 style="font-size:12px;">$arithmetic</textarea>
END_OF_GHC;
	exit;
}

?>

