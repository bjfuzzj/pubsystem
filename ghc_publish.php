<?php	
require_once("plib/config_inc.php");
require_once("plib/db.php");
require_once("plib/global_func.php");
require_once("plib/publish.php");

if($argc < 3)
{
	exit("$argv[0] <p_id> <t_id>\n");
}

$p_id = $argv[1];
$t_id = $argv[2];

conProjDB($p_id, $t_id);

for($i=19348; $i<24145; $i++)
{
	$d_id = $i;
	if( publishOneDoc($p_id, $t_id, $d_id) < 0)
	{
		printf("发布文档失败: %s<br>\n", $error_message);
	}
}

?>
