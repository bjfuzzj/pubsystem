<?php
require_once("plib/head.php");
$nav_str .= " &gt; 新建网站";
$cgi = getCGI();



if($cgi[edit] != "" && $cgi[p_cname] != "")
{
	$sqlstr = sprintf("insert into proj set p_cname='%s', db_name='%s', db_default=1, u_id=$ck_u_id, domain='%s', createdt=now()",
			mysql_escape_string($cgi[p_cname]), $db_name, $cgi[domain]);
	$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	$p_id = mysql_insert_id($pub_mysql);

	$db_name = sprintf("web_%s_%s", $p_id, $ck_u_id); 
	$sqlstr =  "create database $db_name";
	$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());

	$sqlstr =  "update proj set db_name='$db_name' where p_id=$p_id";
	$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	conProjDB($p_id);

	if($cgi[copy_p_id] != "")
	{
		$sqlstr = "select * from proj where p_id=$cgi[copy_p_id]";
		$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
		$row = mysql_fetch_array($res);
		if($row == "") sys_exit("您选择的网站模板不存在.", $sqlstr);

                $copy_db_name = $row[db_name];
		$command = sprintf("cd /tmp; /usr/local/bin/mysqldump --opt -hlocalhost -upublish -ppub54321 -S /tmp/mysql3309.sock %s > pub_copy.sql", $copy_db_name);

                system($command);

		$command = sprintf("cd /tmp; mysql -hlocalhost -upublish -ppub54321 -S /tmp/mysql3309.sock %s < pub_copy.sql", $db_name);
                system($command);


		$sqlstr = "update polymorphic set html_urlbase='$cgi[domain]', file_path='$cgi[file_path]'";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());


		$sqlstr = "select * from polymorphic limit 1";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
		$row = mysql_fetch_array($res);
		if($row == "") sys_exit("样式记录不存在", $sqlstr);

		$old_path = $row[file_path];
		if($old_path == "") $old_path = sprintf("%s/%s/%s", $file_base, $copy_db_name,  $row[html_path]);

		/*
		$cmd = sprintf("mkdir -p $file_base/%s/pc/upload",  $db_name);
		system($cmd);
		*/

		$file_path = $cgi[file_path];
		if($file_path == "") $file_path = "$file_base/$db_name/pc";

		pub_mkdir("$file_path/upload");


		if($is_window)
		{
			xCopy($old_path, $file_path);
		
			@unlink("$file_path/temp_view.php");
			copy("$root_path/temp_view.php", "$file_path/temp_view.php");
	
			@unlink("$file_path/addmess.php");
			copy("$root_path/addmess.php", "$file_path/addmess.php");
	
			xCopy("$root_path/plib", "$file_path/plib");

			xCopy("$root_path/pagelib", "$file_path/pagelib");
	
		}
		else
		{
			$command = sprintf("cp -R %s/* %s/", $old_path, $file_path);
			system($command);
		
			$cmd = "cd $file_path; rm -f  temp_view.php; ln -s  $root_path/temp_view.php temp_view.php";
			system($cmd);
	
			$cmd = "cd $file_path; rm -f addmess.php; ln -s  $root_path/addmess.php addmess.php";
			system($cmd);
	
			$cmd = "cd $file_path;  ln -s  $root_path/plib plib";
			system($cmd);
	
			$cmd = "cd $file_path;  ln -s  $root_path/pagelib pagelib";
			system($cmd);
		}

	}
	else
	{
	        $sqlstr = "create table polymorphic (
	        pm_id int(10) unsigned auto_increment primary key,
	        pm_name varchar(60) not null default '',
	        pm_ename varchar(60)  not null default '',
	        showorder int(10) unsigned not null default 0,
	        html_path varchar(255)  not null default '',
	        html_urlbase varchar(255)  not null default '',
	        file_path varchar(255)  not null default '',
	        file_urlbase varchar(255)  not null default '',
	        rcp_server varchar(128)  not null default '',
	        rcp_user varchar(128)  not null default '',
	        rcp_pwd varchar(128)  not null default '',
	        rcp_html_path varchar(255)  not null default '',
	        rcp_html_urlbase varchar(255)  not null default '',
	        rcp_file_path varchar(255)  not null default '',
	        rcp_file_urlbase varchar(255)  not null default '',
	        rsync_name varchar(255)  not null default ''
	        ) DEFAULT CHARSET=" . DB_CHARSET;
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
		$sqlstr = "insert into polymorphic set pm_name='PC', html_path='/pc', html_urlbase='$cgi[domain]', file_path='$cgi[file_path]'";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	
	        $sqlstr = "create table temp (
	        t_id int(10) unsigned auto_increment primary key,
	        showorder int(10) unsigned not null default 0,
	        grade int(10) unsigned not null default '3',
		ttype int(10) not null default '0',
	        t_name varchar(255)  not null default '',
	        cname varchar(255)  not null default '',
	        ename varchar(255)  not null default '',
	        field_tag varchar(2048)  not null default '',
		createdt datetime not null default '0000-00-00 00:00:00',
		updatedt datetime not null default '0000-00-00 00:00:00',
		defaulturl_1 varchar(255)  not null default '',
		html_1 mediumblob,
	        UNIQUE(cname),
	        UNIQUE(t_name)
	        ) DEFAULT CHARSET=" . DB_CHARSET;
	
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	
	
	        $sqlstr = "create table tempdef(
	        f_id int(10) unsigned auto_increment primary key,
	        t_id int(10) unsigned not null,
	        f_name varchar(255)  not null default '',
	        cname varchar(255)  not null default '',
	        ename varchar(255)  not null default '',
	        type varchar(255)  not null default '',
	        arithmetic blob,
	        defaultvalue varchar(255)  not null default '',
	        validate mediumtext,
	        showorder tinyint(3) unsigned not null default 0,
	        showwidth int(10) unsigned default 0,
	        showheight int(10) unsigned default 0,
	        showmargin int(10) unsigned default 0,
	        hide char(1) default 'n',
	        ifnull char(1) default 'y',
	        if_into_db char(1) default 'y',
	        real_type varchar(255)  not null default '',
	        INDEX(f_id),
	        INDEX(t_id),
	        INDEX(showorder)
	        ) DEFAULT CHARSET=" . DB_CHARSET;
	
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	        $sqlstr = "create table global (
	        id Int(10) unsigned auto_increment primary key,
	        name varchar(255)  not null default '',
	        type varchar(255) default 'text',
	        content blob,
	        url varchar(255) not null default '',
	        UNIQUE(name)
	        ) DEFAULT CHARSET=" . DB_CHARSET;
	
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
		$sqlstr = "CREATE TABLE navigation (
	  id int(10) unsigned NOT NULL auto_increment,
	  name varchar(128) NOT NULL default '',
	  ntype varchar(20) NOT NULL default '',
	  url varchar(255) NOT NULL default '',
	  showorder int(10) NOT NULL default '0',
	  PRIMARY KEY  (id),
	  KEY ntype (ntype)
	  ) DEFAULT CHARSET=" . DB_CHARSET;
	
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	
		$sqlstr = " CREATE TABLE navcode (
	  name varchar(128) NOT NULL default '',
	  content blob,
	  UNIQUE KEY name (name)
	   ) DEFAULT CHARSET=" . DB_CHARSET;
		
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	
	
		$sqlstr = "insert into navcode set name='code1', content='<a href=\"{url}\">{栏目名称}</a>'";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
	
		$sqlstr = "insert into navcode set name='code2', content='<a href=\"{url}\" class=selected>{栏目名称}</a>'";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	
		$sqlstr = "insert into navcode set name='code3', content='{导航条内容}'";
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	

		$tpath = "$file_base/tmp";
		pub_mkdir($tpath);

		$file_path = $cgi[file_path];
		if($file_path == "") $file_path = "$file_base/$db_name/pc";

		pub_mkdir("$file_path/upload");

		if($is_window)
		{
			@unlink("$file_path/temp_view.php");
			copy("$root_path/temp_view.php", "$file_path/temp_view.php");
	
			@unlink("$file_path/addmess.php");
			copy("$root_path/addmess.php", "$file_path/addmess.php");
	
			xCopy("$root_path/plib", "$file_path/plib");

			xCopy("$root_path/pagelib", "$file_path/pagelib");
	
		}
		else
		{

			$cmd = "cd $file_path; rm -f  temp_view.php; ln -s  $root_path/temp_view.php temp_view.php";
			system($cmd);
	
			$cmd = "cd $file_path; rm -f addmess.php; ln -s  $root_path/addmess.php addmess.php";
			system($cmd);
	
			$cmd = "cd $file_path;  ln -s  $root_path/plib plib";
			system($cmd);
	
			$cmd = "cd $file_path;  ln -s  $root_path/pagelib pagelib";
			system($cmd);
		}

	}

	system("cd $root_path; /usr/bin/php gen_domain.php");
	sys_jmp("projlist.php");
}

