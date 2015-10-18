<?php
require_once("plib/head.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$f_id = $cgi[f_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);



$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];

$nav_str .= " &gt; <a href=templist.php?p_id=$p_id>$p_cname</a> &gt; $t_cname(修改模板)";

if($cgi[edit] != "" && $cgi[cname] != "")
{
	sys_jmp("tempdeflist.php?p_id=$p_id");
}

$field_tag  = $temp_data[$t_id][field_tag];

$sqlstr = "select * from tempdef  where t_id=$t_id  order by showorder, f_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

$type_data = array(
"Char"=>"单行输入框",
"Int"=>"数字输入框",
"Text"=>"文本编辑框",
"AutoTypeset_Text"=>"排版编辑框",
"RichText"=>"可视化编辑框",
"File"=>"文件上传框",
"Select"=>"简单下拉框",
"Checkbox"=>"多选框",
"Date"=>"日期输入框",
"Rel_Select"=>"联动下拉框",
"Sql_Result"=>"文章提取",
"Php_List"=>"文章列表",
"Form_List"=>"表单列表",
"Rel_Result"=>"相关结果",
"Multi_Page"=>"分页控制",
"Temp_Field"=>"临时模板域",
"Where_Clause"=>"条件模板域",
"PostInPage"=>"后台触发器",
"DataPost"=>"数据后处理"
);


$this_row = array();
while($row = mysql_fetch_array($res))
{

	$null_buf = $row[if_null] == "y"? "可空":"非空";
	$hide_buf = $row[hide] == "y"? "隐藏": "显示";
	$into_db_buf = $row[if_into_db] == "y"? "是": "否";
	$this_f_id = is_field_tag($field_tag, $row[f_name])? $row[f_id] . "*" : $row[f_id];

	$type_str  = $type_data["$row[type]"];
	$field_name = "$row[cname]<small>($row[f_name])</small>";
	if($cgi[f_id] ==  $row[f_id])
	{
		$field_name = "<b>" . $field_name  . "</b>";
		$this_row = $row;
	}

	$list .= "<tr class=tdata>
                <td><input type=checkbox name=f_id[] value=\"$row[f_id]\" > $this_f_id </td>
                <td>
                <a href=\"tempdeflist.php?f_id=$row[f_id]&t_id=$t_id&p_id=$p_id\">
		$field_name
		</a>
                </td>
                <td> $type_str <small><font color=red> $row[real_type] </font></small></td>
                <td align=center><input type=text name=showorder_$row[f_id]  size=2 value=$row[showorder] ></td>
                <td align=center> $row[showwidth] </td>
                <td align=center> $row[showheight] </td>
                <td align=center> $row[showmargin] </td>
                <td align=center> $null_buf </td>
                <td align=center> $hide_buf </td>
                <td align=center> $into_db_buf </td>
                </tr>";
}


$title_str = $f_id == ""? "添加模板域":"修改模板域";
$action_str = $f_id == ""? "tempdef_new.php":"tempdef_update.php";

//---------------------------------------------------------------------------------------------------------


function is_field_tag($tag, $ff)
{
	$tag = trim($tag);
	if($tag == "") return false;
        $sp = explode(",", $tag);

	foreach($sp as $kk=>$vv)
	{
		$sp["$kk"] = trim($vv);
	}
	return in_array($ff, $sp); 
}


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<title>模板域列表</title>

<link href="css/main.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">

function actionclick(my_form,_action)
{
	len     =       my_form.elements.length;
	var     index   =       0;
	var     ele_checked=false;
	
	
	if( _action == "tempdef_order.php" || _action == "tempdef.php" || _action == "tempdef_tag.php" )
	{
		my_form.action=  _action + '?' + my_form.pt_id.value;
		my_form.submit();
		return true;
	}
	
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == "f_id[]" && my_form.elements[index].checked==true)
		{
			my_form.action= _action + '?f_id='+my_form.elements[index].value+'&'+my_form.pt_id.value;
			ele_checked=true;
			if(_action=="tempdef_delete.php")
			{
				if(confirm("请确认是否真的删除?"))
				{
					my_form.submit(); return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				my_form.submit();return true;
			}
		}
	}
	if(!ele_checked){alert("请先选择一个模板域.");return false;}
	
}


