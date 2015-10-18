<?php
require_once("plib/config_inc.php");
require_once("plib/global_func.php");

$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$content = $cgi[content];

?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>内容预览</title>
<style type="text/css">
td,select,input {font-size:12px}
.l17 p{font-size:14px; line-height:164.28%; margin:15px 0;}


.l15 {line-height:150%;}
.f7 {font-size:7px;}
.f24 {
	font-size:24px;
	color:#03005C;
	font-family:"黑体";
	font-size:20px;
	font-weight:normal;
	height:35px;
	line-height:35px;
	overflow:hidden;
	text-align:center;
}
.f24 img{border:1px #000 solid;margin: 10px 0;}
.f24 a,body a:visited{text-decoration:none;}
.f24 a:hover,body a:active{text-decoration:underline;}
.f14{font-size:14.9px;line-height:130%;}
.title12 {font-size:12px;}
.title14 {font-size:14.9px;line-height:130%}
.tail12{font-size:12px;}
A:link {color: #0000ff;}
A:visited {color: #800080;}
A:active,A:hover {color : #ff0000}
A.a01:link,A.a01:visited {text-decoration:none;color: #07015B;}
A.a01:active,A.a01:hover {text-decoration:none;color : #ff0000}
A.a02:link,A.a02:visited {text-decoration:none;color: #0000ff;}
A.a02:active,A.a02:hover {text-decoration:none;color : #ff0000}
</style>
</head>
<body bgcolor=#ffffff topmargin=5 marginheight=5 leftmargin=0 marginwidth=0">
<center>

<table width=750 border=0 cellspacing=0 cellpadding=0>
<tr><td height=34 width=150><br></td></tr>
</table>
<table width=750 border=0 cellspacing=0 cellpadding=0>
<tr><td height=8></td></tr>
<tr><td height=1 bgcolor=#747474></td></tr>
</table>

<table width=750 border=0 cellspacing=0 cellpadding=0>
<tr><td width=620 valign=top align=center rowspan=2 bgcolor=#EDF0F5><br>
	<div id=article>
	<table width=560 border=0 cellspacing=0 cellpadding=0>
	<tr><th class=f24>当前文档标题</th></tr>
	<tr><td height=><hr size=1 bgcolor=#d9d9d9></td></tr>
	<tr><td height=20 align=center></td></tr>
	<tr><td height=15></td></tr>
	<tr><td class=l17>

		
<?php echo $content ?>
	
	</td></tr>
	</table>
	<p></p>
	<table width=560 border=0 cellspacing=0 cellpadding=0>
	<tr><td>
		<table width=565 border=0 cellspacing=0 cellpadding=0>
		<tr><td align=right>【<a href="javascript:window.close()">关闭</a>】</td></tr>
		</table></td></tr>
	</table>

	&nbsp;<BR>
	


</td><td width=1 bgcolor=#747474 rowspan=2></td><td width=129 valign=top></td></tr>
</table>
</center>
<br><br><br>


</body>
</html>