$sqlstr =  "select * from proj where ptype != 0";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
$list = "";
while($row = mysql_fetch_array($res))
{
	$list .= "<br><input type=radio name=copy_p_id value=\"$row[p_id]\" ><a href=$row[domain] target=_blank>$row[p_cname]</a><br><br>";
}

$domain = sprintf("http://sample%s%s.ghc.net/", $ck_u_id, time());

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo HTML_CHARSET ?>">
<meta http-equiv="Pragma" content="no-cache">
<title>新建网站</title>

<link href="css/main.css" rel="stylesheet" type="text/css" />
<style type="text/csss">
#ghc{
padding:10px;
OVERFLOW-Y:auto;
OVERFLOW:auto;
SCROLLBAR-FACE-COLOR:#ffffff; SCROLLBAR-HIGHLIGHT-COLOR:#ffffff; SCROLLBAR-SHADOW-COLOR:#919192; SCROLLBAR-3DLIGHT-COLOR:#ffffff; SCROLLBAR-ARROW-COLOR:#919192; SCROLLBAR-TRACK-COLOR:#ffffff; SCROLLBAR-DARKSHADOW-COLOR:#ffffff; LETTER-SPACING:1pt;
width:300px;
height:100px;
border:2px solid #ccc;
}
</style>


<script language=javascript>
var submit_flag =0;
function get_radio_value(this_form, ff)
{
	for(i=0; i<this_form.elements.length; i++)
	{
		this_ele = this_form.elements[i];
		if(this_ele.name == ff && this_ele.checked)
		{
			return this_ele.value;
		}
	}

	return "";
}
function checkForm(this_form)
{
	if(submit_flag == 0)
	{

		if(this_form.p_cname.value.length == 0)
		{
			alert("网站名称不能为空");
			this_form.p_cname.focus();
			return false;
		}

		which = get_radio_value(this_form, 'which');

		if( which == 'auto')
		{
			copy_p_id = get_radio_value(this_form, 'copy_p_id');
			if(copy_p_id == "")
			{
				alert('在自动建站方式下，请选择网站模板');
				return false;
			}
		}

		submit_flag = 1;
		return true;
	}
	else
	{
		alert("该页面已经提交，请等待");
		return false;
	}
}

