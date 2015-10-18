<?php
require_once("plib/head.php");

$cgi = getCGI();
gsql_esc($cgi);
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$f_id = $cgi[f_id];

if($p_id == "" || $t_id == "" || $f_id == "" || $cgi['cname'] == "" || $cgi['type'] == "") sys_exit("参数错误");
conProjDB($p_id, $t_id);


$p_cname = $proj_data[p_cname];
$t_cname = $temp_data[$t_id][cname];
$t_name = $temp_data[$t_id][t_name];

$row_old = $tempdef_data["$f_id"];
if($cgi[f_name] == "") $cgi[f_name] = $row_old[f_name];

if( $cgi[if_into_db] == "d" )
{
	if(in_array($cgi[type], array("Char", "Int", "Text", "File", "AutoTypeset_Text", "RichText", "Select", "Rel_Select", "Multi_Page")) )
		$cgi[if_into_db] = "y";
	else
		$cgi[if_into_db] = "n";
}

if($cgi[if_into_db] == "y")
{
	$this_type = $cgi[real_type];
	if($this_type == "")
	{
		if($cgi[type] == "Int")
			$this_type = "int(12) not null default '0'";
		else if( $cgi[type] =="Char")
			$this_type = "varchar(255) not null default ''";
		else if($cgi[type] == "File")
			$this_type = "varchar(255) not null default ''";
		else if( $cgi[type] == "Temp_Field")
			$this_type = "varchar(255) not null default ''";
		else if($cgi[type] == "Select")
			$this_type =  "varchar(255) not null default ''";
		else if($cgi[type] == "Rel_Select")
			$this_type = "varchar(255) not null default ''";
		else if($cgi[type] =="Text")
			$this_type = "blob";
		else if($cgi[type] == "AutoTypeset_Text")
			$this_type = "mediumblob";
		else if($cgi[type] == "RichText")
			$this_type = "mediumblob";
		else
			$this_type = "blob";
	}
}


if($row_old[if_into_db] != $cgi[if_into_db] )
{
	if( $row_old[if_into_db] == "y")
		$sqlstr = "alter table $t_name drop $row_old[f_name]";
	else
		$sqlstr = "alter table $t_name  add $cgi[f_name]  $this_type";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
}
else if( $cgi[if_into_db] == "y" )
{
	$sqlstr = "alter table $t_name  change  $row_old[f_name] $cgi[f_name]  $this_type";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
}


$sqlstr = "update tempdef set t_id=$t_id, f_name='$cgi[f_name]', cname='$cgi[cname]', type='$cgi[type]', arithmetic='$cgi[arithmetic]', defaultvalue='$cgi[defaultvalue]', validate='$cgi[validate]', showwidth=$cgi[showwidth], showheight=$cgi[showheight], showmargin=$cgi[showmargin], hide='$cgi[hide]', ifnull='$cgi[ifnull]', if_into_db='$cgi[if_into_db]', real_type='$cgi[real_type]' where f_id=$f_id";
$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());


sys_jmp("tempdeflist.php?p_id=$p_id&t_id=$t_id");

?>
