<?php

function sys_exit($display, $error_msg="", $exit_flag=true)
{
	
	$html_charset = HTML_CHARSET;
	print "<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$html_charset\">
	<meta http-equiv=\"Pragma\" content=\"no-cache\">
	<title>提示信息</title>
	<link href=\"/css/main.css\" rel=\"stylesheet\" type=\"text/css\" />
	</head>
	<body>
	<center><br><br><br>
	<table style=\"border:1px solid #333; background:#eee; padding:0px; width:350px;\"  cellpadding=0 cellspacing=0>
	<tr height=25 bgcolor=#aaaaaa><td align=center colspan=3><b>提示信息</b></td></tr>
	<tr height=10><td align=center colspan=3></td></tr>
	<tr height=80>
	<td width=10></td>
	<td>
	&nbsp;$display <br>
	
	$error_msg
	<a href=\"\" onclick=\"history.back(); return false;\">返回</a>
	</td>
	
	<td width=10></td>
	</tr>
	<tr height=10><td align=center colspan=3></td></tr>
	</table>
	</body>
	</html>\n";
	
	if($exit_flag) exit;
}

function sys_alert($display, $jmpurl)
{
	print "<script> alert('$display'); window.location='$jmpurl'; </script>";
	exit;
}

function sys_jmp($jmpurl)
{
	print "<script> window.location='$jmpurl'; </script>";
	exit;
}

function mysql_query_log($sqlstr, $sql, $mysql_handle)
{
	
	global $ck_user_id,  $ck_user_login,  $ck_user_name, $ck_user_type;
	
	if($sql != "")
	{
		$res = mysql_query($sql,$mysql_handle) or die("Invalid query: " . mysql_error());
		$beforeStat="之前的状态($sql)：";
		while($row = mysql_fetch_array($res))
		{
			foreach($row as $key => $value)
			{
				if(is_integer($key)) continue;
				$beforeStat.="$key=$value,";
			}
			$beforeStat.="\t";
		}
	}
	else
	{
		$beforeStat = "之前状态空";
	}
	
	
	
	$res = mysql_query($sqlstr, $mysql_handle);
	
	if($res)
	{
		$flag = "succ";
		$msg = "Sucess";
	}
	else
	{
		$flag="fail";
		$msg = mysql_error();
	}
	
	
	$logdir = $ck_user_type;
	$actName = "$ck_user_login($ck_user_name)";
	$actId = $ck_user_id;
	
	
	
	$filepath = "/pfp/log/$logdir/";
	system("mkdir -p $filepath");
	
	$filename = $filepath . date("Y",time())."".date("m",time()).".log";
	if (($fp = fopen($filename, "a+")) == false)
	{
		echo "$filename open error\n";
		return $res;
	}
	$ip= getenv("REMOTE_ADDR");
	$query = getenv("REQUEST_URI");
	$i=0;
	foreach($_POST as $key => $value)
	{
		$query.="&$key=$value";
		$i++;
	}
	
	
	$str = date("Y-m-d G:i:s",time())."|$ip|$actName|$actId|$sqlstr|$beforeStat|$query|$flag|$msg";
	$str = str_replace("\n", " ", $str)."\n";
	
	fwrite($fp, $str, strlen($str));
	fclose($fp);
	
	return $res;
}



function html_space($ct)
{
	$str = "";
	for($i=0; $i<$ct; $i++)
	{
		$str .= "&nbsp;";
	}
	return $str;
}


