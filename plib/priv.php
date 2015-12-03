<?php
function check_priv($p_id, $t_id, $f_id)
{
	global $ck_u_type, $ck_u_name, $ck_u_login, $ck_u_id, $ck_u_priv, $ck_u_allproj;
	global $error_message;

	if ( $ck_u_type == 0 || $ck_u_type == 100 || $ck_u_allproj != 0 )  return 0;
	if($p_id == 0) return 0;

	$sp = explode(",", $ck_u_priv);
	$flag = 0;
	foreach($sp as $vv)
	{
		if( $vv  == $p_id )
		{
			$flag = 1;
			break;
		}
	}

	if( $flag == 0)
	{
		$error_message = sprintf("%s[%s:%s] has no privilege on project:%d\n\n[%s]", $ck_u_name, $ck_u_login, $ck_u_id, $p_id, $ck_u_priv);
		return -1;
	}

	$priv_temp_list = "1";
	return 0;
}

?>
