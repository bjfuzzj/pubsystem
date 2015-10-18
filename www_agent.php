<?php
require_once("plib/global_func.php");

$cgi = getCGI();

	
$browser = $cgi["browse"];
$cgi_uri = $cgi["cgi"];
$target =  $cgi["target"];
$bIEBrowser = strpos($browser, "IE")? 1:0;

if($cgi_url == "") exit("请求错误，不能获得名称为“cgi”的参数！");



$param = sprintf("target=%s", $target);
foreach($cgi as $kk=>$vv)
{
	if($kk != "cgi" && $kk != 'target' && $kk != $vv && $vv != "")
	{
		$param .= sprintf("&%s=%s", $kk, $bIEBrowser == 1? toGB($vv) : $vv);
	}
}

$requestURL = sprintf("%s?%s", $cgi_uri, $param);
	
$content = file_get_contents($requestURL);
if($content == "") exit("Error get $requestURL");

	
$start = strpos($content, "<!--AgentBegin-->");
if ($start) $end = strpos($content, "<!--AgentEnd-->", $start);


printHeadAndJS($target);	
if($start !== false  && $end !== false)
{
	$str = substr($content, $start, $end - $start);
	printf("<html>
<body>
<form name=transfer>
<textarea name=content row=0 col=0>
$str
</textarea>
</form>
<script language=javascript>
Transfer(document.transfer.content.value);
</script>
</body>
</html>
");
}
else
{
	exit($content);
}


function printHeadAndJS($target)
{
	
	print "<script language=javascript>
var target = '$target';

function Transfer(data)
{
	var topWin;
	var currentWin = self;
	var parentWin = currentWin.opener;
	while(parentWin != null)
	{
		currentWin = parentWin;
		parentWin = currentWin.opener;
	}
	topWin = currentWin;
	if(target != null)
	{
		topWin.document.myform.elements[target].value = data;
	}
	self.close();
	topWin.focus();
}
</script>\n";

}

?>
