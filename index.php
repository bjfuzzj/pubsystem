<?php
require_once("plib/config_inc.php");

$ip = $_SERVER["REMOTE_ADDR"];
$pos = strpos($ip, "10.");
//if( false && $pos !== false && $pos === 0)
if(false)
{

	$html_charset = HTML_CHARSET;

	print <<<GHC_END
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=$html_charset">
<title>网站发布管理系统</title>
</head>
<body>
<center><br><br>
<form onsubmit="return checkForm_login(this);" method="post" name="myform" action="login.php">

<table style="border: 1px solid rgb(51, 51, 51); padding: 15px; background: rgb(238, 238, 238) none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;">

<tr height="40">
<td align="right"><b>登录名:</b></td>
<td><input type=text name=admin size=40 value="editor"></td>
<td></td>
</tr>

<tr height="40">
<td height="30" align="right"><b>密　码:</b></td>
<td><input type=password name=pwd size=40 value="111111"></td>
<td/>
</tr>

<tr> <td align="center" colspan="3">
<input type=submit value="登&nbsp;&nbsp;录">
</td>
</tr>
</table>
</form>
</body>
</html>
GHC_END;
exit;

}



?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo HTML_CHARSET; ?>">
<title>网站发布管理系统</title>
<style>
td.ttd {font-size:12px;line-height:150%}
body,p,td,input,select {font-size:13px;}
</style>

<script language=javascript>
function checkForm_login(thisform)
{
	if(thisform.admin.value == "")
	{
		alert('登录名不能为空');
		thisform.admin.focus();
		return false;
	}

	if(thisform.pwd.value == "")
	{
		alert('登录密码不能为空');
		thisform.pwd.focus();
		return false;
	}

	if(thisform.g_code.value == "")
	{
		alert('code不能为空');
		thisform.g_code.focus();
		return false;
	}
	return true;
}

function get_url(url)
{
        var xmlhttp;
        var flag = 0;
        try{
                xmlhttp = new XMLHttpRequest();

        }catch(e){
                try{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }catch(e) { }
        }

        var tm = new Date();
        cgi_prog = url + "&tm=" + tm.getTime();
        //alert(cgi_prog);
        xmlhttp.open("get", cgi_prog, false);
        xmlhttp.send(null);
        return  xmlhttp.responseText;
}

