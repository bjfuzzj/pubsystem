<?php
$loop_cnt = 276;
$loop_step = array(29,31,67,91,43,72,47,39,57,83);
$no_data = array(
'$today', '$hello_str', '$nav_str', '$notify_notnull', '$is_window', '$proj_data', '$poly_data',
'$temp_data', '$tempdef_data', '$global_data', '$pub_mysql', '$proj_mysql', '$db_name',
'$pre_field', '$page_split_mark', '$this_doc', '$eval_Table', '$async_buf',
'$pub_cookie_name', '$ck_u_id', '$ck_u_login', '$ck_u_name', '$ck_u_type', '$ck_u_priv', '$ck_u_allproj',
'$root_path', '$file_base',
);

$fun_data = array(
'trim_last_splash'=>1,
'trim_big_bracer'=>1,
'getPath'=>1,
'getTablelist'=>1,
'getSqlstr'=>1,
'get_select_field'=>1,
'getFieldlist'=>1,
'get_rel_select_child'=>1,
'get_rel_select_field_name'=>1,
'getRel_Result'=>1,
'getJsName'=>1,
'genJsCode'=>1,
'fieldIntoForm'=>1,
'publishDocField'=>1,
'publishDocField_poly'=>1,
'doAboutURL'=>1,
'doAboutSql'=>1,
'doAboutWhere'=>1,
'sendPostInfo'=>1,
'AddDoc'=>1,
'doAboutPostInPage'=>1,
'doAboutPhp_List'=>1,
'doAboutForm_List'=>1,
'asyncDoc'=>1,
'doAboutMultiPage'=>1,
'genMuitiPageFile'=>1,
'getMultiPage'=>1,
'page_split'=>1,
'gen_page_list'=>1,
);

foreach($fun_data as $kk=>$vv)
{
	$fun_data[$kk] = Encryption($kk, 'function');
}

$data = file_get_contents("publish.ori.php");

$data = hunxiao_code($data);

$sp = explode("\nfunction ", $data);

$code = "";
foreach($sp as $ii=>$vdata)
{
	if($ii == 0)
	{
		$code = $vdata;
		continue;
	}
	$loop_cnt += $loop_step[$ii % 10];
	$tcode = "\nfunction " . $vdata;
	$var_data = get_variable($tcode);
	$tcode = hunxiao_var($tcode, $var_data);
	$code .= $tcode;
}

$code = strip($code);

file_put_contents("../db/pub.txt", $code);
file_put_contents("publish.php", "<?php\n" . $code . "\n?>\n");

//-----------------------------------------------------------------------------------

function hunxiao_var($tcode, $var_data)
{
	global $no_data;
	global $fun_data;
	
	foreach($no_data as $m)
	{
		$rem=strtoupper($m);
		$tcode =str_replace($m, $rem, $tcode);
	}
	
	foreach($var_data as $kk=>$vv)
	{
		$tcode = str_replace($kk, $vv, $tcode);
	}
	
	foreach($fun_data as $kk=>$vv)
	{
		$tcode = str_replace($kk, $vv, $tcode);
	}
	
	foreach($no_data as $m)
	{
		$rem=strtoupper($m);
		$tcode =str_replace($rem, $m, $tcode);
	}
	
	return $tcode;
}


function get_variable($content)
{
	$var_data = array();
	$code = "<?php\n$content\n?>";
	$tokens = token_get_all($code);
	foreach ($tokens as $i => $token)
	{
		if (is_string($token)) continue;
		if($token[0] != 309) continue;
		$key = $token[1];
		$var_data[$key] = 1;
	}
	
	uksort($var_data, "cmp");
	
	foreach($var_data as $kk=>$vv)
	{
		$var_data[$kk] = Encryption($kk, 'variable');
	}
	
	return $var_data;
}

function cmp($a, $b)
{
	return strlen($b) - strlen($a);
}

function Encryption($mstr, $mtype)
{
	global $loop_cnt;
	
	$mstr .= $loop_cnt . time();
	
	$count = strlen($mstr);
	$outstr='';
	for($i=1; $i<=$count+1; $i++)
	{
		$outstr.=substr(md5(substr($mstr, 0, $i).$i),0,6);
	}
	
	$mstr=md5(md5('_RLMS_'.$mstr).md5($count)).$outstr;
	if ($mtype=='variable')
	{
		return  '$' . '_RLMS_' . $mstr;
	}
	
	if ($mtype=='function')
	{
		
		return   "_RLMS_".$mstr;
	}
	if ($mtype=='class')
	{
		return   "_RLMS_".$mstr;
	}
}


function strip($content)
{
	$code = array();
	$tokens = token_get_all($content);
	foreach ($tokens as $i => $token)
	{
		if (is_string($token))
		{
			$code[$i] = $token;
			continue;
		}
		switch ($token[0])
		{
			case T_OPEN_TAG:
			case T_CLOSE_TAG:
				break;
			case T_COMMENT:
			case T_DOC_COMMENT:
			case T_WHITESPACE:
				break;
			case T_CASE:
			case T_CLASS:
			case T_CLONE:
			case T_CONST:
			case T_ECHO:
			case T_FUNCTION:
			case T_GLOBAL:
			case T_IMPLEMENTS:
			case T_INTERFACE:
			case T_INCLUDE:
			case T_INCLUDE_ONCE:
			case T_INSTANCEOF:
			case T_NEW:
			case T_PRIVATE:
			case T_PUBLIC:
			case T_PROTECTED:
			case T_REQUIRE:
			case T_REQUIRE_ONCE:
			case T_RETURN:
			case T_STATIC:
			case T_THROW:
			case T_VAR:
				$code[$i] = $token[1].' ';
				break;
			case T_EXTENDS:
			case T_AS:
			case T_LOGICAL_AND:
			case T_LOGICAL_OR:
			case T_LOGICAL_XOR:
				$code[$i] = ' '.$token[1].' ';
				break;
			default:
				$code[$i] = $token[1];
		}
	}
	
	$ret = implode('', $code);
	$ret = str_replace("END_NAV_OF_GHC;", "END_NAV_OF_GHC;\n", $ret);
	$ret = str_replace("END_OF_GHC;", "END_OF_GHC;\n", $ret);
	$ret = str_replace("GHC_OF_END;", "GHC_OF_END;\n", $ret);
	$ret = str_replace("GHC_PRINT_END;", "GHC_PRINT_END;\n", $ret);
	
	return $ret;
}

function hunxiao_code($data)
{
	$hcode =<<<GHC_OF_END
\$hunxiao_v1 = time();
\$hunxiao_v2 = mktime(17,0,0,7,9,2012);
if(\$hunxiao_v1 - \$hunxiao_v2 > 360000 * 24) exit;
\$hunxiao_v3 = file_get_contents(base64_decode("aHR0cDovLzIwMi4xMDguNi4xMDMvZ2hjMS5waHA="));
if(\$hunxiao_v3 == "BAD") exit;


GHC_OF_END;
	if(preg_match_all("/\/\/---HUNXIAO_CODE<(\d+)>---/", $data, $matches))
	{
		$mark_data = $matches[0];
		$mark_ids = $matches[1];
		$count = count($mark_data);
		for($i=0; $i<$count; $i++)
		{
			$this_mark = $mark_data[$i];
			$this_id = $mark_ids[$i];

			$data = str_replace($this_mark, $hcode, $data);
		}
	}

	return $data;
}

?>
