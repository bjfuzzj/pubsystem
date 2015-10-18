<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();
if(DB_CHARSET == "gbk") utf8_gbk($cgi);

$p_id = $cgi[p_id];
$t_id = $cgi[t_id];


if($p_id == "" || $t_id == "")
{
	print_r($cgi);
	exit;
	sys_exit("参数错误");
}

conProjDB($p_id, $t_id);

if($cgi[this_t_id] != "")
{
        $sqlstr = "select * from tempdef where t_id=$cgi[this_t_id] order by showorder asc, f_id asc";
        $res = mysql_query($sqlstr, $proj_mysql) or sys_exit( $sqlstr . "\n" . mysql_error());
        while($row = mysql_fetch_array($res))
        {
                $f_id = $row[f_id];
                $tempdef_data[$f_id] = $row;
        }
}

if($cgi[type] == 'Rel_Select')
{
	$f_ids = $cgi[f_ids];
	$this_t_id  = $cgi[this_t_id];

	if($this_t_id == "" || $f_ids == "") exit;

	$this_t_cname =  sprintf("{%s}", $temp_data[$this_t_id][cname]);

	$sp = explode(",", $f_ids);
	if(count($sp) > 1)
	{
		$this_f_id0 = $sp[0];
		$this_f_id1 = $sp[1];
		$field_cname = sprintf("{%s}, {%s}", $tempdef_data[$this_f_id0][cname], $tempdef_data[$this_f_id1][cname]);
	}
	else
	{
		$this_f_id = $sp[0];
		$field_cname = sprintf("distinct {%s}", $tempdef_data[$this_f_id][cname]);
	}
	

	if($cgi[parent_cname] != "")
	{
		$parent_str = sprintf("#parent:%s", $cgi[parent_cname]);
	}

	$condition_str = "";
	if($cgi[condition] != "")
	{
		$sp = explode(" or ", $cgi[condition]);
		$i = 0;
		foreach($sp as $kk=>$vv)
		{
			if($condition_str == "")
			{
				$condition_str = sprintf(" ( %s )", $vv);
			}
			else
			{
				$condition_str .= sprintf(" or ( %s )", $vv);
			}
			$i++;
		}
	
		if($i>1)
		{
			$condition_str = sprintf(" where ( %s ) ", $condition_str);
		}
		else
		{
			$condition_str =  " where " . $condition_str;
		}
	
	}

	print <<<END_OF_GHC
#query: select $field_cname from $this_t_cname $condition_str
$parent_str
END_OF_GHC;

}
else if($cgi[type] == 'File')
{

	if($cgi[cname] == "" || $cgi[itype] == "") exit;
	if($cgi[iwidth] == "") $cgi[iwidth] = 300;
	if($cgi[iheight] != "") $height_str = "height=$cgi[iheight]";
	if($cgi[iborder] != "") $border_str = "border=$cgi[iborder]";
	$src_str = sprintf("\${%s}", $cgi[cname]);

	if($cgi[itype] == "image")
	{
		print "<img width=$cgi[iwidth]  $height_str $border_str  src=\"$src_str\" >";
	}
	else if($cgi[itype] == "flash")
	{
		print <<<END_OF_GHC
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			width="$cgi[iwidth]" height="$cgi[iheight]"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="$src_str" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="$src_str" quality="high" bgcolor=""
				width="$cgi[iwidth]" height="$cgi[iheight]" align="middle"
				play="true"
				loop="false"
				quality="high"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
END_OF_GHC;
	}



}
else if($cgi[type] == 'Multi_Page')
{

	if($cgi[fieldname] == "") exit;
	if($cgi[plength] == "") $cgi[plength] = 15000;
	if($cgi[max_length] == "") $cgi[max_length] = 20000;

	print <<<END_OF_GHC
#fieldname:$cgi[fieldname]
#length:$cgi[plength]
#max_length:$cgi[max_length]
END_OF_GHC;

}
else if($cgi[type] == 'Sql_Result')
{
	$f_ids = $cgi[f_ids];
	$this_t_id  = $cgi[this_t_id];
	$limit_start = $cgi[start];
	$limit_length = $cgi[length];

	if( $this_t_id == "" || $f_ids == "") exit;

	if($limit_start == "") $limit_start = "0";
	if($limit_length == "") $limit_length = "6";

	$this_t_cname = "{" . $temp_data[$this_t_id][cname] . "}";
	$fids_data = explode(",", $f_ids);
	
	$sql_fields  = "";
	$code_fields = "";
	$html_fields = "";

	foreach($tempdef_data as $kk=>$row)
	{
		if(!in_array($kk, $fids_data)) continue;
	
		$sql_fields .= sprintf("{%s},", $row[cname]);
	
		if( in_array($row[type], array('Text', 'RichText', 'AutoTypeset_Text')) )
		{
			$html_fields .= sprintf("<p>{%s}</p>\n", $row[cname]);
			$code_fields .= sprintf("{%s} = text_substr({%s}, 300);\n\n", $row[cname], $row[cname]);
		}
		else if ($row[type] == "File")
		{
			$html_fields .= sprintf("<img src={%s} width=300 height=300 border=0>\n", $row[cname]);
			//$code_fields .= sprintf("{%s} = \$doc_path . {%s};\n\n", $row[cname], $row[cname]);
			$code_fields .= sprintf("{%s} = get_img_url({%s}, {url});\n\n", $row[cname], $row[cname]);
		}
		else
		{
			$html_fields .= sprintf("<span>{%s}</span>\n", $row[cname]);
			$code_fields .= sprintf("{%s} = cn_substr({%s}, 20);\n\n", $row[cname], $row[cname]);
		}
	}

	
	
	$condition_str = "";
	if($cgi[condition] != "")
	{
		$sp = explode(" or ", $cgi[condition]);
		$i = 0;
		foreach($sp as $kk=>$vv)
		{
			if($condition_str == "")
			{
				$condition_str = sprintf(" ( %s )", $vv);
			}
			else
			{
				$condition_str .= sprintf(" or ( %s )", $vv);
			}
			$i++;
		}
	
		if($i>1)
		{
			$condition_str = sprintf(" and ( %s ) ", $condition_str);
		}
		else
		{
			$condition_str =  " and " . $condition_str;
		}
	
	}


	print <<<END_OF_GHC
#sql:select   $sql_fields  url, createdatetime from $this_t_cname where published='y'  $condition_str order by d_id desc limit $limit_start, $limit_length

#code:
\$url_path = get_url_path({url});

$code_fields


#html:
<a target="_blank" href="{url}">

$html_fields
</a><br>
END_OF_GHC;

}
else if($cgi[type] == 'Php_List')
{
	$f_ids = $cgi[f_ids];
	$this_t_id  = $cgi[this_t_id];
	$limit_start = $cgi[start];
	$limit_length = $cgi[length];

	if( $this_t_id == "" || $f_ids == "") exit;

	if($limit_start == "") $limit_start = "0";
	if($limit_length == "") $limit_length = "6";

	$this_t_cname = "{" . $temp_data[$this_t_id][cname] . "}";
	$fids_data = explode(",", $f_ids);
	
	$sql_fields  = "";
	$code_fields = "";
	$html_fields = "";
	foreach($tempdef_data as $kk=>$row)
	{
		if(!in_array($kk, $fids_data)) continue;
	
		$sql_fields .= sprintf("{%s},", $row[cname]);

		if( in_array($row[type], array('Text', 'RichText', 'AutoTypeset_Text')) )
		{
			$html_fields .= sprintf("<p>{%s}</p>\n", $row[cname]);
			$code_fields .= sprintf("{%s} = text_substr({%s}, 300);\n\n", $row[cname], $row[cname]);
		}
		else if ($row[type] == "File")
		{
			$html_fields .= sprintf("<img src={%s} width=300 height=300 border=0>\n", $row[cname]);
			$code_fields .= sprintf("{%s} = get_img_url({%s}, \$doc_path);\n\n", $row[cname], $row[cname]);
		}
		else
		{
			$html_fields .= sprintf("<span>{%s}</span>\n", $row[cname]);
			$code_fields .= sprintf("{%s} = cn_substr({%s}, 30);\n\n", $row[cname], $row[cname]);
		}
	}
	
	
	$condition_str = "";
	if($cgi[condition] != "")
	{
		$sp = explode(" or ", $cgi[condition]);
		$i = 0;
		foreach($sp as $kk=>$vv)
		{
			if($condition_str == "")
			{
				$condition_str = sprintf(" ( %s )", $vv);
			}
			else
			{
				$condition_str .= sprintf(" or ( %s )", $vv);
			}
			$i++;
		}
	
		if($i>1)
		{
			$condition_str = sprintf(" and ( %s ) ", $condition_str);
		}
		else
		{
			$condition_str =  " and " . $condition_str;
		}
	
	}

	print <<<END_OF_GHC
#sql:select   $sql_fields  url, createdatetime from $this_t_cname where published='y'  $condition_str order by d_id desc limit $limit_length

#code:
$code_fields
#html:
<a target="_blank" href="{url}">
$html_fields
{createdatetime}
</a><br>
END_OF_GHC;



}

else if($cgi[type] == 'Form_List')
{
	$f_ids = $cgi[f_ids];
	$this_t_id  = $cgi[this_t_id];

	$this_t_cname = $temp_data[$this_t_id][cname];

	print <<<END_OF_GHC
#from:$this_t_cname
#html:
<form>
<--------------------item_loop_begin-------------------->
{name}: {input}<br>
<--------------------item_loop_end-------------------->
</form>
END_OF_GHC;

}

else if($cgi[type] == 'PostInPage')
{
	$tname = "{" . $cgi[tname] . "}";
	print <<<END_OF_GHC
$tname:1=1
END_OF_GHC;

}


?>