function checkForm_register(thisform)
{
	if(thisform.admin.value == "")
	{
		alert('登录名不能为空');
		thisform.admin.focus();
		return false;
	}


	ret = get_url("checklogin.php?admin=" + thisform.admin.value);
	if(ret != "OK")
	{
		alert(ret);
		return false;
	}
	

	if(thisform.pwd.value == "")
	{
		alert('登录密码不能为空');
		thisform.pwd.focus();
		return false;
	}

	if(thisform.pwd.value != thisform.pwd1.value) 
	{
		alert('登录密码和确认密码不一致');
		thisform.pwd1.focus();
		return false;
	}

	if(thisform.linkman.value == "")
	{
		alert('联系人不能为空');
		thisform.linkman.focus();
		return false;
	}

	if(thisform.phone.value == "")
	{
		alert('联系电话不能为空');
		thisform.phone.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body bgcolor=#fefefe>
<center>
<br><br>

<h1>网站发布管理系统</h1>
<br><br>
<table width=80%>
<tr valign=top>

<td align=left width=50%>

<font color=green>如果已有帐号，请在此登录 </font>
<form onsubmit="return checkForm_login(this);" method="post" name="myform" action="login.php">

<table style="border: 1px solid rgb(51, 51, 51); padding: 15px; background: rgb(238, 238, 238) none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;">

<tr height="40">
<td align="right"><b>登录名:</b></td>
<td><input type=text name=admin size=40></td>
<td></td>
</tr>

<tr height="40">
<td height="30" align="right"><b>密　码:</b></td>
<td><input type=password name=pwd size=40></td>
<td/>
</tr>

<tr height="40">
<td height="30" align="right"><b>Code :</b></td>
<td><input type=text name=g_code size=40></td>
<td/>
</tr>

<tr> <td align="center" colspan="3">
<input type=submit value="登&nbsp;&nbsp;录">
</td>
</tr>
</table>
</form>

</td>


<td width=1 bgcolor=#cccccc></td>


<!--
<td align=right>


<font color=blue>
如果没有帐号，请在此注册
</font>

<form onsubmit="return checkForm_register(this);" method="post" name="myform" action="register.php">

<table style="border: 1px solid rgb(51, 51, 51); padding: 15px; background: rgb(238, 238, 238) none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;">

<tr height="40">
<td align="right"><b>登录名:</b></td>
<td><input type=text name=admin size=40></td>
<td><span style="color: rgb(255, 0, 0); font-size: 12px;">必填</span></td>
</tr>

<tr height="40">
<td height="30" align="right"><b>密　码:</b></td>
<td><input type=password name=pwd size=40></td>
<td/>
</tr>

<tr height="40">
<td height="30" align="right"><b>确认密码:</b></td>
<td><input type=password name=pwd1 size=40></td>
<td/>
</tr>

<tr height="40">
<td height="30" align="right"><b>联系人:</b></td>
<td><input  name=linkman size=40></td>
<td/>
</tr>

<tr height="40">
<td height="30" align="right"><b>联系电话:</b></td>
<td><input name=phone size=40></td>
<td/>
</tr>


<tr> <td align="center" colspan="3">
<input type=submit value="注&nbsp;&nbsp;&nbsp;&nbsp;册">
</td>
</tr>
</table>
</form>

</td>
-->
</tr>

</table>


<hr width=80%>

<table width=80%>
<tr><td>

<h3>1．系统简介：</h3>
<p>
该系统是一搭建网站的系统，只要你懂得网页的html代码, 就可以利用该系统随心所欲地搭建出个人网站，企业网站，商城网站，门户网站等各种类型的网站，如果你懂得编程语言的基础知识，该系统提供的二次开发功能，可以让你在系统上为你的网站编写程序逻辑，制作更复杂的综合型网站，该系统是一个入门门槛低，易学易用，灵活性，扩展性强，功能强大的网站发布系统。
</p>
<h3>2．基本术语</h3>
<p>
<b>网站, 文档, 模板, 模板域, 发布</b>
</p>
<p>
网站就是我们平常所说的网站， 当我们浏览网站时， 打开的是该网站的一个个页面，该页面就叫做文档，我们制作网站时，就是要生成网站的页面，即文档。
</p>
<p>
网站下面有多个模板，而模板下面可以有多个文档, 模板域隶属于模板，对网站开发人员来说，模板就相当一个数据表， 而模板域就相当于数据表的字段， 当我们在系统中创建一模板时，就会给该模板创建一数据表， 当为一模板创建一模板域时， 模板对应的数据表就会为该模板域增加一个数据表字段， 模板域有两个基本属性，一方面可在在添加文档，编辑文档的页面中定制数据输入项，例如输入框，编辑框，下拉框等， 另一方面可以当作模板域变量嵌在模板的HTML代码中，在发布生成页面文档时被相应的数据替换掉。 而我们生成的文档就是该数据表中一个个记录，所以当我们定制好一个模板时， 就可以利用该模板，生成多个文档。
</p>

<p>发布就是根据数据库中保存的数据记录信息，生成静态页面的过程。</p>


<p>
文档是我们最终所要的结果。而模板，模板域是要生成文档时要经历的的中间步骤.
</p>


<h3>３．如何把模版域和文档关联起来</h3>
<p>
当我们创建一模板域时，在添加文档，或修改文档时， 就会有对应该模板域的输入框，当在输入框输入数据，生成文档时，该输入数据就存到数据库中， 但如何在文档页面上体现该输入数据呢，这就要在编辑模板中操作了， 在编辑模板中，有一叫做” HTML代码”大输入框，在此处，模板域对于模板来说就相当于变量，把左边“模板域列表”中列出的模板域，拷贝到 “HTML代码“大输入框相应的地方，保存修改后，再发布文档，生成的文档页面就会有相应的数据显示。
</p>

<h3>4．什么时候需要发布文档</h3>
<p>
发布文档就是要把存在数据库的数据记录根据模板生成静态页面，当我们添加，修改文档时系统会自动调用发布文档动作，不需要手工触动操作， 而当我们修改完模板时，相应的静态页面不会更新，这时需要在文档列表中选中相应的文档，点击发布按钮，重新发布文档。
</p>

<h3>5．几个变量</h3>
<p>
在编辑模板时，在模板域列表中会有下面几个几个变量<br>
${createdatetime} &nbsp;&nbsp;&nbsp;&nbsp;文档创建时间<br>
${createdate}  &nbsp;&nbsp;&nbsp;&nbsp;          文档创建时间日期<br>
${projid}  &nbsp;&nbsp;&nbsp;&nbsp;             网站编号<br>
${tempid}   &nbsp;&nbsp;&nbsp;&nbsp;           模板编号<br>
${docid}    &nbsp;&nbsp;&nbsp;&nbsp;           文档编号<br> 
</p>

<h3>6．如何指定模板的 “默认URL”</h3>
<p>
一个模板可能对应多个页面，每个页面有自己的路径， 在编辑模板时，“默认URL” 就是要指定生成页面的路径，可以是实际的页面路径，也可以是带变量的路径， 带变量的路径指定了文档页面的路径的生成规则，“默认URL”要以 “/”开头，例如：
</p>
/index.html    只对应一个页面<br>
/news/${docid}.html   对应多个页面，生成页面的路径类似下面<br><br>

/news/1.html<br>
/news/2.html<br>
/news/3.html<br>
/news/4.html<br>


</td></tr>
</table>

</body>
</html>

