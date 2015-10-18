<?php
$g_mysql = mysql_pconnect(DB_HOST, DB_USER, DB_PASS) or sys_exit("无法连接发布系统数据库", mysql_error());
mysql_select_db($db_name, $g_mysql) or exit("can't select database");
$res = mysql_query("set names " . DB_CHARSET, $g_mysql) or sys_exit( mysql_error());
?>
