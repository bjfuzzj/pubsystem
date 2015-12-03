<?php
require_once("db.php");
require_once("pub_eval_func.php");

function trim_last_splash($str)
{
	$pos = strrpos($str, "/");
	$len = strlen($str);
	if($pos == $len - 1) return substr($str, 0, $pos);
	return $str;
}

function trim_big_bracer($table)
{
	$len = strlen($table);
	$ii = $len -1;
	if($ii < 0) $ii == 0;

	if($table{0}=='{' && $table{$ii}=='}' )
	{
		return substr($table, 1, $len-2);
	}
	return $table;
}


function readFieldData($fdata, $fname)
{
        global $error_message;

        if(strpos($fdata, $fname) === 0)
                $mark =  "$fname:";
        else
                $mark = "\n$fname:";

        $pos = strpos($fdata, $mark);
        if($pos === false)
        {
                $error_message = "Not found [$mark]";
		/*
		print "\n================================\n";
		print "$error_message\n";
		print "$fdata\n";
		print "-------------$mark----------------\n";
		*/
                return "";
        }

        $pos += strlen($mark);
        $buff = substr($fdata, $pos);
        $pos = strpos($buff, "\n");
        if($pos)
        {
                $buff = substr($buff, 0, $pos);
        }
        else
        {
                $buff = substr($buff, 0);
        }
        $buff = trim($buff);
        return $buff;
}

function html_length($data)
{
	$str = preg_replace(array('/<.*?>/sm'), array(''), $data);
	$len = strlen($str);
	return $len;
}

function getTablelist($str)
{
        if( preg_match('/from\s+(.*?)\s+/i',  $str . " ", $matches)) 
        {
		return  $matches[1];
        }
	return "";
}

function getSqlstr($str)
{
	global $proj_mysql;

	if($str == "") return "";
	
	$buff =  $str;
	$rep1 = array();
	$rep2 = array();

	$table =  getTablelist($str);
	$table = trim_big_bracer($table);

	
	$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $table, $table, $table);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	$row = mysql_fetch_array($res);
	$t_id=$row[t_id];
	$t_name=$row[t_name];
	
	$rep1[] = '/\{' . $table . '\}/';
	$rep2[] = $t_name;

	
	$sqlstr = sprintf("select f_name, cname, ename from tempdef where t_id=%s", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	while($row = mysql_fetch_array($res, MYSQL_ASSOC))
	{
		$f_name= $row[f_name];
		$cname= $row[cname];
		$ename= $row[ename];
		
		if($cname != "" && strpos($buff, $cname))
		{
			$rep1[] = '/\{' . $cname . '\}/';
			$rep2[] = $f_name;
		}
		
		if($ename != "" && strpos($buff, $ename))
		{
			$rep1[] = '/\{' . $ename . '\}/';
			$rep2[] = $f_name;
		}
		
	}

	$buff = preg_replace ($rep1, $rep2, $buff);
	return $buff;
}

function get_select_field($sqlstr)
{
        if( preg_match('/select\s+(.*)\s+from/i',  $sqlstr, $matches)) 
        {
		$buff = $matches[1];
        }
	$sp = explode(",", $buff);
	for($i=0; $i<count($sp); $i++)
	{
		$sp[$i] = str_replace("distinct ", "", $sp[$i]);
		$sp[$i] = trim($sp[$i]);
	}

	for($i=0; $i<count($sp); $i++)
	{
		$sp[$i] = trim_big_bracer($sp[$i]);
	}
	return $sp;
}

function getFieldlist($sqlstr)
{
        if( preg_match('/select\s+(.*)\s+from/i',  $sqlstr, $matches)) 
        {
		$buff = $matches[1];
        }

	for($i = 0, $flag =0; $i< strlen($buff);  $i++)
	{
		$ch = $buff{$i};
		if($ch == '(') $flag = 1;
		if($flag && ($ch == ',' || $ch ==' ' || $ch == '.') ) $buff{$i} = chr(ord($ch) + 127);
		if($ch == ')' && $flag) $flag =0;
	}

	$sp = explode(",", $buff);
	foreach($sp as $kk=>$vv)
	{
		$vv = trim($vv);
		$pos = strpos($vv, ' ');
		if(!$pos) $pos = strpos($vv, ".");
		$pos = $pos ? $pos + 1 : 0;
		$ff = substr($vv, $pos);

		$ff = trim($ff);
		for($i =0; $i < strlen($ff); $i++)
		{
			$ch = $ff{$i};
			$code = ord($ch);
			if($code > 127 )
			{
				$ch = chr($code - 127);
				if($ch == ',' || $ch == ' ' || $ch == '.') $ff{$i} = $ch;
			}
		}
		$sp[$kk] = $ff;
	}

	return $sp;
}

function get_rel_select_child($cname)
{
	global $tempdef_data;

        $mark = "\n#parent:$cname";
	foreach($tempdef_data as $row)
        {
                if($row[type] !="Rel_Select") continue;
                if(strpos($row[arithmetic], $mark) !== false)
                {
                        return $row;
                }
        }
        return "";
}


function get_rel_select_field_name($arithmetic)
{
	global $proj_mysql;
	global $error_message;

	$query = readFieldData($arithmetic, "#query");
	if($query == "")
	{
		$error_message = "query not found in $arithmetic";
		return "";
	}



	$table =  getTablelist($query);
	$table = trim_big_bracer($table);
	
	$sqlstr = "select t_id  from temp where t_name='$table' or cname='$table' or ename='$table'";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	$row  = mysql_fetch_array($res);
	if($row == "")
	{
		$error_message = "temp not found.\n$sqlstr\n";
		return "";
	}
	$t_id = $row[t_id];

	$sp = get_select_field($query);

	if($sp == "")
	{
		$error_message =  "Not found field:\n  $query";
		return "";
	}
	
	
	$sqlstr =  "select f_name from tempdef where t_id=$t_id and cname='$sp[0]'";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	$row = mysql_fetch_array($res, MYSQL_ASSOC);
	if($row == "")
	{
		$error_message = "Not found: \n$sqlstr\n";
		return "";
	}
	return $row[f_name];
}


