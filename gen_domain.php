<?php
require_once("plib/db.php");

$sqlstr = sprintf("select  p_id, db_name from proj order by p_id");
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);
while( $row = mysql_fetch_array($res) )
{
	$db_name = $row['db_name'];
	mysql_select_db($db_name, $pub_mysql) or exit(mysql_error($pub_mysql));

	$sqlstr = sprintf("select html_path, html_urlbase, file_path from polymorphic");
	$res1 = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error() . "\n" . $sqlstr);

	while($row1 = mysql_fetch_array($res1, MYSQL_ASSOC) )
	{
		$html_path = $row1['html_path'];
		$html_urlbase = $row1['html_urlbase'];
		$file_path = $row1['file_path'];
		if($html_urlbase != "")
		{

			$pos = strpos($html_urlbase , "http://");
			if($pos !== false)
			{
				$html_urlbase = substr($html_urlbase, $pos + strlen("http://"));
				$pos1 = strpos($str, ":");
				if(!$pos1) $pos1 = strpos($html_urlbase, "/");
				if($pos1) $html_urlbase = substr($html_urlbase, 0, $pos1);
			}

			if($file_path == "") $file_path = sprintf("%s/%s%s%s",  $file_base, $db_name, $html_path{0}=='/'?"":"/", $html_path);
			$domain_list .= sprintf("%s       %s\n",  $html_urlbase,  $file_path);
		}
	}

}

file_put_contents("domain.txt", $domain_list);

?>