function actionclick1(my_form,_action,_f_name)
{
	my_form._action.value=_action;
	alert(_action+_f_name);return false;
	len     =       my_form.elements.length;
	var     index   =       0;
	var     ele_checked=false;
	for( index=0; index < len; index++ )
	{
		if( my_form.elements[index].name == "_f_name" && my_form.elements[index].value==_f_name)
		{
			my_form.elements[index].checked=true;
			alert(_action+_f_name);return false;
			my_form.submit();return true;
		}
	}
	return true;
}

var submit_flag =0;
function check_form(this_form)
{
	if(submit_flag == 0)
	{
		
		if(this_form.cname.value.length == 0)
		{
			alert("模板域名称不能为空");
			this_form.cname.focus();
			return false;
		}
		
		if(this_form.type.value.length == 0)
		{
			alert("模板域类型必须选择");
			this_form.type.focus();
			return false;
		}
		
		submit_flag = 1;
		return true;
	}
	else
	{
		alert("该页面已经提交，请等待");
		return false;
	}
}



function set_sel_value(this_sel, vv)
{
	for(var i=0; i<this_sel.options.length; i++)
	{
		if(this_sel.options[i].value == vv)
		{
			this_sel.options[i].selected= true;
		}
	}
}


function get_form_check_value(thisform, ff)
{
	for(i=0; i<= thisform.elements.length; i++)
	{
		this_ele = thisform.elements[i];
		if(this_ele.name == ff && this_ele.checked )
		{
			return this_ele.value;
		}
	}
	return "";
}

function gen_arith_html(type_sel)
{
	var type_val = type_sel.value;
	var f_id = type_sel.form.f_id.value;



	var xmlhttp0;
	var flag = 0;
	try{
		xmlhttp0 = new XMLHttpRequest();
		
		}catch(e){
		try{
			xmlhttp0 = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}
	
	xmlhttp0.onreadystatechange = function()
	{
		if(xmlhttp0.readyState==4)
		{
			if(xmlhttp0.status==200)
			{
				var v_html = xmlhttp0.responseText;
				var more_field = document.getElementById('more_field_html');
				more_field.innerHTML = v_html;
			}
		}
	}
	
	var tm = new Date();
	cgi_prog = "gen_more_field_html.php?" + '<?php echo "p_id=$p_id&t_id=$t_id" ?>';
	cgi_prog += "&f_id=" + f_id + "&type=" + type_val + "&tm=" + tm.getTime();
	
	//alert(cgi_prog);
	//xmlhttp0.open("GET", cgi_prog, false);
	xmlhttp0.open("GET", cgi_prog);

	xmlhttp0.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp0.send();












	
	if(     type_val != "Select"
    && type_val != "Checkbox"
	&& type_val != "Rel_Select"
	&& type_val != "Sql_Result"
	&& type_val != "Php_List"
	&& type_val != "PostInPage"
	&& type_val != "Multi_Page"
	&& type_val != "File"
	&& type_val != "Form_List"
	&& type_val != "Rel_Result"
	&& type_val != "PostInPage"
	&& type_val != "Temp_Field"
	&& type_val != "Where_Clause"
	&& type_val != "DataPost"
	)
	{
		var div_arith = document.getElementById('arith_html');
		div_arith.innerHTML = "";
		return;
	}
	
	
	
	var xmlhttp;
	var flag = 0;
	try{
		xmlhttp = new XMLHttpRequest();
		
		}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState==4)
		{
			if(xmlhttp.status==200)
			{
				var v_html = xmlhttp.responseText;
				var div_arith = document.getElementById('arith_html');
				div_arith.innerHTML = v_html;
			}
		}
	}
	
	var tm = new Date();
	cgi_prog = "gen_arith_html.php?" + '<?php echo "p_id=$p_id&t_id=$t_id" ?>';
	cgi_prog += "&f_id=" + f_id + "&type=" + type_val + "&tm=" + tm.getTime();
	
	//alert(cgi_prog);
	//xmlhttp.open("GET", cgi_prog, false);
	xmlhttp.open("GET", cgi_prog);

	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp.send();
	
	/*
	var v_html = xmlhttp.responseText;
	var div_page = document.getElementById('messagelist');
	div_page.innerHTML = '';
	div_page.innerHTML = v_html;
	*/
}




