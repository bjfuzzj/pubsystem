<?php	
require_once("plib/config_inc.php");
require_once("plib/db.php");
require_once("plib/global_func.php");
require_once("plib/publish.php");

/*

if($argc < 2)
{
	printf("Usage: %s <username> [read_only]\n", $argv[0]);
	exit;
}

$username = $argv[1];
*/



$p_id = -1;
$t_id = -1;

$sqlstr = "select * from pub_queue order by p_id asc, t_id asc, id asc";
$res = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error($pub_mysql) . "\n" . $sqlstr);
$pub_data = array();
while($row = mysql_fetch_array($res))
{
	$pub_data[] = $row;
	$sqlstr = "delete from pub_queue where id=$row[id]";
	$res1 = mysql_query($sqlstr, $pub_mysql) or exit(mysql_error($pub_mysql) . "\n" . $sqlstr);
}

foreach($pub_data as $row)
{
	if($p_id != $row[p_id] || $t_id != $row[t_id])
	{
		conProjDB($row[p_id], $row[t_id]);
	}

	$id   = $row[id];
	$p_id = $row[p_id];
	$t_id = $row[t_id];
	$d_id = $row[d_id];

	print "\n\n\n\np_id:$p_id, t_id:$t_id, d_id:$d_id\n";

	if($d_id == 0)
	{
		if( AddDoc($p_id, $t_id) < 0) printf("添加文档失败: %s<br>\n", $error_message);
	}
	else
	{
		if( publishOneDoc($p_id, $t_id, $d_id) < 0) printf("发布文档失败: %s<br>\n", $error_message);
	}

}


exit("Finish!\n");

?>
