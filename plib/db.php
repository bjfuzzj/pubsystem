<?php
require_once("config_inc.php");

$pub_mysql = mysql_pconnect(DB_HOST, DB_USER, DB_PASS) or sys_exit("无法连接发布系统数据库", mysql_error());
mysql_select_db(DB_NAME, $pub_mysql) or exit("无法选择发布系统数据库\n" . mysql_error());
$res = mysql_query("set names " . DB_CHARSET, $pub_mysql) or sys_exit( mysql_error());

function conProjDB($p_id, $get_proj_data_flag=false)
{
	global $pub_mysql, $proj_mysql;
	global $ck_u_id;
	global $file_base;

	global $proj_data, $poly_data, $temp_data, $tempdef_data, $global_data;
	global $db_name;


	$proj_data = "";
	$poly_data = array();
	$temp_data = array();
	$tempdef_data = array();
	$global_data = array();
	$db_name    = "";
	
	$sqlstr = "select * from proj where p_id=$p_id";
	$res = mysql_query($sqlstr, $pub_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	$row = mysql_fetch_array($res);
	if($row == "") sys_exit("网站不存在");
	if($row[u_id] != $ck_u_id && $ck_u_type > 100)
	{

		sys_exit("对不起，您对该网站没有操作权限");
	}
	$proj_data = $row;

	$db_name = $row[db_name];
	
	if($row['db_default'] == 1)
	{
		$proj_mysql = mysql_connect(DB_HOST, DB_USER, DB_PASS) or sys_exit("无法连接网站数据库", mysql_error());
	}
	else
	{
		$db_port = $row[db_port];
		$db_sock = $row[db_sock];
		$db_user = $row[db_user];
		$db_pwd = $row[db_pwd];
		$db_server = $row[db_server];
		$proj_mysql = mysql_connect("$db_server:$db_sock", $db_user, $db_pwd) or sys_exit("无法连接网站数据库", mysql_error());
	}
	mysql_select_db($db_name, $proj_mysql) or sys_exit("无法选择网站数据库", mysql_error());
	$res = mysql_query("set names " . DB_CHARSET, $proj_mysql) or sys_exit( mysql_error());

	if(strpos($_SERVER[PHP_SELF], '/proj_new.php') !== false) return;


	$sqlstr = "select * from polymorphic";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	while($row = mysql_fetch_array($res))
	{

		$pm_id = $row[pm_id];

		if($row[file_path] == "")
		{
			$row[file_path] = sprintf("%s/%s/%s", $file_base, $db_name, $row[html_path]);
			$row[file_path] = str_replace("//", "/", $row[file_path]);
		}
		$poly_data[$pm_id] = $row;
	}

	foreach($poly_data as $pm_id=>$item)
	{
		$sqlstr = sprintf("select defaulturl_%d, html_%d, t_name, cname from temp where t_id=%d", $pm_id, $pm_id, $get_proj_data_flag);
		$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
		$row = mysql_fetch_array($res);
		if($row == "")
		{
                        $error_message = sprintf("No sush temp;[%d]\n%s", $pm_id, $sqlstr);
                        return -1;
		}
                $poly_data[$pm_id][defaulturl] = $row[0];
                $poly_data[$pm_id][html] = $row[1];
                $poly_data[$pm_id][t_name] = $row[2];
                $poly_data[$pm_id][t_cname] = $row[3];
	}

	$sqlstr = "select * from temp";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	while($row = mysql_fetch_array($res))
	{
		$t_id = $row[t_id];
		$temp_data[$t_id] = $row;
	}



	if(!$get_proj_data_flag) return;

	$sqlstr = "select * from tempdef where t_id=$get_proj_data_flag order by showorder asc, f_id asc";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	while($row = mysql_fetch_array($res))
	{
		$f_id = $row[f_id];
		$tempdef_data[$f_id] = $row;
	}

	$sqlstr = "select * from global order by id  asc";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	while($row = mysql_fetch_array($res))
	{
		$id = $row[id];
		$global_data[$id] = $row;
	}

	return 0;
}


function getTF($key, $which)
{
	global $tempdef_data;

	foreach($tempdef_data as $row)
        {
                if($which == "cname")
                {
                        if($row[cname] ==  $key) return  $row;
                }
                else if($which == "f_name")
                {
                        if($row[f_name] == $key) return $row;
                }
                else if( $which ==  "f_id")
                {
                        if($row[f_id] == $key) return $row;
                }
                else
                {
                        $error_message = "which not match!";
                        return "";
                }
        }

        $error_message = "$which:$key not exist!";
        return "";
}



function gen_nav()
{
	global $proj_mysql, $file_base, $db_name, $poly_data;

	$code1 = $code2 = $code3 = $code4 = "";
	$sqlstr = "select * from  navigation order by showorder asc, id asc";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());

	$item_count = mysql_num_rows($res);
	while($row = mysql_fetch_array($res))
	{
		$list .= sprintf("nav_html += nav_item(\"%s\", \"%s\");\n", $row[name], $row[url]);
	}

	$sqlstr = "select * from navcode";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	while($row = mysql_fetch_array($res))
	{
		if($row[name] == 'code1')
		{
			$code1 = $row[content];
		}
		else if($row[name] == 'code2')
		{
			$code2 = $row[content];
		}
		else if($row[name] == 'code3')
		{
			$code3 = $row[content];
		}
		else if($row[name] == 'code4')
		{
			$code4 = $row[content];
		}
	}

	
	$code1 = addslashes($code1);
	$code2 = addslashes($code2);
	$code3 = addslashes($code3);
	$code4 = addslashes($code4);

	$code1 = preg_replace(array('/\{url\}/', '/{栏目名称}/', '/\r\n/'), array('" + url + "', '" + cname + "', "\\n\\\n"), $code1);
	$code2 = preg_replace(array('/\{url\}/', '/{栏目名称}/', '/\r\n/'), array('" + url + "', '" + cname + "', "\\n\\\n"), $code2);
	$code3 = preg_replace(array('/{导航条内容}/', '/\r\n/'), array('" + nav_html + "',  "\\n\\\n"), $code3);

	$data = <<<END_NAV_OF_GHC
var item_ii = 0;
function nav_item(cname, url)
{
	var doc_url = window.location.href;
	var v_html = "";
	item_ii++;
	if( doc_url.indexOf(url) != -1 )
	{
		v_html = "$code2";
	}
	else
	{
		v_html = "$code1";
	}
	if(item_ii < $item_count)
	{
		v_html += "$code4";
	}
	return v_html;	
}

nav_html = "";

$list

all_html = "$code3";
document.writeln(all_html);

END_NAV_OF_GHC;

	$sqlstr = "select * from polymorphic limit 1 ";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	$row = mysql_fetch_array($res);

	$filename = sprintf("%s/nav.js", $row[file_path]);
	$fp = fopen($filename, "w");
	if(!$fp) sys_exit("无法打开文件 $filename.", "");
	fwrite($fp, $data);
	fclose($fp);
}

function gen_global()
{
	global $proj_mysql, $file_base, $db_name, $poly_data;

	$sqlstr = "select * from global";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());

	$list = "";
	while($row = mysql_fetch_array($res))
	{
		if($row[type] == 'text') continue;
		$content = addslashes($row[content]);

		if($list == "")
		{
			$list .= sprintf("'%s':'%s'", $row[name], $content);
		}
		else
		{
			$list .= sprintf(",\n'%s':'%s'", $row[name], $content);
		}
	}

	
	$data = <<<END_NAV_OF_GHC
var global_data = {
$list
};

function pub_global_vars(global_var_name)
{
	document.write(global_data[global_var_name]);
	return true;
}
END_NAV_OF_GHC;

	$sqlstr = "select * from polymorphic limit 1 ";
	$res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
	$row = mysql_fetch_array($res);

	$filename = sprintf("%s/pub_global.js", $row[file_path]);
	$fp = fopen($filename, "w");
	if(!$fp) sys_exit("无法打开文件 $filename.", "");
	fwrite($fp, $data);
	fclose($fp);
}

?>
