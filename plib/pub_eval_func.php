<?php
require_once("myfunc.php");
function text_substr($str, $len)
{
	$str = preg_replace(array('/<.*?>/'), array(''), $str);
        $s1 = trim($str);
        return cn_substr($s1, $len);
}

function get_url_path($doc_url)
{
	if($doc_url == "") return "";
	$pos = strrpos($doc_url, "/");
	if($pos)
	{
		$doc_url_base = substr($doc_url, 0, $pos);
	}
	else
	{
		$doc_url_base = "";
	}
	return  $doc_url_base;
}

function get_img_url($img, $doc_url)
{
	return $img;
	if(strpos($img, "http://") === 0) return $img;
	$doc_url_base = dirname($doc_url);
	$img = urlencode($img);
        return  $doc_url_base . "/" . $img;
}

function pub_substr($str,$len, $start=0)
{
	$len = $len * 2;
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

function get_table_data($sqlstr)
{
        global $proj_mysql;

        $key = "";
        $tdata = array();
        $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
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
	global $proj_mysql;
        $res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);
        $row = mysql_fetch_array($res, MYSQL_ASSOC);
        return $row;
}


function txt_to_ids($txt, $ct=0)
{
        $sp = explode("\n", $txt);
        $tdata = array();

        $ii = 0;
        foreach($sp as $vv)
        {
                $vv = trim($vv);
                if($vv == "") continue;
                if(!is_numeric($vv)) continue;

                $tdata[] = $vv;

                if($ct > 0 && count($tdata) >= $ct)
                {
                        break;
                }
        }

        if(count($tdata) == 0) return ""; 
        $tstr = implode(",", $tdata);
        return $tstr;
}