function  genPage($total_rec, $page, $ppage, $cgi_prog, $cgi_para)
{
	
	
	global $notify_buf, $page_desc, $limit_begin, $limit_length;
	
	
	$limit_begin=$page*$limit_length;
	$last_page=$page-1;
	$next_page=$page+1;
	$first_page=0;
	$end_page=$total_rec/$limit_length;
	
	
	if($total_rec % $limit_length == 0 && $end_page > 0 )
	{
		$end_page--;
	}
	
	
	if($last_page>=0)
	{
		$last_page_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>上一页</a>", $cgi_prog, $cgi_para, $last_page, ($page%10)==0?$ppage-1:$ppage);
	}
	else
	{
		$last_page_buf =  "上一页";
	}
	
	
	if(($page+1)*$limit_length<$total_rec)
	{
		$next_page_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>下一页</a>", $cgi_prog, $cgi_para, $next_page, ($page+1)%10==0?$ppage+1:$ppage);
	}
	else
	{
		$next_page_buf="下一页";
	}
	
	
	
	
	if($first_page != $page)
	{
		$first_page_buf = sprintf("<a href=%s?%s&page=%d>首页</a>", $cgi_prog, $cgi_para, $first_page);
	}
	else
	{
		$first_page_buf = "首页";
	}
	
	
	if($end_page!=$page)
	{
		$end_page_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>末页</a>", $cgi_prog, $cgi_para, $end_page, $end_page/10);
	}
	else
	{
		$end_page_buf =  "末页";
	}
	
	
	
	
	
	
	if($ppage > 0)
	{
		$last_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>&lt;&lt;</a> \n", $cgi_prog, $cgi_para, ($ppage-1)*10, $ppage-1);
	}
	else
	{
		$last_buf = "";
	}
	
	
	if( ($ppage+1)*10 < $end_page )
	{
		$next_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>&gt;&gt;</a> \n", $cgi_prog, $cgi_para, ($ppage+1)*10, $ppage+1);
	}
	else
	{
		$next_buf = "";
	}
	
	
	
	for($i=$ppage*10, $page_buf=''; $i<=$end_page && $i<($ppage*10+10); $i++)
	{
		if($i != $page)
		{
			$page_buf .= sprintf("<a href=%s?%s&page=%d&ppage=%d>[%d]</a>\n",
			$cgi_prog, $cgi_para,  $i, $ppage, $i+1);
		}
		else
		{
			$page_buf .= sprintf("<font color=red>%d </font>\n", $i+1);
		}
	}
	
	
	$notify_buf = sprintf("共%d页%d条信息",  ceil($total_rec/$limit_length), $total_rec);
	$page_desc  = "$first_page_buf $last_page_buf $last_buf $page_buf $next_buf $next_page_buf $end_page_buf";
	
	/*
	sprintf(notify_buf, "共%d个产品, 这是第%d到第%d个",
	total_rec, limit_begin+1>total_rec?total_rec:(limit_begin+1),
	(limit_begin+limit_length>total_rec)? total_rec: (limit_begin+limit_length)
	);
	
	sprintf(page_desc, "%s %s %s %s %s",
	last_page_buf,
	last_buf, page_buf, next_buf,
	next_page_buf);
	*/
	
	
	
	
	
	return 0;
	
}

function getCGI()
{
	
	$cgi=array();
	foreach($_POST as $key => $value)
	{
		
		//$vv = str_replace("　", " ", $value);
		$vv = $value;
        if(is_array($vv)){
            $vv=@join(',',$vv);
        }
		if(get_magic_quotes_gpc()) $vv = stripcslashes($vv);
		$cgi[$key] = trim($vv);
	}
	
	foreach($_GET as $key => $value)
	{
		//$vv = str_replace("　", " ", $value);
		$vv = $value;
		if(get_magic_quotes_gpc()) $vv = stripcslashes($vv);
		$cgi[$key] = trim($vv);
	}
	return $cgi;
}

function upload_files()
{
	global $cgi;
        foreach($_FILES as $key=>$this_file)
        {
                $filename = urlencode($this_file[name]);
                $filename = str_replace('%', '', $filename);
                $cgi[$key] = $filename;
                move_uploaded_file($this_file['tmp_name'], TMP_PATH . "/" . $filename);
        }
}