function getRel_Result($arithmetic, $f_name, $p_id, $t_id, $cname)
{
	global $g_mysql;
	
	$field_name = sprintf("%s%s", $pre_field, $f_name);
	
	$buff = "";
	
	$arithmetic = trim($arithmetic);
	$arithmetic = substr($arithmetic, 1);
	$sp=explode("\n#", $arithmetic);
	for($i=0; $i<count($sp); $i++)
	{
		$sp1=explode(":", $sp[$i],2);
		if($sp1[0] == "Agent") $agent=$sp1[1];
		if($sp1[0] == "CGI")  $cgi=$sp1[1];
		if($sp1[0] == "Param") $param=$sp1[1];
		if($sp1[0] == "Command") $command=$sp1[1];
	}
	
	
	$agent = trim($agent); $cgi = trim($cgi); $param = trim($param); $command = trim($command);

	
	$sp=explode("&", $param);

	for($i=0; $i<count($sp); $i++)
	{
		$sp1=explode("=", $sp[$i]);
		$value=$sp1[1];
		$name=$sp1[0];

		$pos = strpos($name, "{");
		$name = substr($name, 0, $pos);
		$pos++;
		
		$str = substr($sp1[0], $pos);
		$str = substr($str, 0, strlen($str) -1);

		$sp2=explode(";", $str);
		
		for($j=0; $j < count($sp2); $j++)
		{
			$sp3=split("->", $sp2[$j]);
			if($sp3[0] == "label") $label=$sp3[1];
			if($sp3[0] == "type")  $type=$sp3[1];
			if($sp3[0] == "value") $options=$sp3[1];
		}
		
		
		
		if($type == "select")
		{
			$buff .= sprintf("%s:<select name=_rel_result_%s_mode size=1>\n", $label, $field_name);
			$options = substr($options, 1, strlen($options) -2);
			$sp4=explode(",", $options);
			
			for($j=0; $j<count($sp4); $j++)
			{
				$opt=$sp4[$j];
				if($opt{0}=="'")
				{
					$opt = substr($opt, 1, strlen($opt) - 2);
				}
				
				if($sp4[$j] == $value)
					$buff .= sprintf("<option value=%s selected>%s</option>\n", $opt, $opt);
				else
					$buff .= sprintf("<option value=%s selected>%s</option>\n", $opt, $opt);
			}
			$buff .= "</select><br>\n";
		}
		
		
		if($type == "text")
		{
			if(strpos($value, "?"))
			$buff .= sprintf("%s:<input size=10 type=text name=_rel_result_%s_key><br>\n", $label, $field_name);
			else
			$buff .= sprintf("%s:<input size=10 type=text name=_rel_result_%s_key value=%s><br>\n", $label, $field_name, $value);
		}
		
	}

	
	
	$buff .= sprintf("<input type=button name=btnCommand value=\"%s\" onClick=\"do_Rel_Result('%s','%s',self.document.myform,'%s','%s');\"><br>\n", $command, $agent, $cgi, $field_name, $cname);
	
	$buff .= sprintf("
	<input type=hidden name=_rel_result_%s__p_id value=%d><br>
	<input type=hidden name=_rel_result_%s__t_id value=%d><br>
	<input type=hidden name=_rel_result_%s_language value=cn>
	", $field_name, $p_id, $field_name, $t_id, $field_name);
	
	return $buff;
}

$Js_Fname = array();
function getJsName($validate)
{
	global $Js_Fname;
        if( preg_match('/function\s+(.*?)\(\)/',  $validate, $matches)) 
        {
		$Js_Fname[] = $matches[1];
		return 1; 
        }
	//---HUNXIAO_CODE<111>---
	return 0;
}
function genJsCode()
{
	global $Js_Fname;
	$buff = "";
	foreach($Js_Fname as $fname)
	{
		$buff .= sprintf("if (ret) { ret = %s(); } else { return ret; }\n",  $fname);
	}
	//---HUNXIAO_CODE<109>---
	return $buff;
}

function get_doc_new_html($p_id, $t_id)
{
	global $proj_mysql;
	global $poly_data;
	global $error_message;

	$v_html = "";
	$poly_html = "";
	$form_html = "";

	foreach($poly_data as $row)
	{

		$url_html .= <<<GHC_OF_END
	<table id=url_table width=100%>
	<tr> <TD ALIGN=left BGCOLOR=#bfbfbf><small>$row[pm_name] url:</td></tr>
<tr>
<TD ALIGN=left BGCOLOR=#dddddd>
<input type=radio name=urlradio_$row[pm_id] value="default" checked><input type="text" name=default_url_$row[pm_id] size=60 value="$row[defaulturl]">
</td>
</tr>
<tr><TD ALIGN=left BGCOLOR=#dddddd>
<input type=radio name=urlradio_$row[pm_id] value="new_outer"><input type="text" name=outer_url_$row[pm_id] size=60 value="直接引用外部网站文章">
</td></tr>
</small>
</table>
GHC_OF_END;

	}
	
	$sqlstr = "select f_name, cname, type, arithmetic, showwidth, showheight, showmargin, defaultvalue, validate from tempdef where t_id=$t_id and hide='n' and type!='Sql_Result' and type!='PostInPage' and type != 'Php_List' and type != 'DataPost' order by showorder, f_id";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	while($row = mysql_fetch_array($res, MYSQL_ASSOC))
	{
		//print "<b>$row[cname]</b> .....";
		$form=fieldIntoForm($row, "", "", "",  $p_id, $t_id);
		if($form == "")
		{
			print "[$row[cname]]模板域呈现错误: $error_message";

			//print "$form_html";
			return;
		}

		//print "OK<br>\n";

		$form_html .= $form;
	}

	$js_code = genJsCode();
	$v_html =<<<GHC_OF_END
<table id=doc_table border=0 width=100% cellspacing=2 cellpadding=3>
$form_html
</table>
<br>
$url_html
<script language=javascript>
 function checkForm()
 {
        ret = true;
	$js_code;
        return ret;
 }
</script>
GHC_OF_END;

	return $v_html;
}


function get_doc_edit_html($p_id, $t_id, $d_id)
{
	global $proj_mysql;
	global $poly_data;
	global $temp_data;
	global $error_message;

	$v_html = "";
	$poly_html = "";
	$form_html = "";


        $sqlstr = sprintf("select * from %s where d_id=%s", $temp_data[$t_id][t_name], $d_id);
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
        $pdoc = mysql_fetch_array($res, MYSQL_ASSOC);


	foreach($poly_data as $pm_id=>$row)
	{
                $html_urlbase = $row[html_urlbase];
                $url_name = sprintf("url_%d", $pm_id);
		$url = $pdoc[$url_name];

		
                $doc_url = $url;
                $doc_url1 = $url;
		$hii = strlen($html_urlbase) - 1;

                if($html_urlbase{$hii} !='/' && $doc_url{0} !='/' && $doc_url != "") $doc_url = "/$doc_url";

                if($html_urlbase{$hii} =='/' && $doc_url{0}=='/' && $doc_url != "") $doc_url = substr($doc_url, 1);
		

                if($url != "")
                {
			$pos = strrpos($url, "/");
			if($pos !== false) $url = substr($url, 0, $pos);
                }

                $html_urlbase1=$html_urlbase;
		if(strpos($doc_url, "http://") === 0)  $html_urlbase1 = "";


                $url_html .= sprintf(
                "<table id=url_table width=100%%>
                <tr>
                <TD ALIGN=left BGCOLOR=#bfbfbf nowap><a href=\"%s%s\" target=_blank>%s URL:</a></td>
                <TD ALIGN=left BGCOLOR=#dddddd>%s </td>
                <TD ALIGN=left BGCOLOR=#dddddd><input type=text name=doc_url_%d size=40 value=%s></td>
                </tr>
                </table>", $html_urlbase1, $doc_url, $row[pm_name], $html_urlbase, $pm_id, $doc_url1);


	}
	
	
	$sqlstr = "select f_name, cname, type, arithmetic, showwidth, showheight, showmargin, defaultvalue, validate from tempdef where t_id=$t_id and hide='n' and type!='Sql_Result' and type!='PostInPage' and type != 'Php_List' and type != 'DataPost' order by showorder, f_id";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	while($row = mysql_fetch_array($res, MYSQL_ASSOC))
	{
		$form=fieldIntoForm($row, $pdoc, $html_urlbase1, $url, $p_id, $t_id);
		if($form == "")
		{
			print "[$row[cname]]模板域呈现错误: $error_message";
			return;
		}
		$form_html .= $form;
	}

	$js_code = genJsCode();
	$v_html =<<<GHC_OF_END
<table id=doc_table border=0 width=100% cellspacing=2 cellpadding=3>
$form_html
</table>
<br>
$url_html
<script language=javascript>
 function checkForm()
 {
        ret = true;
	$js_code;
        return ret;
 }
</script>
GHC_OF_END;

	return $v_html;
}


function fieldIntoForm($this_row, $pdoc, $html_urlbase, $url, $p_id, $t_id)
{
	global $error_message;

	global $proj_mysql;
	global $pre_field;

	$f_name=$this_row[f_name];
	$cname=$this_row[cname];
	$type=$this_row[type];
	$arithmetic=$this_row[arithmetic];
	$showwidth = $this_row[showwidth];
	$showheight = $this_row[showheight];
	$showmargin = $this_row[showmargin];
	$defaultvalue = $this_row[defaultvalue];
	$validate = $this_row[validate];

	$value = $defaultvalue;
	if($pdoc != "") $value = $pdoc[$f_name];
	$field_name =  "$pre_field$f_name";
	
	if($type =="Select")
	{
		$buff = "<tr><td width=20%>
		$cname
		</td><td>
		<select name=$field_name>
		<option value=\"\">---</option>\n";
		
		$str = substr($arithmetic, 1);
		$sp = explode("#", $str);
		foreach($sp as $item)
		{
			$item = trim($item);
			if($item == "") continue;

			$sp1=explode(",", $item);
			if(count($sp1)<2)
			{
				$kk = $item;
				$vv = $item;
			}
			else
			{
				$kk = $sp1[0];
				$vv = $sp1[1];
			}
			
			if($value != $kk)
				$buff .= "<option value=\"$kk\">$vv\n";
			else
				$buff .= "<option value=\"$kk\" selected>$vv\n";
		}
		
		$buff .= "</select>\n</td></tr>\n";
		
	}
    elseif($type =="Checkbox")
    {
        $value=explode(",",$value);
        $buff = "<tr><td width=20%>$cname</td><td>";
        $str = substr($arithmetic, 1);
        $sp = explode("#", $str);
        foreach($sp as $item)
        {
            $item = trim($item);
            if($item == "") continue;

            $sp1=explode(",", $item);
            if(count($sp1)<2)
            {
                $kk = $item;
                $vv = $item;
            }
            else
            {
                $kk = $sp1[0];
                $vv = $sp1[1];
            }
            if(in_array($kk,$value))
                $buff.="<input type=\"checkbox\" name=\"{$field_name}[]\" checked=\"true\" value=\"$kk\">$vv\n";
            else
                $buff.="<input type=\"checkbox\" name=\"{$field_name}[]\"  value=\"$kk\">$vv\n";
        }
        $buff .= "\n</td></tr>\n";

    }





	else if($type == "Rel_Select")
	{

		$query = readFieldData($arithmetic, "#query");
		if($query == "")
		{
			$error_message = "query not found in $arithmetic !";
			return "";
		}


		$childTF = get_rel_select_child($cname);
		if( $childTF != "")
		{
			$child_query = readFieldData($childTF['arithmetic'], "#query");
			if($child_query == "")
			{
				$error_message = "child query not found in $childTF[arithmetic]!";
				return "";
			}
			$child_field_name = "$pre_field$childTF[f_name]";
		}

		$parent_value =  "'---'";
		$parent_f_name = "";

		$parent = readFieldData($arithmetic, "#parent");

		if($parent != "")
		{
			global $tempdef_data;
			$parentTF = getTF($parent, "cname");
			if($parentTF == "")
			{
				$error_message =  "parent:$parent not found in TF!";
				return "";
			}


			$parent_f_name = get_rel_select_field_name($parentTF['arithmetic']);
			if($parent_f_name == "")
			{
				if($error_message == "") $error_message = "parent_f_name not found.";
				return "";
			}

		


			$parent_field_name = sprintf("%s%s", $pre_field, $parentTF[f_name]);
			$parent_value = sprintf("document.myform.%s.value", $parent_field_name);
		}

		if($child_query != "");
		{
			$this_f_name = get_rel_select_field_name($arithmetic);
			$sqlstr =  getSqlstr($child_query);
			$child_html = sprintf(" onchange=\"gen_sel_options(%d, document.myform.%s, '%s', '',  this.value, '%s');\"", 
					$p_id, $child_field_name, urlencode($sqlstr), $this_f_name);
		}

		$sqlstr = getSqlstr($query);


		$buff = sprintf("<tr>
		<td width=20%%>
		%s
		</td>
		<td>
		<select name=%s %s>
		<option value=\"\">--
		</select>
		<script type=text/javascript>gen_sel_options(%d, document.myform.%s, '%s', '%s', %s, '%s');</script>
		</td>\n</tr>
		", $cname, $field_name, $child_html, $p_id, $field_name, urlencode($sqlstr), $value, $parent_value, $parent_f_name);
		
	}
	else if($type == "Text")
	{
		if($showwidth == "" || $showwidth == "0" ) $showwidth="65";
		if($showheight == "" || $showheight == "0") $showheight="15";
		
		$buff = sprintf("
		<tr>
		<td width=20%%>
		%s
		</td>
		<td>
		<textarea name=%s cols=%s rows=%s wrap=physical>%s </textarea>
		</td>
		</tr>
		", $cname, $field_name, $showwidth, $showheight, htmlspecialchars($value, ENT_QUOTES));
		
	}
	
	else if($type == "AutoTypeset_Text")
	{
		if($showwidth == "" || $showwidth == "0" ) $showwidth="65";
		if($showheight == "" || $showheight == "0") $showheight="15";
		
		$buff = sprintf("
		<tr>
		<td width=20%%>
		%s<br>
		<input type=button name=format_btn value=\"自动排版\"
		onClick=\"document.myform.format_status.value = '正在排版...';
		document.myform.%s.value=formattext(document.myform.%s.value,1);
		document.myform.format_status.value ='排版结束！'; \"><br>
		<input type=text name=format_status value=\"进程显示...\" size=11><br>
		<input type=button name=format_preview value=\"预览\" onClick=\"text_preview(document.myform.%s.value); \">
		</td>
		<td>
		<textarea name=%s cols=%s rows=%s wrap=physical>
		%s
		</textarea>
		</td>
		</tr>
		", $cname, $field_name, $field_name, $field_name, $field_name,  $showwidth, $showheight, htmlspecialchars($value, ENT_QUOTES));
		
	}
	else if($type == "RichText")
	{
		$file_data = file_get_contents("tmpl/richtext.htm");
		if($file_data != "")
		{
			$file_data = str_replace("GHC_FIELD_NAME", $field_name, $file_data);
			$file_data = str_replace("GHC_FIELD_CNAME", $cname, $file_data);
			$file_data = str_replace("GHC_FIELD_VALUE", htmlspecialchars($value, ENT_QUOTES), $file_data);
			$file_data = str_replace("GHC_P_ID", $p_id, $file_data);
			$file_data = str_replace("GHC_T_ID", $t_id, $file_data);
			$buff =  $file_data;
		}
		else
		{
			$buff = "Error get tmpl/richtext.htm";
		}
	}
	else if($type == "Rel_Result")
	{
		$buff = sprintf("
		<tr>
		<td width=20%%>
		%s<br>\n", $cname);
		
		$buff .= sprintf("%s", getRel_Result($arithmetic, $f_name, $p_id, $t_id, $cname));
		
		$buff .= sprintf("
		</td>
		<td>
		<textarea name=\"%s\" cols=65 rows=15 wrap=physical></textarea>
		</td>
		</tr>\n", $field_name);
		
	}
	else if($type == "File")
	{

		if($showwidth == "" || $showwidth == "0" ) $showwidth="40";
		if($value == "")
		{
			$buff = sprintf("
			<tr>
			<td width=20%%>%s</td>
			<td> <input type=file name=%s value=\"%s\" size=%s></td>
			</tr>
			", $cname,   $field_name,  $value,  $showwidth);
		}
		
		else
		{
			$escape = "/";
			if($url{0}=="/") $escape ="";

			if(strpos($value, "http://") === 0)
				$img_url = $value;
			else
			{
				//$img_url = sprintf("%s%s%s/%s", $html_urlbase, $escape, $url, urlencode($value));
				$img_url = sprintf("%s%s", $html_urlbase, substr($value, 1));
			}

			$pos = strrpos($value, '.');
			if($pos)
			{
				$postfix = substr($value, $pos);
				$postfix = strtolower($postfix);

				if(  $postfix ==  ".jpg" || $postfix ==  ".gif" || $postfix == ".png" || $postfix == ".bmp")
				{
					$img_str = sprintf("<br><a href=\"%s\" target=_blank><img src=\"%s\" width=250 border=0></a>", $img_url, $img_url);
				}
			}

			
			
			$buff = sprintf("
			<tr>
			<td valign=top nowrap>%s</td>
			
			<td>
			<input type=radio name=\"radio_%s\" value=new>
			<input type=file name=\"%s\" size=40 value=\"*.*\"><br>
			
			<input type=radio name=\"radio_%s\" value=old checked>
			<a href=\"%s\" target=\"_blank\">%s</a>\n%s
			</td></tr>
			", $cname, $field_name, $field_name, $field_name, $img_url, $value, $img_str);
			
		}
		
	}
	else if($type =="Char")
	{

		if($showwidth == "" || $showwidth == "0" ) $showwidth="65";

		$buff = sprintf("
		<tr>
		<td width=20%%>%s</td>
		<td> <input type=text name=%s value=\"%s\" size=%s></td>
		</tr>
		", $cname,   $field_name,  htmlspecialchars($value),  $showwidth);
		
	}
	else if($type =="Int")
	{
		if($showwidth == "" || $showwidth == "0" ) $showwidth="65";

		$buff = sprintf("
		<tr>
		<td width=20%%>%s</td>
		<td> <input type=text name=%s value=\"%s\" size=%s></td>
		</tr>
		", $cname,   $field_name,  $value,  $showwidth);
		
	}
	else if($type == "Date")
	{
		if($showwidth == "" || $showwidth == "0" ) $showwidth="40";
		$buff = sprintf("
		<tr>
		<td width=20%%>%s</td>
		<td> <input type=text name=%s id=%s value=\"%s\" size=%s>
		<script type=\"text/javascript\">\n$(document).ready(function() { $(\"#%s\").datepicker(); });\n</script>
		</td>
		</tr>
		",$cname,  $field_name, $field_name, $value, $showwidth, $field_name);
		
	}
	else if($type == "Multi_Page")
	{
		if($showwidth == "" || $showwidth == "0" ) $showwidth="65";

		$buff = sprintf("
		<tr>
		<td width=20%%>%s</td>
		<td> <select name=%s><option value=0 %s>不分页<option value=1 %s>手工分页<option value=2 %s>自动分页</select></td>
		</tr>
		",$cname,  $field_name, $value{0} == '0'?"selected":"", $value{0} == '1'?"selected":"", $value{0} == '2'?"selected":"");
	}
	else if($type == "Where_Clause")
	{
		return "\n";
	}
	else if($type == "Temp_Field")
	{
		return "\n";
	}
	else
	{
		$buff = sprintf("
		<tr>
		<td width=20%%>%s</td>
		<td> %s-------------%s </td>
		</tr>
		",$cname, $field_name, $type);
	}


	if($showmargin > 0)
	{
		$buff .= "
		<tr style=\"height:40px;\">
		<td colspan=2> <hr width=100%> </td>
		</tr>";
	}

	//---HUNXIAO_CODE<108>---

	if($validate != "")
	{
		if( getJsName($validate) > 0 )
		{
			$rep1 = sprintf('${%s}', $cname);
			$rep2 = sprintf("document.myform.%s", $field_name);
			$validate = str_replace($rep1, $rep2, $validate);
			$buff .= sprintf("\n<script language=javascript>\n%s\n</script>\n", $validate);
		}
	}
	
	return $buff;
}



function print_html($title, $nav_buf)
{
	global $today, $ck_u_name;

	$html_charset = HTML_CHARSET;
	
	$ret = sprintf("<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$html_charset\">
	<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
	<link href=css/main.css rel=stylesheet type=\"text/css\" />
	<title>%s</title></head>
	<body>

	<table width=100%% border=0>
	<tr valign=bottom>
	<td>%s</td>
	<td align=right valign=bottom>你好, %s! 欢迎使用发布系统(%s)</td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor=#808080></td></tr>
	</table><br>\n", $title, $nav_buf, $ck_u_name, $today);

	print $ret;
}







function publishOneDoc($p_id, $t_id, $d_id)
{
	global  $root_path, $file_base;
	global $proj_data, $poly_data, $temp_data, $tempdef_data, $global_data;
	global $pub_mysql, $proj_mysql;
	global $this_doc;

	$this_doc = "";
	$eval_Table = "";
	$async_buf = "";


	$outer_flag=0;
	
	printf("\n<br>=======================================================================================<br>\n开始发布文档[<a href=%s?p_id=%d&t_id=%d&d_id=%d>%d</a>]...<br>\n", "doc_edit.php", $p_id, $t_id, $d_id, $d_id);


	print("开始获取文档数据<br>\n");
	
	$sqlstr = sprintf("select *, date_format(createdatetime, '%%Y-%%m-%%d') createdate, date_format(createdatetime, '%%H:%%i:%%s') createtime, date_format(savedatetime, '%%Y-%%m-%%d') savedate, date_format(savedatetime, '%%H:%%i:%%s') savetime from %s where d_id=$d_id", $temp_data[$t_id][t_name]);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr . "\n" );
	$this_doc = mysql_fetch_array($res, MYSQL_ASSOC);
	if($this_doc == "") exit("Doc not exist: $sqlstr\n");
	printf("获取文档数据结束!<br><br>\n");



	printf("开始发布模板域<br>\n");
	$ret = publishDocField($p_id, $t_id, $d_id);
	if($ret < 0 )
	{
		printf("%s\n", $error_message);
		return -1;
	}
	printf("发布模板域结束!<br><br>\n");



	$mp = getMultiPage($t_id);
	$spos = doAboutMultiPage($mp);


	
	foreach($poly_data as $pm_id => $this_poly)
	{
		printf("开始发布二次开发模板域<br>\n");
		$ret = publishDocField_poly($p_id, $t_id, $d_id, $pm_id);
		if($ret < 0 )
		{
			printf("%s\n", $error_message);
			return -1;
		}
		printf("发布二次开发模板域结束!<br><br>\n");
		
		$ret = doAboutURL($p_id, $t_id, $d_id, $pm_id);
		if($ret < 0 )
		{
			printf("处理文档URL出错: %s", $error_message);
			continue;
		}
		$doc_url = $poly_data[$pm_id][doc_url];
		if($doc_url == "")
		{
			printf("本文档[<a href=\"doc_edit.php?d_id=%d&t_id=%d&p_id=%d\">%d</a>]URL为空...不做发布!<br>\n", $d_id, $t_id, $p_id, $d_id);
			continue;
		}
		$pos =strpos($doc_url, "http://");
		if($pos === 0)
		{
			printf("<a href=\"%s\" target=_blank>%s</a><br>\n", $doc_url, $doc_url);
			continue;
		}
		
		//---HUNXIAO_CODE<101>---
		
		$doc_filename = sprintf("%s/%s", $this_poly[file_path], $doc_url);
		$doc_filename = str_replace("//", "/", $doc_filename);

		$pos = strpos($doc_filename, "?");
		if($pos)
		{
			$doc_filename = substr($doc_filename, 0, $pos);
		}

		$doc_filepath  =  dirname($doc_filename);
		
		pub_mkdir($doc_filepath);


		/*
		$img_filename = sprintf("%s/upload_pub/%s%s", $this_poly[file_path], $t_id, $doc_url);
		$img_filename = str_replace("//", "/", $img_filename);
		$img_filepath = dirname($img_filename);

		pub_mkdir($img_filepath);
		
		foreach($tempdef_data as $kk=>$this_tempdef)
		{
			if($this_tempdef[type] !=  "File") continue;
			
			$f_value = $this_tempdef[f_value];
			if($f_value != "")
			{
				$filename = sprintf("%s/%s", TMP_PATH, $f_value);
				if(file_exists($filename))
				{
					copy(TMP_PATH . "/" . $f_value, $img_filepath . "/" . $f_value);
					$tempdef_data[$kk][f_value] =  dirname("/upload_pub/$t_id$doc_url") ."/" . $f_value;
					$tempdef_data[$kk][f_output] = $tempdef_data[$kk][f_value];
				}
			}
		}
		*/


		$html=$this_poly[html];

		foreach($tempdef_data as $this_tempdef)
		{
			if($this_tempdef[type] == "PostInPage" ) continue;
			if($mp != "" && $mp[f_name] ==  $this_tempdef[f_name]) continue;

			$rep1 = sprintf('${%s}', $this_tempdef[cname]);
			$html = str_replace($rep1, $this_tempdef[f_output], $html);


			if($this_tempdef['type'] == "Php_List" )
			{
				$rep1 = sprintf('${%s.内容}', $this_tempdef[cname]);
				$rep2 = sprintf( '<?php $php_list_ret=$php_ret_%s; if(is_array($php_list_ret)) echo $php_list_ret[php_list]; else echo $php_list_ret;  ?>', $this_tempdef[f_id]);
				$html = str_replace($rep1, $rep2, $html);

				$rep1 = sprintf('${%s.分页页码}', $this_tempdef[cname]);
				$rep2 = sprintf( '<?php echo $php_ret_%s[page_list] ?>', $this_tempdef[f_id]);
				$html = str_replace($rep1, $rep2, $html);

				$rep1 = sprintf('${%s.分页提示}', $this_tempdef[cname]);
				$rep2 = sprintf( '<?php echo $php_ret_%s[page_notify] ?>', $this_tempdef[f_id]);
				$html = str_replace($rep1, $rep2, $html);
			}
			
		}


		$html = str_replace('${docid}', $d_id, $html);
		$html = str_replace('${tempid}', $t_id, $html);
		$html = str_replace('${projid}', $p_id, $html);
	
		//---HUNXIAO_CODE<102>---

		$html = str_replace('${createdatetime}', $this_doc['createdatetime'], $html); 
		$html = str_replace('${createdate}', $this_doc['createdate'], $html); 
	

		foreach($global_data as $row_global)
		{
			$rep1 = sprintf('$G{%s}', $row_global[name]);
			if($row_global[type] == "js")
			{
				$rep2 = sprintf("<script type=\"text/javascript\">pub_global_vars('%s');</script>\n", $row_global[name]);
				$html = str_replace($rep1, $rep2, $html);
			}
			else
			{
				$html = str_replace($rep1, $row_global['content'], $html);
			}
		}



	
		if($mp)
		{
			genMuitiPageFile($mp, $spos, $html, $doc_filename, $doc_url);
		}
		else
		{
			writeFile($doc_filename, $html, 0);
		}

		$html_urlbase = $poly_data[$pm_id][html_urlbase];
		$html_urlbase = trim_last_splash($html_urlbase);
		
		printf("<a href=\"%s%s%s\" target=_blank>%s</a><br>\n", $html_urlbase, $doc_url{0}=='/'?"":"/", $doc_url, $doc_url);
	}
	
	
	
	$sqlstr = sprintf("update %s set ", $temp_data[$t_id][t_name]);

	foreach($tempdef_data as $this_tempdef)
	{
		if( ($this_tempdef[type] != "Sql_Result" && $this_tempdef[type] != "File") || $this_tempdef[if_into_db] != 'y' ) continue;
		$sqlstr .= sprintf("%s = '%s', ", $this_tempdef[f_name], mysql_escape_string($this_tempdef[f_value]));
	}
	$sqlstr .= sprintf("published='y' where d_id=%d", $d_id);


	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr . "\n");
	printf("发布完成<br>\n");
	if($outer_flag) return 0;

	
	printf("开始相关发布<br>\n");
	
	foreach($tempdef_data as $ii=>$pTF)
	{
		if($pTF[type] != "PostInPage") continue;
		if( doAboutPostInPage($p_id, $t_id, $d_id, $ii) < 0)
		{
			printf("发布错误: %s<br>\n", $error_message);
			return -1;
		}
		$i++;
	}
	
	printf("同步开始<br>\n");
	asyncDoc($p_id, $t_id, $d_id);
	printf("\n<br>同步完成");
	printf("\n<br>开始处理后数据");
	//doAboutDataPost($p_id, $t_id, $d_id);
	printf("\n<br>处理后数据结束");

}

function publishDocField($p_id, $t_id, $d_id)
{
	
	global  $root_path, $file_base;
	global $proj_data, $poly_data, $temp_data, $tempdef_data, $global_data;
	global $pub_mysql, $proj_mysql;
	global $this_doc;
	global $async_buf;
	global $eval_Table;
	

	$eval_Table = array();
	
	foreach($tempdef_data as $ii=>$this_tempdef)
	{
		
		printf(".");
		
		if($this_tempdef[type] == "Sql_Result")
		{
			continue;
		}
		else if($this_tempdef[type] == "Sql_Result_Ex")
		{
			continue;
		}
		else if($this_tempdef[type] == "Temp_Field")
		{
			if( $this_tempdef[arithmetic] != "")
			{
				$tempdef_data[$ii][f_value] =  $this_tempdef[arithmetic];
			}
			else if($this_tempdef[if_into_db]=="y")
			{
				$f_name = $this_tempdef[f_name];
				$tempdef_data[$ii][f_value] =$this_doc[$f_name];
			}
			else
			{
				$tempdef_data[$ii][f_value] = "";
			}
			
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
			
		}
		else if($this_tempdef[type] == "Where_Clause")
		{
			$t_value = 0;
			$tempdef_data[$ii][f_value] = doAboutWhere($this_tempdef[arithmetic], $p_id, $t_id, $t_value);
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
		}
		else if($this_tempdef[type] == "PostInPage")
		{
			continue;
		}
		else
		{
			
			if($this_tempdef[if_into_db]=="y")
			{
				$f_name = $this_tempdef[f_name];
				$tempdef_data[$ii][f_value] = $this_doc[$f_name];
				
			}
			else
			{
				$tempdef_data[$ii][f_value] = "";
			}


			if($this_tempdef[type] == "File")
			{
				$tempdef_data[$ii][f_output]= urlencode($tempdef_data[$ii][f_value]);

				if($tempdef_data[$ii][arithmetic] != "" && $tempdef_data[$ii][f_value] != "")
				{
					$rep1 = sprintf('${%s}', $this_tempdef[cname]);
					$f_value_file = $tempdef_data[$ii][f_value];
					//if(strpos($f_value_file, "http://") !== 0) $f_value_file = urlencode($f_value_file);
					$tempdef_data[$ii][f_output] = str_replace($rep1, $f_value_file,  $tempdef_data[$ii][arithmetic]);
				}
				else
				{
					$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
				}
			}
			else
			{
				$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
			}

		}
		
		
		
/*
		if( $tempdef_data[$ii][f_value] == "")
		{
			printf("发布模板域[%s]出错<br>\n", $this_tempdef[cname]);
			return -1;
		}
*/
		
		//---HUNXIAO_CODE<103>---
		
		$eval_Table["$this_tempdef[f_name]"] = $tempdef_data[$ii][f_value];
		
		$display_len = 100;
		
		$display_value = $tempdef_data[$ii][f_value];
		if(strlen($display_value) > $display_len)
		{
			$display_value = cn_substr($display_value, $display_len-2);
			$display_value .= "....";
		}

		printf("[%s] ===> [%s]<br>\n", $this_tempdef[cname], htmlspecialchars($display_value, ENT_QUOTES));
		
		//	printf("\n&&&&&&&&&&&&&&&&&&&&&&&&&&&<br>\n%s\n&&&&&&&&&&&&&&&&&&&&&&&&&&&<br>\n", perl_buf);
		//	printf("----------%s--------------%s------------%s----- \n<br>", f_name, cname, f_value);
	}
	
	return 0;
}

function publishDocField_poly($p_id, $t_id, $d_id, $poly)
{
	global  $root_path, $file_base;
	global $proj_data, $poly_data, $temp_data, $tempdef_data, $global_data;
	global $pub_mysql, $proj_mysql;
	global $this_doc;
	global $async_buf;
	global $eval_Table;
	
	
	foreach($tempdef_data as $ii=>$this_tempdef)
	{
		
		$f_id = $this_tempdef[f_id];
		$f_name = $this_tempdef[f_name];
		$arithmetic = $this_tempdef[arithmetic];
		
		if($this_tempdef[type] == "Sql_Result")
		{
			$tempdef_data[$ii][f_value] = doAboutSql($f_id, $f_name, $arithmetic, $p_id, $t_id, $d_id, $poly);
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
		}
		else if($this_tempdef[type] == "Sql_Result_Ex")
		{
			//$tempdef_data[$ii][f_value] = doAboutSql_Ex(proj_mysql, pTF[i].f_name, pTF[i].arithmetic, p_id, t_id, d_id, poly);
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
		}
		else if($this_tempdef[type] == "Php_List")
		{
			$tempdef_data[$ii][f_value] = doAboutPhp_List($f_id, $f_name, $arithmetic, $p_id, $t_id, $d_id, $poly);
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
		}
		else if($this_tempdef[type] == "Form_List")
		{
			$tempdef_data[$ii][f_value] = doAboutForm_List($f_id, $f_name, $arithmetic, $p_id, $t_id, $d_id, $poly);
			$tempdef_data[$ii][f_output] = $tempdef_data[$ii][f_value];
		}
		else
		{
			continue;
		}

		/*
		
		if(!pTF[i].f_value)
		{
			printf("发布模板[%s]域出错: %s<br>\n", pTF[i].cname, error_message);
			return -1;
		}
		*/
		
		
		
		printf(".");



		$eval_Table["$this_tempdef[f_name]"] = $tempdef_data[$ii][f_value];
		//---HUNXIAO_CODE<104>---

		if(is_array($tempdef_data[$ii][f_value]))
		{
			$display_value = print_r($tempdef_data[$ii][f_value], true);
		}
		else
		{
			$display_value = $tempdef_data[$ii][f_value];
		}

		$display_len = 100;
		if(strlen($display_value) > $display_len)
		{
			$display_value = cn_substr($display_value, $display_len - 2);
			$display_value .= "....";
		}
		printf("[%s] ===> [%s]<br>\n", $this_tempdef[cname], htmlspecialchars($display_value, ENT_QUOTES));
		
		//	printf("\n&&&&&&&&&&&&&&&&&&&&&&&&&&&<br>\n%s\n&&&&&&&&&&&&&&&&&&&&&&&&&&&<br>\n", perl_buf);
		//	printf("----------%s--------------%s------------%s----- \n<br>", f_name, cname, f_value);
	}
	
	
	return 0;
}

function doAboutURL($p_id, $t_id, $d_id, $poly_index)
{
	global $proj_mysql;
	global $poly_data;
	global $this_doc;
	global $tempdef_data;
	global $temp_data;

	$url_field = sprintf("url_%d",$poly_index);
	$defaulturl = $this_doc[$url_field];

	
	if( $defaulturl == "")
	{
		$poly_data[$poly_index][doc_url] = $defaulturl;
		return 0;
	}
	
	$doc_url = $defaulturl;

	$doc_url = str_replace('${docid}', $d_id, $doc_url);
	$doc_url = str_replace('${tempid}', $t_id, $doc_url);
	$doc_url = str_replace('${projid}', $p_id, $doc_url);

	
	$doc_url = str_replace('${createdate}', $this_doc[createdate], $doc_url);

	$pos = strpos($doc_url, '${');
	if($pos)
	{
		foreach($tempdef_data as $row)
		{
			$rep1 = sprintf('${%s}', $row[cname]);
			$doc_url = str_replace($rep1, $row[f_value], $doc_url);
		}
	}

	$doc_url = trim($doc_url);
	$sqlstr = sprintf("update %s set url_%d='%s' where d_id=%d", $temp_data[$t_id][t_name], $poly_index, $doc_url, $d_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr . "\n");

	$poly_data[$poly_index][doc_url] = $doc_url;
	return 0;
}



function doAboutSql($field_id, $field_name, $arithmetic, $p_id, $t_id, $d_id, $poly)
{
	global  $root_path, $file_base;
	global $proj_data, $poly_data, $temp_data, $tempdef_data, $global_data;
	global $pub_mysql, $proj_mysql;
	global $this_doc;
	global $async_buf;
	global $eval_Table;

	
	$arithmetic= trim($arithmetic);
	$arithmetic1 = substr($arithmetic, 1);
	
	$pairs = explode("\n#", $arithmetic1);
	foreach($pairs as $pair)
	{
		$sp=explode(":", $pair, 2);
		if($sp[0] == "sql")    $sql_value=$sp[1];
		if($sp[0] == "code")   $code_value=$sp[1];
		if($sp[0] == "html")   $html_value=$sp[1];
		if($sp[0] == "init")   $init_value=$sp[1];
		if($sp[0] == "end")   $end_value=$sp[1];
	}
	
	
	$sql_value = trim($sql_value);
	$code_value = trim($code_value);
	$html_value =  trim($html_value);
	$init_value =  trim($init_value);
	$end_value =  trim($end_value);

	
	if($sql_value == "" && $code_value == "" && $html_value == "" && $init_value == "" && $end_value == "")
	{
		if($arithmetic{0} != '#') return -1;
		$sql_value= substr($arithmetic, 1);
		
		foreach($tempdef_data as $ii=>$pTF)
		{
			if($pTF[type] =="PostInPage") continue;

			if( $pTF[f_name] == $field_name )
			{
				break;
			}
			else if( $tempdef_data[$ii][f_value]  !== -1)
			{
				$rep1 = sprintf('${%s}', $pTF[cname]);
				$sql_value = str_replace($rep1, $tempdef_data[$ii][f_value], $sql_value);
			}
			else
			{
				$error_message = sprintf("%s: 没有值",  $pTF[cname]);
				return -1;
			}
			
		}



		$table=getTablelist($sql_value);
		$table = trim($table);
		$table = trim_big_bracer($table);

		$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $table, $table, $table);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		$row = mysql_fetch_array($res, MYSQL_ASSOC);
		if($row == "") 
		{
			$sql_t_id  =  0;
			$sql_t_name = $table;
		}
		else
		{
			$sql_t_id  =  $row[t_id];
			$sql_t_name = $row[t_name];
		}
	
	
		$rep1 = sprintf('{%s}', $table);
		$sql_value = str_replace($rep1, $sql_t_name, $sql_value);
	
	
	
		$sqlstr = sprintf("select f_name,cname,ename,type from tempdef where t_id=%d", $sql_t_id);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		while($row = mysql_fetch_array($res, MYSQL_ASSOC))
		{
			$rep1 = sprintf('{%s}', $row[cname]);
			$sql_value = str_replace($rep1, $row[f_name], $sql_value);

			$rep1 = sprintf('{%s}', $row[ename]);
			$sql_value = str_replace($rep1, $row[f_name], $sql_value);
		}
	
		
		$fields = getFieldlist($sql_value);
		foreach($fields as $field)
		{
			if($field ==  "url")
			{
				$rep2 = sprintf('url_%d', $poly);
				$sql_value = str_replace("url", $rep2, $sql_value);
			}
		}
		
		
		sprintf(sqlstr, "%s", sql_value);

		$sqlstr = $sql_value;
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		$row = mysql_fetch_array($res, MYSQL_NUM);

		if($row != "")
		{
			$first_result = join("", $row);
			return $first_result;
		}
		else
		{
			return "";
		}
	}
	
	
	
	//printf("\n=====%s-------\n======%s----------\n========%s--------\n", $sql_value, $code_value, $html_value);
	
	
	$nocode = 0;
	if($code_value == "") $nocode=1;
	if($sql_value == "")
	{
		$nosql=1;
	}
	else
	{
	
		$sql_value = str_replace('${docid}', $d_id, $sql_value);
		
		$table=getTablelist($sql_value);
		$table = trim_big_bracer($table);
		
	
		$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $table, $table, $table);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		$row = mysql_fetch_array($res, MYSQL_ASSOC);
		if($row == "")
		{
			$sql_t_id  =  0;
			$sql_t_name = $table;
		}
		else
		{
			$sql_t_id  =  $row[t_id];
			$sql_t_name = $row[t_name];
		}
		
		
		$rep1 = sprintf('{%s}', $table);
		$sql_value = str_replace($rep1, $sql_t_name, $sql_value);
	
		
		
		$sqlstr = sprintf("select f_name,cname,ename,type from tempdef where t_id=%d", $sql_t_id);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	
		$mark_str="NOT_SQL_REPLACE_OF_GHC";
		while($row = mysql_fetch_array($res, MYSQL_ASSOC))
		{
			
			$f_name  = $row[f_name];
			$f_cname = $row[cname];
			$f_ename = $row[ename];
			$type    = $row[type];
			
			
			$rep_cname = sprintf('${%s}', $f_cname);
			$sql_value = str_replace($rep_cname, $mark_str, $sql_value);
	
			if(!$nocode) $code_value = str_replace($rep_cname, $mark_str, $code_value);
			$html_value = str_replace($rep_cname, $mark_str, $html_value);
			
			
			$rep1 = sprintf('{%s}', $f_cname);
			$rep2 = sprintf('%s', $f_name);

			$sql_value = str_replace($rep1, $rep2, $sql_value);
			
			
			$rep1 = sprintf('{%s}', $f_cname);
			$rep2 = sprintf('$row_eval_sql[%s]', $f_name);
			
			if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
			$html_value = str_replace($rep1, $rep2, $html_value);
			
			
			$sql_value = str_replace($mark_str, $rep_cname, $sql_value);
			if(!$nocode) $code_value = str_replace($mark_str, $rep_cname, $code_value);
			$html_value = str_replace($mark_str, $rep_cname, $html_value);
		}
		
		
		
		
		$fields = getFieldlist($sql_value);

		foreach($fields as $i=>$field)
		{
			if($field == "url")
			{
				$rep2 = sprintf("url_%d", $poly);
				$sql_value = str_replace("url", $rep2, $sql_value);
				
				$rep2 = sprintf('$row_eval_sql[url_%d]', $poly);
				$code_value = str_replace('{url}', $rep2, $code_value);
				$html_value = str_replace('{url}', $rep2, $html_value);
				continue;
			}
			
			
			$rep1 = sprintf('{%s}', $field);
			$rep2 = sprintf('$row_eval_sql[%s]', $field);

			if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
			$html_value = str_replace($rep1, $rep2, $html_value);

			continue;
			
			
			if( !strchr($field, '(') && !strchr($field, ')') && !strchr($field, '\'') && !strchr($field, '%') )
			{
				continue;
			}
			
			$field_buf = $field;
			
			$field_buf = str_replace("(", "\\(", $field_buf);
			$field_buf = str_replace(")", "\\)", $field_buf);
			$field_buf = str_replace("'", "\\'", $field_buf);
			$field_buf = str_replace("%", "\\%", $field_buf);
			
			// printf("\n--------%s--------\n", field_buf);
			
			$rep1 = sprintf(rep1, '${\'sql_%s\'}', $field);
			sprintf(rep2, '${\'sql_%s\'}', $field_buf);
			if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
			$html_value = str_replace($rep1, $rep2, $html_value);
			
			$fields[$i] = $field_buf;
		}

	}
	
	
	
	//  printf("###################%s########################<br>\n", field_name);
	//  return pnull;
	
	$mark_str = array(
			"MARK_OF_EXECSQL_GHC", "MARK_OF_EXECSQL_ONE_GHC",
			"MARK_OF_EXECSQL_TWO_GHC", "MARK_OF_EXECSQL_THREE_GHC",
			"MARK_OF_EXECSQL_FOUR_GHC", "MARK_OF_EXECSQL_FIVE_GHC"
		);
	$mark = array();

	$mark_num=-1;
	foreach($tempdef_data as $ii=>$pTF)
	{

		if($pTF[f_name] == $field_name )
		{
			break;
		}
		
		$f_name  = $pTF[f_name];
		$f_cname = $pTF[cname];
		$f_ename = $pTF[ename];

		
		$p=strstr($code_value, "mysql_query(");
		while($p)
		{
			$mark_num++;
			$pos = strpos($p, ");");
			$mark[$mark_num] =  substr($p,0, $pos+1);
			$p1 = substr($p, $pos);

			$code_value = str_replace($mark[$mark_num], $mark_str[$mark_num], $code_value);
			$p=strstr($p1, "execsql");
		}

		
		$rep1 =  sprintf('\'${%s}\'', $f_cname);
		$rep2 =  sprintf('${%s}', $f_cname);
		if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
		$html_value = str_replace($rep1, $rep2, $html_value);
		
		
		
		
		$rep1 = sprintf('${%s}', $f_cname);
		$rep2 = sprintf('$eval_Table[%s]', $f_name);
		if(!$nosql) $sql_value = str_replace($rep1, $rep2, $sql_value);
		if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
		$html_value = str_replace($rep1, $rep2, $html_value);
		$init_value = str_replace($rep1, $rep2, $init_value);
		$end_value = str_replace($rep1, $rep2, $end_value);
		
		
	}

	print "***********************************<br>\n";
	print "$code_value\n";
	print "***********************************<br>\n";

	//---HUNXIAO_CODE<105>---
	
	for($i=0; $i<=$mark_num; $i++)
	{
		$code_value = str_replace($mark_str[$i], $mark[$i], $code_value);
	}
	

	 //print_r($eval_Table);
	 if($sql_value != "")
	 {
		$my_code = sprintf('%s%s$sql_value = "%s";', $init_value, "\n", $sql_value);
		//printf("----------------------------\n%s\n---------------------------\n", $my_code);
		eval($my_code);
	 }

	 //printf("\n=====%s=====\n======%s=======\n========%s========\n", "$sql_value", $code_value, $html_value);


	$my_code = "\$f_value='';\n";

	$func_postfix = sprintf('%s_%s_%s_%s', $p_id, $t_id, $d_id, $field_name);
	$my_code = <<<GHC_OF_END
 function eval_func_of_publish_$func_postfix(\$sqlstr_of_eval)
 {
	global \$proj_mysql;
	global \$eval_Table;
	global \$this_doc;

	$init_value

	\$f_value = "";
	
GHC_OF_END;


	if(!$nosql)
	{
		$my_code .= <<<GHC_OF_END
\$res_eval_sql = mysql_query(\$sqlstr_of_eval, \$proj_mysql) or exit(mysql_error() . "\\n" . \$sqlstr_of_eval);
\twhile (\$row_eval_sql =  mysql_fetch_array(\$res_eval_sql, MYSQL_ASSOC))
\t{\n\n
GHC_OF_END;
	}

	$my_code .= "\n$code_value";
	$my_code .= sprintf("\n\$f_value .=<<<GHC_OF_EVAL_END\n%s\nGHC_OF_EVAL_END;\n", $html_value);


	if(!$nosql)
	{
		$my_code .= "\n\n\t}\n";
	}

$my_code .= <<<GHC_OF_END
\t$end_value
\treturn \$f_value;\n
 }
return eval_func_of_publish_$func_postfix(\$sql_value);
GHC_OF_END;

	$result= eval($my_code);
	if($result === false)
	{
		printf("<br>==============%s=============<br>\n%s\n<br>========================<br>\n", $field_name, $my_code);
	}
	return $result;
}


function doAboutWhere($arithmetic, $p_id, $t_id, &$table_name)
{
	global $proj_mysql;
	global $temp_data;
	
	$sp = explode(":", $arithmetic, 2);
	$temp = $sp[0];
	$condition = $sp[1];

	$temp = trim_big_bracer($temp);
	
	
	$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $temp, $temp, $temp);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if($row == "") return "";
	
	$t_id1 = $row[t_id];
	$t_name = $row[t_name];
	
	if($table_name !== 0) $table_name = $t_name;
	
	
	
	$sqlstr = sprintf("select f_name, cname, ename from tempdef where t_id=%d", $t_id1);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	while ( $row = mysql_fetch_array($res) )
	{
		$f_name=$row[f_name];
		$cname=$row[cname];
		$ename=$row[ename];
		
		$rep1 = sprintf("{%s}", $cname);
		$condition = str_replace($rep1, $f_name, $condition);
	}
	
	return $condition;
}

function sendPostInfo($p_id, $t_id, $d_id)
{
	global $pub_mysql;
	$sqlstr = "select * from  pub_queue where p_id=$p_id and  t_id=$t_id and  d_id=$d_id";
	$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if($row != "") return;
	$sqlstr = "insert into pub_queue set p_id=$p_id, t_id=$t_id, d_id=$d_id, createdt=now()";
	$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
}

function AddDoc($p_id, $t_id)
{
	
	global $proj_mysql;
	global $temp_data, $tempdef_data, $poly_data;
	
	
	
	$field_page = 0;
	$condition = 0;
	$limit_length = 0;
	
	$list_table_name = $temp_data[$t_id][t_name];
	$table_name = "";
	
	$sqlstr = sprintf("select cname, type, arithmetic, f_name from tempdef where t_id=%d", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	while ($row=mysql_fetch_array($res))
	{
		$cname=$row[cname];
		$type=$row[type];
		$arithmetic = $row[arithmetic];
		$f_name = $row[f_name];
		
		if($type == "Where_Clause")
		{
			$condition = doAboutWhere($arithmetic, $p_id, $t_id, $table_name);
		}
		else if($type == "Temp_Field")
		{
			if($cname == "limit_length")
			{
				$limit_length = trim($arithmetic);
			}
			else if($cname == "page")
			{
				$field_page = $f_name;
			}
		}
	}
	
	/*
	print "condition: $condition\n";
	print "field_page: $field_page\n";
	print "limit_length: $limit_length\n";
	*/
	
	if($field_page == "" || $condition == "" || $limit_length == "") return 0;
	

	$sqlstr = sprintf("select count(*) ct from %s where published='y' and %s", $table_name, $condition);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if( !$row )
	{
		$error_message = sprintf("No result: %s", $sqlstr);
		return -1;
	}
	$total_rec = $row[ct];
	
	$max_page  = $total_rec/$limit_length;
	if($total_rec % $limit_length == 0 && $max_page >0 ) $max_page --;
	
	
	
	
	$sqlstr = sprintf("select d_id, %s from %s order by %s asc", $field_page, $list_table_name, $field_page);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	
	$list_count = mysql_num_rows($res);
	$page_arr = array();
	while($row = mysql_fetch_array($res))
	{
		$page_arr[] = array('d_id'=>$row[0], 'page'=>$row[1]);
	}
	
	
	for($page=0; $page <= $max_page; $page++)
	{
		
		$flag =0;
		$last_docid = 0;
		for($i=0; $i<$list_count; $i++)
		{
			if($page_arr[$i][page] == $page-1 )
			{
				$last_docid = $page_arr[$i][d_id];
			}
			else if($page_arr[$i][page] == $page )
			{
				$flag=1;
				break;
			}
		}

		
		if( $flag == 0 )
		{

			printf("page:%d, last_docid:%d\n", $page, $last_docid);

			$sqlstr = sprintf("insert into %s set createdatetime=now(), %s=%d", $list_table_name, $field_page, $page);

			foreach($poly_data as $ii=>$this_poly) 
			{
				$sqlstr .= sprintf(", url_%d='%s'", $ii, $this_poly[defaulturl]);
			}
			$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
			$d_id = mysql_insert_id($proj_mysql);

			sendPostInfo($p_id, $t_id, $d_id);
			if($last_docid> 0 ) sendPostInfo($p_id, $t_id,$last_docid);

			$sqlstr =  "select t_id, t_name from temp where cname = 'page_script'";
			$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n". $sqlstr);
			$row = mysql_fetch_array($res);
			if( $row == "")
			{
				$error_message = sprintf("No Result:[%s]", $sqlstr);
				return -1;
			}
			
			$page_js_t_id = $row[t_id];
			$page_js_t_name = $row[t_name];

			
			$sqlstr = sprintf("select f_name from tempdef where cname='list_tid' and t_id=%d", $page_js_t_id);
			$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n". $sqlstr);
			$row = mysql_fetch_array($res);
			if( $row == "")
			{
				$error_message = sprintf("No Result:[%s]", $sqlstr);
				return -1;
			}

			$list_tid_f_name = $row[f_name];


			$sqlstr = sprintf("select d_id from %s where %s=%d", $page_js_t_name, $list_tid_f_name, $t_id);
			$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n". $sqlstr);
			if( ($row = mysql_fetch_array($res)) )
			{
				sendPostInfo($p_id, $page_js_t_id, $row[d_id]);
			}
		}

		
	}
	
	return 0;
}

		


		
function doAboutPostInPage($p_id, $t_id, $d_id, $TF_index)
{
	global $proj_mysql;
	global $tempdef_data;
	global $eval_Table;
	

	$pTF = $tempdef_data[$TF_index];

	$arithmetic = $pTF[arithmetic];
	$sp=explode(":", $arithmetic, 2);
	$temp=$sp[0];
	$condition=$sp[1];

	$temp = trim_big_bracer($temp);
	
	$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $temp, $temp, $temp);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if($row == "") return "";
	
	
	$t_id1 = $row[t_id];
	$t_name = $row[t_name];
	
	foreach($tempdef_data as $pTF)
	{
		$f_name=$pTF[f_name];
		$cname=$pTF[cname];
		
		$rep1 = sprintf('${%s}', $cname);
		$rep2 = sprintf('$eval_Table[%s]', $f_name);

		
		$condition = str_replace($rep1, $rep2, $condition);
		$condition = str_replace("{d_id}", "d_id", $condition);
	}
	
	
	$sqlstr = sprintf("select f_name, cname, ename from tempdef where t_id=%d", $t_id1);
	$row = mysql_query($sqlstr , $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	while ($row=mysql_fetch_array($res))
	{
		$f_name=$row[f_name];
		$cname=$row[cname];
		$ename=$row[ename];
		
		$rep1 = sprintf('{%s}', $cname);
		$condition = str_replace($rep1, $f_name, $condition);
	}
	
	//---HUNXIAO_CODE<106>---


	$sqlstr = sprintf("select d_id from %s where %s", $t_name, $condition);
	$my_code = sprintf('$sqlstr = "%s";', $sqlstr);


	eval($my_code);

	print "$sqlstr<br>\n";

	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$doc_ids_arr = array();
	while($row = mysql_fetch_array($res))
	{
		$doc_ids_arr[] = $row[d_id];
	}
	sendPostInfo($p_id, $t_id1, 0);

	foreach($doc_ids_arr as $docid)
	{
		sendPostInfo($p_id, $t_id1, $docid);
		printf("add   %d,%d,%s<br>\n", $p_id, $t_id1, $docid);
	}

	return 0;
}



function doAboutPhp_List($field_id, $field_name, $arithmetic, $p_id, $t_id, $d_id, $poly)
{
	global $proj_mysql;
	global $temp_data, $tempdef_data, $poly_data;
	global $db_name;
	global $root_path;

	$item_begin_mark = "<--------------------item_loop_begin-------------------->";
	$item_end_mark   = "<--------------------item_loop_end-------------------->";
	
	$arithmetic = trim($arithmetic);
	$init_value = "";
	$sql_value = "";
	$code_value = "";
	$html_value = "";
	$end_value = "";

	$nosql = 0;
	$nocode = 0;

	$arithmetic1 = substr($arithmetic, 1);
	
	$pairs = explode("\n#", $arithmetic1);
	foreach($pairs as $pair)
	{
		$sp=split(":", $pair, 2);
		if($sp[0] == "sql")    $sql_value=$sp[1];
		if($sp[0] ==  "code")   $code_value=$sp[1];
		if($sp[0] == "html")   $html_value=$sp[1];
		if($sp[0] == "init")   $init_value=$sp[1];
		if($sp[0] == "end")   $end_value=$sp[1];
	}




	$pos_begin = strpos($html_value, $item_begin_mark);
	$pos_end   = strpos($html_value, $item_end_mark);

	if($pos_begin && $pos_end)
	{
		$pre_html = substr($html_value, 0, $pos_begin);
		$post_html = substr($html_value, $pos_end + strlen($item_end_mark));
		$pos = $pos_begin + strlen($item_begin_mark);
		$html_value = substr($html_value, $pos, $pos_end - $pos);
	}


	//print "####################################################################################<br>\n";
	//printf("%s\n----------------------\n%s\n--------------------\n%s\n", $pre_html, $html_value, $post_html);
	
	$table = getTablelist($sql_value);
	$table = trim($table);
	$table = trim_big_bracer($table);
	
	$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $table, $table, $table);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res);
	if($row == "")
	{
		$error_message = sprintf("No such data: %s", $sqlstr);
		return 0;
	}
	
	$sql_t_id= $row[t_id];
	$sql_t_name= $row[t_name];
	
	$rep1 = sprintf("{%s}", $table);
	$sql_value = str_replace($rep1, $sql_t_name, $sql_value);

	$ct_sql = sprintf("select count(*) ct from %s", $sql_t_name);

	$sqlstr = sprintf("select f_name,cname,ename,type from tempdef where t_id=%d", $sql_t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$mark_str="NOT_SQL_REPLACE_OF_GHC";
	while($row=mysql_fetch_array($res))
	{
		
		$f_name=$row[f_name];
		$f_cname=$row[cname];
		$f_ename=$row[ename];
		$type=$row[type];
		
		
		$rep_cname = sprintf('${%s}', $f_cname);
		$sql_value = str_replace($rep_cname, $mark_str, $sql_value);


		if(!$nocode) $code_value = str_replace($rep_cname, $mark_str, $code_value);
		$html_value = str_replace($rep_cname, $mark_str, $html_value);
		
		
		$rep1 = sprintf('{%s}', $f_cname);
		$rep2 = sprintf('%s', $f_name);
		$sql_value = str_replace($rep1, $rep2, $sql_value);
		
		
		$rep1 = sprintf('{%s}', $f_cname);
		$rep2 = sprintf('$php_row[%s]', $f_name);
		
		if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
		$html_value = str_replace($rep1, $rep2, $html_value);
		
		
		$sql_value = str_replace($mark_str, $rep_cname, $sql_value);
		if(!$nocode) $code_value = str_replace($mark_str, $rep_cname, $code_value);
		$html_value = str_replace($mark_str, $rep_cname, $html_value);
		
		
	}

	$rep2 = sprintf("url_%d", $poly);
	$sql_value = str_replace("url", $rep2, $sql_value);

	$rep2 = sprintf('$php_row[url_%d]', $poly);
	$code_value = str_replace('{url}', $rep2, $code_value);
	$html_value = str_replace('{url}', $rep2, $html_value);
	$html_value = str_replace("\"", "\\\"", $html_value);
	$pre_html  =  str_replace("\"", "\\\"", $pre_html);
	$post_html =  str_replace("\"", "\\\"", $post_html);

	//---HUNXIAO_CODE<107>---


	$fields = getFieldlist($sql_value);
	foreach($fields as $field)
	{
		$rep1 = sprintf('{%s}', $field);
		$rep2 = sprintf('$php_row[%s]', $field);
		if(!$nocode) $code_value = str_replace($rep1, $rep2, $code_value);
		$html_value = str_replace($rep1, $rep2, $html_value);
	}

	$sql_value = trim($sql_value);

	printf("\n=====%s=====\n======%s=======\n========%s========\n", $sql_value, $code_value, $html_value);


	if(preg_match("/\s+limit\s+(\d+),/", $sql_value, $matches))
	{
		$php_list_func = "get_phplist_nopage";
		$limit_length = 10;
	}
	else if(preg_match("/\s+limit\s+(\d+)/", $sql_value, $matches))
	{
		$limit_length = $matches[1];
		$php_list_func = "get_phplist_page";
	}
	else
	{
		$php_list_func = "get_phplist_nopage";
		$limit_length = 10;
	}

	$postfix = sprintf("%s_%s_%s_%s", $p_id, $t_id, $d_id, $field_id);
	
	$dollar = '$';
	if($sql_value == "")
	{
		$buff = <<<GHC_OF_END
<?php
require_once('$root_path/plib/config_inc.php');
${dollar}db_name = '$db_name';
require_once('$root_path/pagelib/db.php');
require_once('$root_path/pagelib/global_func.php');
function php_list_func_$postfix()
{
$init_value
$code_value
}
${dollar}php_ret_$field_id = php_list_func_$postfix();
?>
GHC_OF_END;
		return $buff;
	}



	$buff =<<<GHC_OF_END
<?php
require_once('$root_path/plib/config_inc.php');
${dollar}db_name = '$db_name';
require_once('$root_path/pagelib/pagelist.php');
function php_list_func_$postfix()
{
	$init_value
	${dollar}sqlstr = "$sql_value";
	${dollar}limit_length = $limit_length;
	${dollar}ret  = $php_list_func(${dollar}sqlstr, ${dollar}limit_length);
	${dollar}php_res = ${dollar}ret[php_res];
	${dollar}ret[php_list] = "$pre_html";
	while(${dollar}php_row= mysql_fetch_array(${dollar}php_res))
	{
		$code_value
		${dollar}ret[php_list] .= "$html_value";
	}
	${dollar}ret[php_list] .= "$post_html";
	$end_value
	return ${dollar}ret;
}
${dollar}php_ret_$field_id = php_list_func_$postfix();
?>
GHC_OF_END;
	
	return $buff;
}



function doAboutForm_List($field_id, $field_name, $arithmetic, $p_id, $t_id, $d_id, $poly)
{
	global $proj_mysql;
	global $poly_data, $temp_data, $tempdef_data;

	$item_begin_mark = "<--------------------item_loop_begin-------------------->";
	$item_end_mark   = "<--------------------item_loop_end-------------------->";

	$nosql=0;
	$nocode=0;


	
	$arithmetic= trim($arithmetic);
	$sql_value = "";
	$code_value = "";
	$html_value = "";
	
	$arithmetic1 = substr($arithmetic, 1);
	$pairs = explode("\n#", $arithmetic1);
	foreach($pairs as $pair)
	{
		$sp = explode(":", $pair, 2);
		if($sp[0] == "from")   $sql_value=$sp[1];
		if($sp[0] == "html")   $html_value=$sp[1];
	}

	$item_begin = strpos($html_value, $item_begin_mark);
	$item_end   = strpos($html_value, $item_end_mark);

	if($item_begin !== false  && $item_end !== false )
	{
		$pre_html = substr($html_value, 0, $item_begin);
		$post_html = substr($html_value, $item_end + strlen($item_end_mark));
		$pos = $item_begin + strlen($item_begin_mark);
		$html_value = substr($html_value, $pos, $item_end - $pos); 
	}


	//printf("%s\n----------------------\n%s\n--------------------\n%s\n", $pre_html, $html_value, $post_html);
	
	$table = trim($sql_value);
	$table = trim_big_bracer($table);
	
	
	$sqlstr = sprintf("select t_id, t_name from temp where t_name='%s' or cname='%s' or ename='%s'", $table, $table, $table);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res, MYSQL_ASSOC);
	if($row == "") 
	{
		$error_message = "No result: $sqlstr";
		return 0;
	}
	
	$sql_t_id= $row[t_id];
	$sql_t_name= $row[t_name];
	
	$sqlstr = sprintf("select f_name, cname, ename, type, f_id from tempdef where t_id=%d and showorder < 1000 order by showorder  asc, f_id asc", $sql_t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);


	$script_buf = sprintf("<script type=\"text/javascript\">\nfunction checkForm(thisform)\n\t{\n");

	while($row=mysql_fetch_array($res))
	{
		$f_name=$row[f_name];
		$f_cname=$row[cname];

		$script_buf .= sprintf("\tif(thisform.%s.value == ''){ alert('%s不能为空'); thisform.%s.focus(); return false; }\n", $f_name, $f_cname, $f_name);
		
	}
	$script_buf .= sprintf("\n\treturn true;\n}\n</script>\n");

	
	if(strstr($pre_html, "<form>"))
	{
		$buff = sprintf("<form method=post name=myform action=/addmess.php?p_id=%d&t_id=%d onsubmit=\"return checkForm(this);\">", $p_id, $sql_t_id);
		$pre_html = str_replace("<form>", $buff, $pre_html);
		$buff .= $script_buf;
	}

	$buff .= $pre_html;


	mysql_data_seek($res, 0);
	while($row=mysql_fetch_array($res))
	{
		$f_name= $row[f_name];
		$f_cname= $row[cname];
		$f_ename= $row[ename];
		$type=    $row[type];
		$f_id = $row[f_id];
		
		$item_buf = $html_value;
		$item_buf = str_replace("{name}", $f_cname, $item_buf);

		if($type == "Text" || $type == "RichText" || $type == "AutoTypeset_Text" )
		{
			$rep2 = sprintf("<textarea class=messageinput id=in%s name=%s rows=10 cols=40></textarea>", $f_id, $f_name);
			$item_buf = str_replace('{input}', $rep2, $item_buf);
		}
		else if($type == "File")
		{
			$rep2 = sprintf("<input type=file class=messageinput id=in%s  name=%s size=30>", $f_id, $f_name);
			$item_buf = str_replace('{input}', $rep2, $item_buf);
		}
		else
		{
			$rep2 = sprintf("<input type=input class=messageinput id=in%s   name=%s size=30>", $f_id, $f_name);
			$item_buf = str_replace('{input}', $rep2, $item_buf);
		}

		$buff .= $item_buf;
	}

	$buff .= $post_html;
	return $buff;
}




function doAboutDataPost($p_id, $t_id, $d_id)
{
	global $proj_mysql;
	global $poly_data, $temp_data, $tempdef_data;


	$t_name=$temp_data[$t_id]['t_name'];

	$this_doc = get_table_row("select * from $t_name where d_id=$d_id");
	if($this_doc == "")
	{
		printf("数据后处理，无法获取文档数据<br>\n");
		return;
	}


	foreach($tempdef_data as $this_tempdef)
	{
		if($this_tempdef['type'] != 'DataPost') continue;


		$arithmetic = $this_tempdef['arithmetic'];
		if($arithmetic == "") continue;
		$this_f_id = $this_tempdef['f_id'];


		$eval_code .= <<< GHC_OF_END
		function data_post_process_$this_f_id(\$row_doc)
		{
			global \$proj_mysql;
			global \$poly_data, \$temp_data, \$tempdef_data;

			$arithmetic
		}

		data_post_process_$this_f_id(\$this_doc);
GHC_OF_END;

		eval($eval_code);

	}

}





function asyncDoc($p_id, $t_id, $d_id)
{
	global $poly_data;
	global $tempdef_data;

	
	foreach($poly_data as $pm_id=>$this_poly)
	{
		if($this_poly[rcp_server] == "" || $this_poly[rsync_name] == "") continue;

		$doc_url = $this_poly[doc_url];
		$pos = strrpos($doc_url, "/");
		if($pos)
		{
			$doc_filename = substr($doc_url, $pos+1);
			$doc_path = substr($doc_url, 0, $pos);
			if($doc_path{0} == '/') $doc_path = substr($doc_path, 1);
		}
		else
		{
			$doc_filename = $doc_url;
			$doc_path = "";
		}


		$first_command = sprintf("(cd %s; rsync -avz  -R %s --include \"%s\"", $this_poly[file_path], $doc_path, $doc_filename);
		
		foreach($tempdef_data as $pTF)
		{
			char *f_value;
			if($pTF[type] !=  "File")  continue;
			if($pTF[f_value] ==  "") continue;
			$command .= sprintf(' --include "%s"', $pTF[f_value]);
			
		}

		
		
		$sp = explode(",",  $this_poly[rcp_server]);
		foreach($sp as $item)
		{
			$command= sprintf(' %s %s --exclude "*.*"  %s::%s)', $first_command, $command, trim($item), $this_poly[rsync_name]);
			
			printf("%s<br>\n", $command);
			system($command);
		}
	}
	
	
	return 0;
}



function doAboutMultiPage(&$mp)
{
	global $page_split_mark;

	$spos = "";
	if($mp[method] == 1)
	{
		printf("手工分页<br>\n");
		$sp_m = explode($page_split_mark, $mp[data]);
		$page_num = count($sp_m);
		printf("page_num: %d<br>\n", $page_num);
		return $sp_m;
	}
	else if($mp[method] == 2)
	{
		printf("自动分页<br>\n");
		if(strpos($mp[data], $page_split_mark) !== false)
		{
			$mp[data] = str_replace($page_split_mark, "", $mp[data]);
		}
		$len = html_length($mp[data]);
		if($len > $mp[max_length] )
		{
			$spos = page_split($mp[data], $mp[length], $page_num);
			printf("page_num: %d<br>\n", $page_num);
		}
		else
		{
			printf("自动分页没有达到条件.<br>\n");
		}
	}
	else
	{
		printf("没有开启分页功能<br>\n");
	}

	return $spos;
	
	
}




function genMuitiPageFile($mp, $spos, $html, $doc_filename, $doc_url)
{

	if($mp[method] == 1)
	{
		$page_num = count($spos);

		for($j=0; $j<$page_num; $j++)
		{
			$page_list = gen_page_list($doc_url, $j+1, $page_num);
			$content =  $spos[$j] . $page_list;
			
			$rep1 = sprintf('${%s}', $mp[cname]);
			$multi_html =  $html;
			
			$multi_html = str_replace($rep1, $content, $multi_html);

			
			$pos = strrpos($doc_filename, ".");
			if($pos)
			{
				$pre_path = substr($doc_filename, 0, $pos);
				$post_path = substr($doc_filename, $pos);
			}
			else
			{
				$pre_path = $doc_filename;
				$post_path = "";
			}
			
			
			
			$multi_doc_filename = $doc_filename;
			if($j>0)
			{
				$multi_doc_filename = sprintf("%s_%d%s", $pre_path, $j+1, $post_path);
			}
			printf("%s<br>\n", $multi_doc_filename);
			//printf("%s<br>\n", $page_list);
			$ret=writeFile($multi_doc_filename, $multi_html);
			if($ret == 0)
			{
				printf("%s", $error_message);
			}
		}
	}
	else if($mp[method] == 2 && $spos != "")
	{
		$j = 0;
		foreach($spos as $sp)
		{
			$content = substr($mp[data], $sp[begin], $sp[end] -$sp[begin] +1);
			
			$page_num = count($spos);
			$page_list = gen_page_list($doc_url, $j+1, $page_num);
			$content .= $page_list;
			
			$rep1 = sprintf('${%s}', $mp[cname]);
			$multi_html =  $html;
			
			$multi_html = str_replace($rep1, $content, $multi_html);
			
			$pos = strrpos($doc_filename, ".");
			if($pos)
			{
				$pre_path = substr($doc_filename, 0, $pos);
				$post_path = substr($doc_filename, $pos);
			}
			else
			{
				$pre_path = $doc_filename;
				$post_path = "";
			}
			
			
			$multi_doc_filename = $doc_filename;
			if($j>0)
			{
				$multi_doc_filename = sprintf("%s_%d%s", $pre_path, $j+1, $post_path);
			}
			printf("%s<br>\n", $multi_doc_filename);
			//printf("%s<br>\n", $page_list);
			$ret=writeFile($multi_doc_filename, $multi_html);
			if($ret == 0)
			{
				printf("%s", $error_message);
			}
			
			$j++;
		}
	}
	else
	{
		$rep1 = sprintf('${%s}', $mp[cname]);
		$html = str_replace($rep1, $mp[data], $html);
		writeFile($doc_filename, $html, 0);
	}
}







function getMultiPage($t_id)
{
	global $proj_mysql;
	
	$sqlstr = sprintf("select arithmetic, f_name from tempdef where t_id=%d and type='Multi_Page'", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr . "\n");
	
	$row = mysql_fetch_array($res, MYSQL_ASSOC);
	if($row == "")
	{
		printf("模板%d 没有Multi_Page模板域<br>\n", $t_id);
		return "";
	}
	
	$arithmetic = $row[arithmetic];
	$f_name = $row[f_name];
	$this_TF = getTF($f_name,  "f_name");
	if($this_TF == "")
	{
		$error_message = sprintf("%s not found in TF.", $f_name);
		return "";
	}
	
	$method = $this_TF[f_value];
	
	$fieldname = readFieldData($arithmetic, "#fieldname");
	if($fieldname == "")
	{
		$error_message = sprintf("fieldname not found in %s !", $arithmetic);
		return "";
	}
	
	$length = readFieldData($arithmetic, "#length");
	if($length == "")
	{
		$error_message = sprintf("length not found in %s !", $arithmetic);
		return "";
	}
	
	$max_length = readFieldData($arithmetic, "#max_length");
	if($max_length == "")
	{
		$error_message = sprintf("max_length not found in %s !", $arithmetic);
		return "";
	}
	
	
	
	$mp = array();
	$mp['length'] = $length;
	$mp['max_length'] = $max_length;
	
	$this_TF = getTF($fieldname,  "cname");
	if($this_TF == "")
	{
		$error_message = sprintf("%s not found in TF.", $fieldname);
		return "";
	}
	
	
	$mp[cname] =  $this_TF[cname];
	$mp[f_name] = $this_TF[f_name];
	$mp[data] = $this_TF[f_value];
	$mp[method] = $method;
	
	//	print_r($mp);
	
	return $mp;
}


function page_split($data, $page_length, &$pnum)
{
	$sp = array();
	$this_sp = array('begin'=>0, 'end'=>0);
	$dlen = strlen($data);
	
	print "dlen: $dlen<br>\n";
	
	for($ii=0, $len=0, $flag=0; $ii<$dlen; $ii++)
	{
		$ch = $data{$ii};
		if($ch == '<' && $flag ==0)
		{
			if($len >= $page_length)
			{
				$this_sp['end'] = $ii - 1;
				$sp[] = $this_sp;
				
				$this_sp[begin] = $ii;
				$this_sp[end] = $dlen;
				$len  = 0;
			}
			$flag = 1;
			continue;
		}
		else if($ch  == '>' && $flag ==1)
		{
			if($len >= $page_length)
			{
				$this_sp['end'] = $ii;
				$sp[] = $this_sp;
				
				$this_sp[begin] = $ii + 1;
				$this_sp[end] = $dlen;
				$len  = 0;
			}
			$flag = 0;
			continue;
		}
		else if($flag == 0)
		{
			$len++;
		}
	}
	
	if($this_sp[end] - $this_sp[begin] > 0) $sp[] = $this_sp;
	$pnum = count($sp);
	
	return $sp;
}

function gen_page_list($doc_url, $index, $page_num)
{
	
//	print "index: $index, page_num:$page_num<br>\n";
	
	$pos = strrpos($doc_url, ".");
	if($pos)
	{
		$pre_url = substr($doc_url, 0, $pos);
		$post_url = substr($doc_url, $pos);
	}
	else
	{
		$pre_url = $doc_url;
		$post_url = "";
	}
	
	
	for($i=1; $i<= $page_num;  $i++)
	{
		$this_url = $doc_url;
		if($i > 1)
		{
			$this_url = sprintf("%s_%d%s", $pre_url, $i, $post_url);
		}
		
		if($i==$index -1)
		{
			$last_page = sprintf(" <span class=page> <a href=%s>上一页</a> </span> ", $this_url);
		}
		
		if($i==$index +1)
		{
			$next_page = sprintf(" <span class=page> <a href=%s>下一页</a> </span> ", $this_url);
		}
		
		if($i == $index)
		{
			$page_list .= sprintf(" <span class=page_current>&nbsp;%d&nbsp;</span>",  $i);
		}
		else
		{
			$page_list .= sprintf(" <span class=page>&nbsp;<a href=%s>%d</a>&nbsp;</span> ", $this_url, $i);
		}
	}
	
	$result = sprintf("\n\n<br><br><p align=right>%s%s%s</p>", $last_page, $page_list, $next_page);
	return $result;
}