function get_xmlhttp()
{
	var xmlhttp;
	var flag = 0;
	try{
		xmlhttp = new XMLHttpRequest();
		
		}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}

	return xmlhttp;
}


function gen_field_html(myform)
{
	var t_id = myform.this_t_id.value;
	
	var xmlhttp = get_xmlhttp();
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState==4)
		{
			if(xmlhttp.status==200)
			{
				var v_html = xmlhttp.responseText;
				var div_field = document.getElementById('field_html');
				div_field.innerHTML = v_html;
			}
		}
	}
	
	var tm = new Date();
	cgi_prog  =  "gen_field_html.php?t_id=" + t_id +  '&<?php echo "p_id=$p_id" ?>' + '&type=' + myform.type.value;
	cgi_prog += "&tm=" + tm.getTime();

	xmlhttp.open("GET", cgi_prog);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp.send();

	var xmlhttp1 = get_xmlhttp();
	
	xmlhttp1.onreadystatechange = function()
	{
		if(xmlhttp1.readyState==4)
		{
			if(xmlhttp1.status==200)
			{
				var v_html = xmlhttp1.responseText;
				var div_cond = document.getElementById('cond_html');
				div_cond.innerHTML = v_html;
			}
		}
	}
	
	var tm = new Date();
	cgi_prog  =  "gen_condition_html.php?t_id=" + t_id +  '&<?php echo "p_id=$p_id" ?>';
	cgi_prog += "&tm=" + tm.getTime();

	xmlhttp1.open("GET", cgi_prog);
	xmlhttp1.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp1.send();
}



var cond_code_data = "";
function gen_code_html(myform)
{



	var type_val = myform.type.value;
	var fids_str = "";
	var fids_count = 0;

	for(i=0; i<myform.elements.length; i++)
	{
		this_ele = myform.elements[i];
		ele_name = this_ele.name;
		ele_val  = this_ele.value;

		if(ele_name == "f_ids" && this_ele.checked)
		{
			fids_str +=  fids_str == ""? (ele_val):("," + ele_val);
			fids_count++;
		}
	}

	var xmlhttp;
	var flag = 0;
	try{
		xmlhttp = new XMLHttpRequest();
		
		}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState==4)
		{
			if(xmlhttp.status==200)
			{
				var v_html = xmlhttp.responseText;
				myform.arithmetic.value= v_html;
			}
		}
	}
	

	cgi_prog  =  '<?php echo "gen_code_html.php?t_id=$t_id&p_id=$p_id" ?>' +  '&type=' + type_val;



	post_data = '';
	if(type_val == 'Sql_Result')
	{
		var this_t_id = myform.this_t_id.value;
		var start_val = myform.limit_start.value;
		var length_val = myform.limit_length.value;
		cgi_prog += '&f_ids=' + fids_str + '&this_t_id=' + this_t_id;
		cgi_prog += "&start=" + start_val + "&length=" + length_val;
		post_data = "condition=" + cond_code_data;
	}
	if(type_val == 'Php_List')
	{
		var this_t_id = myform.this_t_id.value;
		var length_val = myform.limit_length.value;
		cgi_prog += '&f_ids=' + fids_str + '&this_t_id=' + this_t_id;
		cgi_prog += "&length=" + length_val;
		post_data = "condition=" + cond_code_data;
	}
	if(type_val == 'Form_List')
	{
		var this_t_id = myform.this_t_id.value;
		cgi_prog +=  '&this_t_id=' + this_t_id;
	}
	else if (type_val == 'Rel_Select')
	{
		var this_t_id = myform.this_t_id.value;
		cgi_prog += '&f_ids=' + fids_str + '&this_t_id=' + this_t_id;
		post_data = "parent_cname=" + myform.parent_cname.value
		post_data += "&condition=" + cond_code_data;
		if(fids_count > 2)
		{
			alert('联动下拉框最多只能选两个字段');
			return false;
		}
	}
	else if (type_val == 'Multi_Page')
	{
		post_data = "fieldname=" + myform.fieldname.value;
		post_data += '&plength=' + myform.plength.value;
		post_data += '&max_length=' + myform.max_length.value;
	}
	else if (type_val == 'PostInPage')
	{
		post_data = "tname=" + myform.tname.value;
	}
	else if (type_val == 'File')
	{
		if(myform.cname.value == "") return;
		post_data  = "cname=" + myform.cname.value;
		post_data += "&iwidth=" + myform.iwidth.value;
		post_data += '&iheight=' + myform.iheight.value;
		post_data += '&iborder=' + myform.iborder.value;

		itype_v = get_form_check_value(myform, 'itype');
		post_data += '&itype=' + itype_v;
	}


	var tm = new Date();
	cgi_prog += "&tm=" + tm.getTime();


	//alert(cgi_prog);
	xmlhttp.open("POST", cgi_prog);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp.send(post_data);
}


