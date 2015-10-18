<?php
require_once("plib/head.php");

$cgi = getCGI();
gsql_esc($cgi);
$p_id = $cgi[p_id];
$ids = $cgi[ids];

if($p_id == "");
conProjDB($p_id);

for($i=1; $i<=4; $i++)
{
	$ff = "code$i";
	if($cgi[$ff] != "")
	{

		$sqlstr = sprintf("replace into navcode set name='$ff', content='%s'",  $cgi[$ff]);
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	}
}

gen_nav();
sys_jmp("navlist.php?p_id=$p_id");
?>
