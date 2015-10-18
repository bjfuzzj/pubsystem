<?php
require_once("plib/db.php");
require_once("plib/global_func.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");
$filename = sprintf("%s/tmp/temp_%s_%s.html", $file_base, $p_id, $t_id);
$html = @file_get_contents($filename);

conProjDB($p_id, $t_id);

if($html == "")
{
	$sqlstr = "select * from temp where t_id=$t_id";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	$row = mysql_fetch_array($res);
	if($row == "") sys_exit("模板不存在", $sqlstr);
	$html = $row[html_1];
}

if($cgi[view] == "")
{
	$html = preg_replace(array("/<body\s*(.*)>/i"), array("<body $1 contentEditable=true>"), $html);
}

?>


<script type="text/javascript">
document.old_write = document.write;
document.old_writeln = document.writeln;

document.write = function()
{
	var ghc_v_html = "<ghc_write_mark>";
	for (var i = 0; i < arguments.length; i++)
	{
		argument = arguments[i];
		if (typeof argument == 'string')
		{
			ghc_v_html += argument;
		}
	}
	ghc_v_html += "</ghc_write_mark>";
	document.old_write(ghc_v_html);
	return false;
}

document.writeln = function()
{
	var ghc_v_html = "<ghc_write_mark>";
	for (var i = 0; i < arguments.length; i++)
	{
		argument = arguments[i];
		if (typeof argument == 'string')
		{
			ghc_v_html += argument;
		}
	}
	ghc_v_html += "</ghc_write_mark>";
	document.old_writeln(ghc_v_html);
	return false;
}

</script>

<?php
printf("<base href=%s>\n", $poly_data[1][html_urlbase]);
print "$html";
?>