var cond_data = new Object();
var cond_ii = 1;

/*
cond_data[0] = {ctype:'and', ctext:'标题', cequal:'=', cval:'新闻', status:'0'};
cond_data[1] = {ctype:'and', ctext:'分类', cequal:'=', cval:'保险', status:'0'};
*/

function add_cond_option(thisform)
{
	cond_ii++;
	condtype = thisform.cond_type.value;
	condfield = thisform.cond_field.value;
	condequal = thisform.cond_equal.value;
	condval   = thisform.cond_val.value;
	
	cond_data[cond_ii] = {status:'0'};
	cond_data[cond_ii].ctype =condtype;
	cond_data[cond_ii].ctext =condfield;
	cond_data[cond_ii].cequal = condequal;
	cond_data[cond_ii].cval =  condval;
	
	gen_cond_option_html();
}
function del_cond_option(ii)
{
	cond_data[ii].status = '-1';
	gen_cond_option_html();
}

function gen_cond_option_html()
{
	v_html = "";
	cond_code_data = "";
	i = 0;
	for(p in cond_data)
	{
		this_cond = cond_data[p];
		if(this_cond.status != 0) continue;
		
		this_item = (i==0? '' : ' ' + this_cond.ctype) + ' {' + this_cond.ctext + '} ' + this_cond.cequal + " '" + this_cond.cval + "'";
		cond_code_data += this_item;
		v_html += '<tr bgcolor=#dddddd><td>' + this_item + '</td><td align=right><input type=button value="删除该条件" onclick="del_cond_option('  + p +  ')"></td></tr>\n'
		i++;
	}

	v_html = '<table border=0 width=60%>\n' + v_html + '\n</table>\n';

	div_cond_option = document.getElementById('cond_option_html');
	div_cond_option.innerHTML = v_html;
	gen_code_html(document.myform);
}


