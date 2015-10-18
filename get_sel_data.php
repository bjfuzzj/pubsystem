<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");

$cgi = getCGI();

$p_id= $cgi["p_id"];
$pv =  $cgi["pv"];
$pf =  $cgi["pf"];
$sql = $cgi["sqlstr"];

if($p_id == "") exit("p_id is null.\n");
if($sql == "") exit("sqlstr is null.\n");

if($pv != "" && $pf != "")
{
	if(strpos($sql, "where"))
	{
		$sqlstr = str_replace("<PID>", $pv, $sql);
	}
	else
	{
		$sqlstr = sprintf("%s  where %s = '%s'", $sql, $pf, $pv);
	}
}
else
{
	$sqlstr = $sql;
}

conProjDB($p_id);


$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$field_ct = mysql_num_fields($res);

$buff = "";
while($row = mysql_fetch_array($res))
{
	if($buff == "")
	{
		$buff .= sprintf("{value:'%s', text:'%s'}",  $row[0], $field_ct > 1?$row[1]:$row[0]);
	}
	else
	{
		$buff .= sprintf(", {value:'%s', text:'%s'}", $row[0], $field_ct > 1?$row[1]:$row[0]);
	}
}

print "sel_data = [$buff];\n";

?>
