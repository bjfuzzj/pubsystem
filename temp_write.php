<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();

$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$tdata = $cgi[tdata];
$filename = sprintf("%s/tmp/temp_%s_%s.html", $file_base, $p_id, $t_id);
if (!$fp = fopen($filename, 'w')) exit("不能打开文件 $filename");
if (fwrite($fp, $tdata) === false) exit("不能写入到文件 $filename");
fclose($fp);

if($html_charset == 'gb2312')
{
	$tdata = `iconv -f utf-8 -t gbk $filename`;
}

if (!$fp = fopen($filename, 'w')) exit("不能打开文件 $filename");
if (fwrite($fp, $tdata) === false) exit("不能写入到文件 $filename");
fclose($fp);

$html_code_body1 = "<html>\n<body>\n";
$html_code_body2 = "\n</body>\n</html>\n";

if( preg_match('/(.*?<body>).*/ism',  $tdata, $matches) )
{
	$html_code_body1 = $matches[1];
}
else if( preg_match('/(.*?<body\s+.*?>).*/ism',  $tdata, $matches) )
{
	$html_code_body1 = $matches[1];
}

if( preg_match('/.*?(<\/body>.*)/ism',  $tdata, $matches) )
{
	$html_code_body2 = $matches[1];
}

print "$html_code_body1\n========================MARK_OF_GHC=============================\n$html_code_body2";
?>