function display_more_field()
{
	var more_field = document.getElementById('more_field_html');
	var disp = more_field.style.display;
	if(disp == "none")
	{
		more_field.style.display = "block";
	}
	else
	{
		more_field.style.display = "none";
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
<td><a href="temp_edit.php?t_id=<?php echo $t_id ?>&p_id=<?php echo $p_id ?>">编辑模板</a></td>
<td> 编辑模板域 </td>
</tr>
</table>
<br>
</center>


<style type="text/css">
#tempdef_table {border:2px solid #ccc; background:#f1f1f1; padding:15px; text-align:left;}
#export_table {border:2px solid #ccc; background:#f1f1f1; padding:15px; text-align:left;}
#list_table {border:2px solid #ccc; background:#fff;}
#arith_html,#field_html,#table_field,#more_field_html {font-size: 12px;}
.theader {background:#ccc; font-weight:bold; height:30px; text-align:center}
.tdata{background:#ccc; background:#eee; height:30px;}
</style>

<form action="<?php echo $action_str ?>" method=post name=myform onsubmit="return check_form(this);">
<input type=hidden name=p_id value="<?php echo $p_id ?>">
<input type=hidden name=t_id value="<?php echo $t_id ?>">
<input type=hidden name=f_id value="<?php echo $f_id ?>">
<input type=hidden name=ename value="">

<!--
<input type=hidden name=real_type value="">
<input type=hidden name="if_into_db" value="d">
<input type=hidden name=showorder  value="0">
<input type=hidden name=showwidth  value="0">
<input type=hidden name=showheight  value="0">
<input type=hidden name=hide  value="n">
<input type=hidden name=f_name value="">
<input type=hidden name="ifnull" value="y">
-->

<table id=tempdef_table border=0 width=90% cellspacing=2 cellpadding=3>
<tr><td align=center colspan=2><b><?php echo $title_str ?><b></td></tr>
<tr><td align=right width=150>模板域名称</td><td> <input type=text name=cname value="<?php echo $this_row[cname] ?>" size=40> </td></tr>

<tr><td align=right>模板域类型</td>
<td>
<select name=type onchange="gen_arith_html(this)">
<option value="">--------</option>
<option value="Char">单行输入框</option>
<option value="Text">文本编辑框</option>
<option value="AutoTypeset_Text">排版编辑框</option>
<option value="RichText">可视化编辑框</option>
<option value="File">文件上传框</option>
<option value="Date">日期输入框</option>
<option value="Select">简单下拉框</option>
<option value="Checkbox">多选框</option>
<option value="Rel_Select">联动下拉框</option>
<option value="Sql_Result">文章提取</option>
<option value="Php_List">文章列表</option>
<option value="Form_List">表单列表</option>
<option value="Rel_Result">相关结果</option>
<option value="Multi_Page">分页控制</option>
<option value="Temp_Field">临时模板域</option>
<option value="Where_Clause">条件模板域</option>
<option value="PostInPage">后台触发器</option>
<option value="DataPost">数据后处理</option>
</select>
</td>
</tr>

<tr>
<td> &nbsp; </td>
<td> <div id=arith_html></div> </td>
</tr>

<tr>
<td align=right> &nbsp;  </td>
<td> 
<span onclick="display_more_field()" style="cursor:pointer;color:#00f;"); more">更多属性</span>
<div id=more_field_html style="display:none;border:1px dashed #333; padding:10px;"></div>
</td>
</tr>

<tr> <td></td> <td> <input type=submit value="<?php echo $title_str ?>"> </td></tr>
</table>
</form>

<script type="text/javascript">
set_sel_value(document.myform.type, '<?php echo $this_row[type] ?>')
gen_arith_html(document.myform.type);
</script>

<script type=text/javascript>
function checkForm(this_form)
{

	if(this_form.filename.value == "")
	{
		alert("请选择要上传的模板域文件");
		this_form.filename.focus();
		return false;
	}

	return true;
}
</script>
<form method=post name=exportform encType="multipart/form-data" action="tempdef_import.php?<?php echo "t_id=$t_id&p_id=$p_id" ?>" onsubmit ="return checkForm(this);">

<table id=export_table border=0 width=90% cellspacing=2 cellpadding=3>
<tr><td>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href=tempdef_export.php?<?php echo "t_id=$t_id&p_id=$p_id" ?> >完全导出模板域</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href=tempdef_export.php?simple=1&<?php echo "t_id=$t_id&p_id=$p_id" ?> >简单导出模板域</a>
&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;
<input type=file name=filename> <input type=submit value="倒入模板域">

</td></tr>
</table>

</form>


<form action="" name=fields_list_form method=post>

<input name=pt_id type=hidden value="<?php echo "t_id=$t_id&p_id=$p_id" ?> ">
<table>
<tr>
<td valign=top>

<td>
</tr>
</table>

<table id=list_table width=90% cellspacing=2 cellpadding=3 border=0>
<tr class=tdata style="height:40px;"><td colspan=10 align=center> 
<span style="font-weight:bold; font-size:18px;">模板域列表(模板表名:<?php echo $temp_data[$t_id][t_name] ?>)</span>
</td>

</tr>
<tr class=tdata style="height:40px;"><td colspan=10 align=center> 
<input type=button value="删除模板域" onClick="actionclick(this.form,'tempdef_delete.php');">
&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button value="设置显示顺序"  onClick="actionclick(this.form,'tempdef_order.php');">
&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button value="设置文档标记域"  onClick="actionclick(this.form,'tempdef_tag.php');">
<span style="font-weight:normal;font-size:12px;">(*标记域)</span>
</td>
</tr>


<tr class=theader>
<td>编号</td>
<td>名称</td>
<td>类型</td>
<td>顺序</td>
<td>显示宽度</td>
<td>高度</td>
<td>边距</td>
<td>是否为空</td>
<td>是否隐藏</td>
<td>是否入库</td>
</tr>

<?php echo $list ?>


</table>

</form>


</body>
</html>

