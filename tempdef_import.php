<?php




require_once("plib/head.php");
require_once("plib/priv.php");


$cgi = getCGI();
upload_files();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$simple = $cgi['simple'];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);

if($ck_u_type > 2 ) sys_exit("对不起，你没有操作权限",   "");
if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);

$t_name  = $temp_data[$t_id][t_name];
$t_cname = $temp_data[$t_id][cname];
$field_tag = $temp_data[$t_id][field_tag];
$insert_flag = 0;



$field_mark0 =  "#============================";
$field_mark = "\n#============================";

$filename = TMP_PATH . "/" . $cgi[filename];
$file_data = file_get_contents($filename);
if($file_data == "") exit("Error access $filename");

$pos = strpos($file_data, $field_mark0);
if($pos !== 0) $pos = strpos($file_data,  $field_mark);

while($pos !== false)
{
	$off = $pos + strlen($field_mark);
	$pos1 = strpos($file_data, "\n", $off);
	if($pos1 === false) break;

	$pos1++;

	$pos = strpos($file_data, $field_mark, $pos1);

	if($pos === false)
	{
		$this_tempdef = substr($file_data, $pos1); 
	}
	else
	{
		$this_tempdef = substr($file_data, $pos1, $pos - $pos1);
	}

	//print "$this_tempdef\n";
	

	
	$f_id= parse_data_field($this_tempdef, "f_id");
	$f_name = parse_data_field($this_tempdef, "f_name");
	$cname = parse_data_field($this_tempdef, "cname");
	$type = parse_data_field($this_tempdef, "type");
	$real_type = parse_data_field($this_tempdef, "real_type");
	$arithmetic = parse_data_field($this_tempdef, "arithmetic");


	$defaultvalue = parse_data_field($this_tempdef, "defaultvalue");
	$validate = parse_data_field($this_tempdef, "validate");
	$showorder= parse_data_field($this_tempdef, "showorder");
	$showwidth = parse_data_field($this_tempdef, "showwidth");
	$showheight = parse_data_field($this_tempdef, "showheight");
	$showmargin = parse_data_field($this_tempdef, "showmargin");
	$hide = parse_data_field($this_tempdef, "hide");
	$ifnull = parse_data_field($this_tempdef, "ifnull");
	$if_into_db= parse_data_field($this_tempdef, "if_into_db");


	$type = get_type_str($type);

	if($if_into_db == "")
	{
		if( $type == "Char"
		|| $type == "Int"
		|| $type == "Text"
		|| $type == "AutoTypeset_Text"
		|| $type == "Select"
		|| $type == "Rel_Select"
		|| $type ==  "File"
		)
		{
			$if_into_db="y";
		}
		else
		{
			$if_into_db="n";
		}
	}

	if($hide == "") $hide = "n";

	
	if($type == "Sql_Result" && $if_into_db == "n" && $real_type == "") $real_type = "NO_INCLUDE!";



	if($showorder == "") $showorder="0";
	if($showwidth == "") $showwidth="0";
	if($showheight == "") $showheight="0";
	if($showmargin == "") $showmargin="0";


	if($f_id != "")
		$sqlstr = sprintf("select f_id, f_name from tempdef where f_id=%s and t_id=%s", $f_id, $t_id);
	else
		$sqlstr = sprintf("select f_id, f_name from tempdef where cname='%s' and t_id=%s", $cname, $t_id);

	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

	$row = mysql_fetch_array($res);
	if(!$row)
	{
		$sqlstr = sprintf("select f_id, f_name from tempdef where cname='%s' and t_id=%s", $cname, $t_id);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
		$row = mysql_fetch_array($res);
	}

	if( !$row)
	{
		$insert_flag =1;
	}
	else
	{
		$f_id = $row[f_id];
		$old_field_name = $row[f_name];
		$insert_flag = 0;
	}

	//print "f_id: $f_id<br>\n";



	if($insert_flag)
	{
		$sqlstr = sprintf("insert into tempdef set t_id=%s, f_name='%s', cname='%s', type='%s', arithmetic='%s', defaultvalue='%s', validate='%s', showorder='%s', showwidth='%s', showheight='%s', showmargin='%s', hide='%s', ifnull='%s', if_into_db='%s', real_type='%s'", $t_id, $f_name, $cname,  $type, mysql_escape_string($arithmetic), $defaultvalue, mysql_escape_string($validate), $showorder, $showwidth, $showheight, $showmargin, $hide, $ifnull, $if_into_db, $real_type);
	}
	else
	{
		if( ($f_name == "" || strpos($f_name, "sp_f") === 0) )
		{
			$sqlstr = sprintf("update tempdef set cname='%s',  type='%s', arithmetic='%s', defaultvalue='%s', validate='%s', showorder='%s', showwidth='%s', showheight='%s', showmargin='%s', hide='%s', ifnull='%s', if_into_db='%s', real_type='%s' where f_id=%s", 
			 $cname,  $type, mysql_escape_string($arithmetic), $defaultvalue, mysql_escape_string($validate), $showorder, $showwidth, $showheight, $showmargin, $hide, $ifnull, $if_into_db, $real_type, $f_id);

		}
		else
		{
			$sqlstr = sprintf("update tempdef set f_name='%s', cname='%s',  type='%s', arithmetic='%s', defaultvalue='%s', validate='%s', showorder='%s', showwidth='%s', showheight='%s', showmargin='%s', hide='%s', ifnull='%s', if_into_db='%s', real_type='%s' where f_id=%s", 
			 $f_name, $cname,  $type, mysql_escape_string($arithmetic), $defaultvalue, mysql_escape_string($validate), $showorder, $showwidth, $showheight, $showmargin, $hide, $ifnull, $if_into_db, $real_type, $f_id);
		}
	}


	printf("%s<br>\n\n", $sqlstr);


	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

	if( ($f_name == ""  || strpos($f_name, "sp_f") === 0 ) && $insert_flag)
	{
		$f_id=mysql_insert_id($proj_mysql);
		$field_name = sprintf("sp_f%ld", $f_id);
		$sqlstr = sprintf("update tempdef set f_name='%s' where f_id=%ld", $field_name, $f_id);
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	}
	else
	{
		$field_name = $f_name;
	}
	
	if($if_into_db == "y") 
	{

		if($real_type)
		{
			$this_type = $real_type;
		}
		else
		{
			if($type == "Int")
				$this_type = "int(12) not null default '0'";
			else if($type == "Char")
				$this_type = "varchar(255) not null default ''";
			else if($type == "File")
				$this_type = "varchar(255) not null default ''";
			else if($type == "Temp_Field")
				$this_type = "varchar(255) not null default ''";
			else
				$this_type = "blob";
		}

		if($insert_flag)
			$sqlstr = sprintf("alter table %s add %s %s", $t_name, $field_name, $this_type);
		else
		{
			if( ($field_name == "" || strpos($field_name, "sp_f") === 0) )
				$sqlstr = sprintf("alter table %s change %s %s %s", $t_name, $old_field_name, $old_field_name, $this_type);
			else
				$sqlstr = sprintf("alter table %s change %s %s %s", $t_name, $old_field_name, $field_name, $this_type);
		}


		print "$sqlstr<br>\n";
		$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	}


	
}