function upload_pic()
{
	global $poly_data, $cgi;

	$year = date("Y");
	$month  = date("m");
	$day = date("d");

	$path_html  =  "/upload_pub/$cgi[t_id]/$year/$month/$day";

	foreach($poly_data as $pm_id => $this_poly)
	{
  		$img_file_path =  $this_poly[file_path] . $path_html;
		break;
	}

	$img_file_path = str_replace("//", "/", $img_file_path);
	pub_mkdir($img_file_path);

	foreach($_FILES as $key=>$this_file)
	{
		if($this_file[name] == "")
		{
			continue;
		}
		$filename = "pic" . time() . "_" .  urlencode($this_file[name]);
		$filename = str_replace('%', '', $filename);
		
		$cgi[$key] = "$path_html/$filename";
		move_uploaded_file($this_file['tmp_name'], $img_file_path . "/" . $filename);
	}
}

function toJavascript($str)
{
	$ret = $str;
	$ret = str_replace("\\", "\\\\", $ret);
	$ret = str_replace("\r", "\\r", $ret);
	$ret = str_replace("\n", "\\n", $ret);
	$ret = str_replace('"', '\"', $ret);
	$ret = str_replace("'", "\\'", $ret);
	return $ret;
}


function js_unescape($escstr)
{
	preg_match_all("/%u[0-9A-Za-z]{4}|%.{2}|[0-9a-zA-Z.+-_]+/", $escstr, $matches);
	$ar = &$matches[0];
	$c = "";
	foreach($ar as $val)
	{
		if (substr($val, 0, 1) != "%")
		{
			$c .= $val;
		} elseif (substr($val, 1, 1) != "u")
		{
			$x = hexdec(substr($val, 1, 2));
			$c .= chr($x);
		}
		else
		{
			$val = intval(substr($val, 2), 16);
			if ($val < 0x7F)
			{
				$c .= chr($val);
			} elseif ($val < 0x800)
			{
				$c .= chr(0xC0 | ($val / 64));
				$c .= chr(0x80 | ($val % 64));
			}
			else
			{
				$c .= chr(0xE0 | (($val / 64) / 64));
				$c .= chr(0x80 | (($val / 64) % 64));
				$c .= chr(0x80 | ($val % 64));
			}
		}
	}
	
	return $c;
}


function gsql_esc(&$cgi)
{
	foreach($cgi as $kk=>$vv) $cgi["$kk"] =  mysql_escape_string($vv);
}

function utf8_to_gbk($str)
{
	return  iconv("UTF-8", "GB2312", $str);
}

function utf8_gbk(&$cgi)
{
	foreach($cgi as $kk=>$vv)
	{
		$v =  utf8_to_gbk($vv);
		$cgi["$kk"] =  $v;
	}
}


function writeFile($filename, $data)
{
	if (!$fp = fopen($filename, 'w'))
	sys_exit("不能打开文件 $filename");
	
	if (fwrite($fp, $data) === false)
	sys_exit("不能写入到文件 $filename");
	
	fclose($fp);
}

function pub_mkdir($filepath)
{
	$sp = explode("/", $filepath);
	$path = "";
	
	$i = 0;
	foreach($sp as $vv)
	{
		if($i == 0)
		{
			$path = $vv;
		}
		else
		{
			$path .= "/$vv";
			
		}
		$path = str_replace("//", "/", $path);
		@mkdir($path);
		$i++;
	}
}

function my_substr($str, $len, $start=0)
{
	if(DB_CHARSET == 'gbk')
	{
		return cn_substr_gbk($str, $len, $start);
	}
	else
	{
		return cn_substr_utf8($str, $len, $start);
	}
}

function cn_substr($str, $len, $start=0,$isadd=false)
{
	if(DB_CHARSET == 'gbk')
	{
		return cn_substr_gbk($str, $len, $start);
	}
	else
	{
		return cn_substr_utf8($str, $len, $start,$isadd);
	}
}

