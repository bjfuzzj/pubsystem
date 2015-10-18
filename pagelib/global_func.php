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
            $n = $n + 1;
		}
	}

    if($isadd){
        if ($str_length > $i) {
            $returnstr = $returnstr . "..."; //超过长度时在尾处加上省略号
        }
    }


	return $returnstr;
}

function getCGI()
{
	$cgi=array();
	foreach($_POST as $key => $value)
	{
		
		$tv = gettype($value);
		if($tv != "array")
		{
			$vv = str_replace("　", " ", $value);
			$cgi[$key] = trim($vv);
		}
		else
		{
			$cgi[$key] = $value;
		}
		
	}
	
	foreach($_GET as $key => $value)
	{
		$vv = str_replace("　", " ", $value);
		$cgi[$key] = trim($vv);
	}
	return $cgi;
}

function getmicrotime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function text_substr($str,$len,$isadd=false)
{
	$s1 = preg_replace(array("/<.*?>/"), array(""), $str);
	$s1 = trim($s1);
	if($isadd) return cn_substr($s1,$len,0,true);
	return cn_substr($s1,$len);
}

function get_url_path($doc_url)
{
        if($doc_url == "") return "";
        $ii = strrpos($doc_url, "/");
        if($ii)
        {
                $doc_url_base = substr($doc_url, 0, $ii);
        }
        else
        {
                $doc_url_base = "";
        }
        return  $doc_url_base;
}

function get_img_url($img, $doc_url)
{
	if(strpos($img, "http://") === 0) return $img;
        $ii = strrpos($doc_url, "/");
        if($ii)
        {
                $doc_url_base = substr($doc_url, 0, $ii);
        }
        else
        {
                $doc_url_base = "";
        }

	$img = urlencode($img);
	$img = str_replace("+", " ", $img); 
	
        return  $doc_url_base . "/" . $img;
}

function get_table_data($sqlstr)
{
        global $g_mysql;

        $key = "";
        $tdata = array();
        $res = mysql_query($sqlstr, $g_mysql) or exit(mysql_error() . "\n" . $sqlstr);
        while($row = mysql_fetch_array($res, MYSQL_ASSOC))
        {
                if($key == "")
                {
                        foreach($row as $kk=>$vv) { $key = $kk; break; }
                }

                $tdata["$row[$key]"] = $row;
        }
        return $tdata;
}

function get_table_row($sqlstr)
{
    global $g_mysql;
    $res = mysql_query($sqlstr, $g_mysql) or exit(mysql_error() . "\n" . $sqlstr);
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
    return $row;
}

?>
