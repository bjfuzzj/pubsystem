<?php

$mwww='./t1'; //要混淆的文件位置
$mdir='./t2'; //混淆后的文件存放位置

function cmp($a, $b) { return strlen($b) - strlen($a); }

$notfile=array('pinyin.php','pinyin_table.php');
$mreplase=array('$flag', '$p_id', '$t_id', '$d_id', '$f_id', '$poly', '$poly_index',
		'$field_name', '$arithmetic', '$field_id', '$validate',
		'$matches', '$cname', '$table', '$filename', '$pdoc', '$ename', '$f_name', '$t_name',
		'$sqlstr', '$pos', '$len', '$mark', '$res', '$row', 
		'$sp', '$rep1', '$rep2', '$buff', '$kk', '$vv', '$ii', '$i',
		'$type', '$showwidth', '$showheight', '$value', '$defaultvaluer',
		'$this_doc', '$doc_url', '$pair', '$sql_value', '$code_value', '$html_value', '$init_value', '$end_value',
		'$nocode', '$nosql', '$pTF', '$my_code', '$result', '$html',
		'$this_f_name', '$child_html', '$child_query', '$parent_field_name', '$parent_f_name', '$parent_value', '$query',
		'$display_value', '$display_len',
		'$fdata', '$fname',
		'$html_urlbase', '$url', '$form', '$str',
		'$mp', '$spos', '$ret', '$pm_id', '$doc_url', '$doc_filename', '$doc_filepath', 
);
$no_data = array(
'$today', '$hello_str', '$nav_str', '$notify_notnull', '$is_window', '$proj_data', '$poly_data',
'$temp_data', '$tempdef_data', '$global_data', '$pub_mysql', '$proj_mysql', '$db_name',
'$pre_field', '$page_split_mark', '$this_doc', '$eval_Table', '$async_buf',
'$pub_cookie_name', '$ck_u_id', '$ck_u_login', '$ck_u_name', '$ck_u_type', '$ck_u_priv', '$ck_u_allproj'
);

$mrefun=array(
'trim_last_splash',
'trim_big_bracer',
'getPath',
'getTablelist',
'getSqlstr',
'get_select_field',
'getFieldlist',
'get_rel_select_child',
'get_rel_select_field_name',
'getRel_Result',
'getJsName',
'genJsCode',
'fieldIntoForm',
'publishDocField',
'publishDocField_poly',
'doAboutURL',
'doAboutSql',
'doAboutWhere',
'sendPostInfo',
'AddDoc',
'doAboutPostInPage',
'doAboutPhp_List',
'doAboutForm_List',
'asyncDoc',
);
$mreclass=array();
$copyrightinfo="";

usort($mreplase, "cmp");
usort($mrefun, "cmp");
usort($no_data, "cmp");
print_r($mrefun);

tree($mwww);
foreach ($filearray as $t)
{
	
	$mfiledata='';
	$filedir=str_replace(basename($t), "", $t);
	
	$filedir=str_replace($mwww, $mdir, $filedir);
	//创建文件夹
	createFolder($filedir);
	
	$filename=str_replace($mwww, $mdir, $t);
	$nowfilename = basename($filename);
	//如果是php文件并不在//不混淆的php文件数组中
	if (fileext(basename($t))=='php' && !in_array($nowfilename,$notfile))
	{
		
		echo "复制并混淆文件:$filename\n";
		$mfiledata=php_strip_whitespace($t);
		$mfiledata=file_get_contents($t);


		foreach($no_data as $m)
		{
			$rem=strtoupper($m);
			$mfiledata=str_replace($m, $rem, $mfiledata);
		}

		//变量处理
		foreach($mreplase as $m)
		{
			$rem=Encryption($m,$mreplase,'variable');
			$mfiledata=str_replace($m, $rem, $mfiledata);
		}


		//函数处理
		foreach($mrefun as $mf)
		{
			$remf=Encryption($mf,$mrefun,'function');
			$mfiledata=str_replace($mf, $remf, $mfiledata);
		}

		
		foreach($mreclass as $mc)
		{
			$remc=Encryption($mc,$mreclass,'class');
			$mfiledata=str_replace($mc, $remc, $mfiledata);
		}
		foreach($no_data as $m)
		{
			$rem=strtoupper($m);
			$mfiledata=str_replace($rem, $m, $mfiledata);
		}
		
		//第一次写文件
		$fp = fopen($filename,'w');
		fwrite($fp,$mfiledata) or die('写文件错误');
		fclose($fp);
	}
}

exit("OK\n");


//------------------------------------------------------------------------------

//变量、函数、类的加密
function Encryption($mstr,$marray,$mtype)
{
	$count=array_search($mstr, $marray);
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


//遍历文件存放的数组
$filearray=array();

//遍历文件夹的方法
function tree($directory)
{
	global $filearray;
	$mydir=dir($directory);
	while($file=$mydir->read())
	{
		if((is_dir("$directory/$file")) AND ($file!=".") AND ($file!=".."))
		{
			tree("$directory/$file");
		}
		else if (($file!=".") AND ($file!=".."))
		{
			$filearray[]=$directory.'/'.$file;
		}
	}
	$mydir->close();
}

//取得文件后缀
function fileext($filename)
{
	return trim(substr(strrchr($filename, '.'), 1, 10));
}

function createFolder($path)
{
	if (!file_exists($path))
	{
		createFolder(dirname($path));
		mkdir($path, 0777);
	}
}

?>
