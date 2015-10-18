<?php
require_once("plib/head.php");
require_once("plib/priv.php");


$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$simple = $cgi['simple'];

if($p_id == "" || $t_id == "" ) sys_exit("参数错误");

conProjDB($p_id, $t_id);

if($ck_u_type > 2 ) sys_exit("对不起，你没有操作权限",   "");
if( check_priv($p_id, $t_id, 0) < 0 ) sys_exit("对不起，你没有操作权限",   $error_message);

$t_name  = $temp_data[$t_id][t_name];
$t_cname = $temp_data[$t_id][cname];

$buff = "";

$sqlstr = sprintf("select f_id, f_name, cname, type, real_type, showorder, showwidth, showheight,  showmargin, ifnull, hide, if_into_db, arithmetic, defaultvalue, validate from tempdef where t_id=%s order by showorder, f_id", $t_id);
$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

for($i=1; $row = mysql_fetch_array($res, MYSQL_ASSOC); $i++)
{
	$f_id         = $row[f_id];
	$f_name       = $row[f_name];
	$cname        = $row[cname];
	$type         = $row[type];
	$real_type    = $row[real_type];
	$showorder    = $row[showorder];
	$showwidth    = $row[showwidth];
	$showheight   = $row[showheight];
	$showmargin   = $row[showmargin];
	$ifnull       = $row[ifnull];
	$hide         = $row[hide];
	$if_into_db   = $row[if_into_db];

	$arithmetic   = $row[arithmetic];
	$defaultvalue = $row[defaultvalue];
	$validate     = $row[validate];


		
	$buff .= <<<GHC_PRINT_END
\n\n\n#==========================================$i:$cname==========================================================
f_id:$f_id
cname:$cname
f_name:$f_name
type:$type
real_type:$real_type\n
GHC_PRINT_END;


	if($arithmetic != "")
	{

	$buff .= <<<GHC_PRINT_END

arithmetic:
-------------------------------------------------------------------------------------------
$arithmetic
-------------------------------------------------------------------------------------------
\n
GHC_PRINT_END;
	}

	if($validate != "")
	{

	$buff .= <<<GHC_PRINT_END

validate:
-------------------------------------------------------------------------------------------
$validate
-------------------------------------------------------------------------------------------
\n
GHC_PRINT_END;
	}




	if($simple != "" ) continue;

	$buff .= <<<GHC_PRINT_END
defaultvalue:$defaultvalue
showorder:$showorder
showwidth:$showwidth
showheight:$showheight
showmargin:$showmargin
ifnull:$ifnull
hide:$hide
if_into_db:$if_into_db\n
GHC_PRINT_END;


}


$buff = str_replace("\r\n", "\n", $buff);
$buff = str_replace("\n", "\r\n", $buff);

$filename = sprintf("%s(%s_%s).txt", $t_cname,  $p_id, $t_id);

//header("Content-type: application/x-file");
header('Content-Type: application/octet-stream');
header("content-length:" . strlen($buff));
header("content-disposition: attachment;filename=\"$filename\"");

print $buff;


?>
