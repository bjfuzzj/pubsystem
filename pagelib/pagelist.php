<?php
require_once("db.php");
require_once("global_func.php");
require_once("page.php");

function get_phplist_page($php_sqlstr, $php_limit_length)
{
	global $g_mysql;

	$notify_buf = "";
	$page_desc = "";
	$limit_begin = 0;
	
	$cgi = getCGI();
	
	$page = $cgi[page];
	$ppage = $cgi[ppage];
	if($page == "") $page = 0;
	if($ppage == "") $ppage = 0;
	
	$limit_begin = $php_limit_length * $page;
	
	$php_sqlstr = str_replace(" limit ", " limit $limit_begin,", $php_sqlstr);
	$php_sqlstr = str_replace("select ", "select SQL_CALC_FOUND_ROWS", $php_sqlstr);
	$php_res = mysql_query($php_sqlstr, $g_mysql) or exit(mysql_error() . ":" . $php_sqlstr);
	
	$sqlstr = "select FOUND_ROWS() as ct";
	$res_ct = mysql_query($sqlstr, $g_mysql) or exit(mysql_error() . "\n" . $sqlstr);
	$row = mysql_fetch_array($res_ct, MYSQL_ASSOC);
	$ct = $row[ct];
	if($ct == "") $ct = 0;
	$total_rec = $ct;
	
	$page = new PAGE($ct, $ct, 0, $php_limit_length);
	$page_list = $page->pagelist;
	$page_notify = $page->notify;

	$ret = array('page_list'=>$page_list, 'page_notify'=>$page_notify, 'php_res'=>$php_res);
	return $ret;
}

function get_phplist_nopage($php_sqlstr) 
{
	global $g_mysql;
	$php_res = mysql_query($php_sqlstr, $g_mysql) or exit(mysql_error() . ":" . $php_sqlstr);
	$ret[php_res] = $php_res;
	return $ret;
}


?>
