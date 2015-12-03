<?php

$pub_cookie_name = "pub_coookie";
$ck_u_id = "";
$ck_u_login = "";
$ck_u_name = "";
$ck_u_type = "";
$ck_u_priv = "";
$ck_u_allproj = "";


if(strpos($_SERVER['PHP_SELF'], "/login.php") === false)
{
	$ck_str = $_COOKIE["$pub_cookie_name"];
	$ck_str = base64_decode($ck_str);
	$row_user  = unserialize($ck_str);

	if(!is_array($row_user))
	{
		exit("闲置时间太长，请重新<a href=index.php>登陆</a>。");
	}


	$ck_u_id=$row_user[id];
	$ck_u_login=$row_user[login];
	$ck_u_name=$row_user[name];
	$ck_u_type=$row_user[type];
	$ck_u_priv=$row_user[priv];
	$ck_u_allproj = $row_user[allproj];
	if( $ck_u_id == "" || $ck_u_login == "" || $ck_u_type == "" )
	{
		exit("<!-- [$ck_str]  [$code_str] -->  闲置时间太长，请重新<a href=index.php>登陆</a>。");
	}
	reset_cookie();
}

function reset_cookie($row_user="")
{
	global $pub_cookie_name;

	if($row_user != "")
	{
		$ck_data = serialize($row_user);
		$ck_data = base64_encode($ck_data);
	}
	else
	{
		$ck_data = $_COOKIE["$pub_cookie_name"];
	}
        setcookie($pub_cookie_name, trim($ck_data), time() + 3600, "/");
}

?>