</script>


</head>


<body>

<center>
<table width=100% border=0>
<tr valign=bottom>
<td><?php echo $nav_str ?></td>
<td align=right valign=bottom><?php echo $hello_str; ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#808080></td></tr>
</table>
<br>
<br>


<form action="proj_new.php" name=myform method=post onsubmit="return checkForm(this);">
<input type=hidden name=edit value=1>

<table style="border:1px solid #333; background:#eee; padding:15px;">

<tr height=40>
<td align=right><b>网站名称:</b></td>
<td><input type=text name="p_cname" size=60 value=""></td>
<td><?php echo $notify_notnull ?></td>
</tr>

<tr height=40>
<td align=right height=30><b>网站域名:</b></td>
<td><input type=text name="domain" size=60 value="<?php echo $domain ?>"></td>
<td><span style="color:#f00; font-size:12px;"></td>
</tr>

<tr valign=top>
<td align=right></td>
<td><span style="color:#f00; font-size:12px;">域名要以"http://"开头, 临时域名要以".ghc.net"为后缀</span></td>
<td></td>
</tr>

<tr height=40>
<td align=right height=30><b>网站存放路径:</b></td>
<td><input type=text name="file_path" size=60 value=""></td>
<td><span style="color:#f00; font-size:12px;"></td>
</tr>

<tr valign=top>
<td align=right></td>
<td><span style="color:#f00; font-size:12px;">可以为空，如果为空，则按照默认规则存放在网站默认根路径下</span></td>
<td></td>
</tr>

<script type="text/javascript">
function show_copy_div(show_flag)
{
	show_value = show_flag? "block":"none";
	document.getElementById("copy_header").style.display = show_value;
	document.getElementById("copy_body").style.display = show_value;
}

</script>

<tr height=40>
<td align=right height=30><b>建站方式:</b></td>
<td align=center>
<input type=radio name="which" value="manu" checked=true onclick="show_copy_div(false)">手动建站
&nbsp;&nbsp;&nbsp;&nbsp;
<input type=radio name="which" value="auto"  onclick="show_copy_div(true)">自动建站
</td>
<td></td>
</tr>


<tr>
<td></td>
<td  colspan=2>
<div id=copy_header style="display:none"><b>网站模板选择:</b></div>
<div id=copy_body style="border:0px solid #000; height:300px; overflow:auto; display:none;">
<?php echo $list ?>
</div>

</td>
</tr>

<tr> <td colspan=3 align=center>
<input type=reset value="重  写">
&nbsp;&nbsp;&nbsp;
<input type=submit value="提  交">
</td>
</tr>
</table>

</form>


</body>
</html>