function cn_substr_gbk($str,$len, $start=0)
{

	$len *= 2;
	$restr="";
	$c="";
	$str_len=strlen($str);
	if($str_len<$start+1) //如果字符串长度小于等于要截取的开始位置的长度
	{
		return "";
	}
	if($str_len<$start+$len||$len==0)
	{
		$len=$str_len-$start;
	}
	$end=$start+$len-1;
	for($i=0;$i<$str_len;$i++)
	{
		if(ord($str[$i])>0x80)//判断是否为汉字
		{
			if($str_len>$i+1)
			{
				$c=$str[$i].$str[$i+1];
			}
			$i++;
		}
		else
		{
			$c=$str[$i];
		}
		if($i>$end) //如果超过要截取的字符则推出
		{
			if((strlen($restr)+strlen($c))>$len)
			{
				break;
			}else
			{
				$restr.=$c;
				break;
			}
		}
		//截取
		if($start==0 || $i>$start)
		{
			$restr.=$c;
		}
	}
	return $restr;
}


function cn_substr_utf8($sourcestr, $cutlength,  $cutstart=0,$isadd=false)
{
	$returnstr = '';
	$i = 0;
	$n = 0;
	$str_length = strlen ( $sourcestr ); //字符串的字节数
	while ( ($n < $cutlength) and ($i <= $str_length) )
	{
		$temp_str = substr ( $sourcestr, $i, 1 );
		$ascnum = Ord ( $temp_str ); //得到字符串中第$i位字符的ascii码
		if ($ascnum >= 224) //如果ASCII位高与224，
		{
			$returnstr = $returnstr . substr ( $sourcestr, $i, 3 ); //根据UTF-8编码规范，将3个连续的字符计为单个字符
			$i = $i + 3; //实际Byte计为3
			$n ++; //字串长度计1
		}
		elseif ($ascnum >= 192) //如果ASCII位高与192，
		{
			$returnstr = $returnstr . substr ( $sourcestr, $i, 2 ); //根据UTF-8编码规范，将2个连续的字符计为单个字符
			$i = $i + 2; //实际Byte计为2
			$n ++; //字串长度计1
		}
		elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
		{
			$returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
			$i = $i + 1; //实际的Byte数仍计1个
			$n ++; //但考虑整体美观，大写字母计成一个高位字符
		}
		else //其他情况下，包括小写字母和半角标点符号，
		{
			$returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
			$i = $i + 1; //实际的Byte数计1个
			//$n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
            $n=$n+1;
		}
	}


    if($isadd){
        if ($str_length > $i) {
            $returnstr = $returnstr . "..."; //超过长度时在尾处加上省略号
        }
    }


	return $returnstr;
}




function xCopy($source, $destination, $child=1)
{
	if(!is_dir($source))
	{
		echo "Error:the $source is not a directory";
		return 0;
	}
	
	if(!is_dir($destination)) mkdir($destination, 0777);
	
	$handle=dir($source);
	while($entry=$handle->read())
	{
		if(($entry!=".")&&($entry!=".."))
		{
			if(is_dir($source."/".$entry))
			{
				if($child)
				xCopy($source."/".$entry,$destination."/".$entry,$child);
			}
			else
			{
				copy($source."/".$entry,$destination."/".$entry);
			}
		}
	}
	return true;
}



function get_pinyin($cname)
{
	if(DB_CHARSET == 'utf8')
	{
		$cname = iconv("UTF-8", "GBK", $cname);
	}

	if($cname == "") return "";

	require_once("py.php");
	$py = new PY();
	$pinyin = $py->_get_pinyin($cname);
	return $pinyin;
}


function zoomImg($src, $dest, $width, $height)
{
        $srcImgRes=NewMagickWand();
        if(!MagickReadImage($srcImgRes,$src))
        {
                exit("Error MagickReadImage $src\n");

        }
        MagickScaleImage($srcImgRes, $width, $height);
        MagickWriteImage($srcImgRes,  $dest);
}

function getSalt()
{
	return substr(md5(mt_rand()), 0, 8);
}

function dump($arr)
{
	echo "<pre>";
	var_dump($arr);
	echo "</pre>";
}