printf("模板域数据导入成功! <a href='tempdeflist.php?t_id=%s&p_id=%s'>返回</a><br>\n", $t_id, $p_id);
exit("finish!\n");

printf("<script language=javascript> alert('模板域数据导入成功!'); window.location='tempdeflist.php?t_id=%s&p_id=%s'; </script>\n", $t_id, $p_id);


//--------------------------------------------------------------------------------------------------------
function parse_data_field($this_tempdef, $field_name)
{

/*
	print "field_name: $field_name\n\n";
	print $this_tempdef . "\n";
*/

	$mark0 = sprintf("%s:", $field_name);
	$mark = sprintf("\n%s:", $field_name);

	$pos = strpos($this_tempdef, $mark0);
	if($pos !== 0)
	{
		$pos = strpos($this_tempdef, $mark);
		$mlen = strlen($mark);
	}
	else
	{
		$mlen = strlen($mark0);
	}

	if($pos === false) return "";

	$str = substr($this_tempdef, $pos + $mlen);

//	printf('[%s]', $str);

	$pos = strpos($str, "\n---------------------");
	if($pos === false)
	{
		$pos1 = strpos($str, "\n");
		if($pos1 !== false) $str = substr($str, 0, $pos1);
		return trim($str);
	}

	$tstr = substr($str, 0, $pos);
	
	
	if(trim($tstr) != "")
	{
		$pos1 = strpos($str, "\n");
		if($pos1 !== false) $str = substr($str, 0, $pos1);
		return trim($str);
	}


	$pos1 = strpos($str, "\n", $pos + 1);
	if($pos1 === false) return trim($str);

	$pos2 = strpos($str, "\n-----------------------", $pos + 1);

	if($pos2 === false) return trim($str);

	$str = substr($str, $pos1 + 1, $pos2 - $pos1 -1);


	return $str;
}

function get_type_str($ti)
{
	$type_data = array("", "Char", "Text", "AutoTypeset_Text", "File", "Select", "Rel_Select", "Sql_Result");
	if($ti == "") return "";
	if(is_numeric($ti)) return $type_data[$ti];
	return $ti;
}

?>

