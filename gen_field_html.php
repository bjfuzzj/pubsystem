<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");

conProjDB($p_id, $t_id);

	
$i = 0;


print "<table id=table_field border=0>\n";
foreach($tempdef_data as $kk=>$row)
{
	if($row[t_id] != $t_id) continue;
	if($row[if_into_db] != "y") continue;

	if($cgi[type] == "Rel_Select")
	{
		$type_str = "checkbox";
	}
	else
	{
		$type_str = "checkbox";
	}

	if($i == 0)
		print "<tr>\n";
	else if($i % 6 == 0)
		print "</tr>\n<tr>\n"; 
	print <<< END_OF_GHC
<td align=left style="font-size:12px;">&nbsp;&nbsp;<input type=$type_str name=f_ids value="$row[f_id]" onclick="gen_code_html(this.form)">$row[cname]</td>
END_OF_GHC;
	$i++;
}
print "</table>";
?>

