<?php
/**
* update password + slat
*/

//set_magic_quotes_runtime(0);
ini_set("magic_quotes_runtime",0);
require_once("./../plib/config_inc.php");
require_once("./../plib/global_func.php");
// require_once("pub_cookie.php");
require_once("./../plib/db.php");

$today = date('Y-m-d h:i:s');

$sqlstr ="select id, name, passwd, salt from user where salt is null or salt = ''";
$res = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
while ($row_user = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$salt = getSalt();
	$password = md5($row_user['passwd'].$salt);
	$sqlstr = "update user set passwd='$password', salt='$salt' where id = {$row_user['id']};";
	// dump($sqlstr);
	$result = mysql_query($sqlstr, $pub_mysql) or sys_exit("系统忙， 请稍候再试。", $sqlstr . ":\n" . mysql_error());
	echo $result ? 'success' : 'failed';
	echo "\r\n";
}