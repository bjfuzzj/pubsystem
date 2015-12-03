<?php
function doAboutMultiPage(&$mp)
{
	global $page_split_mark;

	$spos = "";
	if($mp[method] == 1)
	{
		printf("手工分页<br>\n");
		$sp_m = explode($page_split_mark, $mp[data]);
		$page_num = count($sp_m);
		printf("page_num: %d<br>\n", $page_num);
		return $sp_m;
	}
	else if($mp[method] == 2)
	{
		printf("自动分页<br>\n");
		if(strpos($mp[data], $page_split_mark) !== false)
		{
			$mp[data] = str_replace($page_split_mark, "", $mp[data]);
		}
		$len = html_length($mp[data]);
		if($len > $mp[max_length] )
		{
			$spos = page_split($mp[data], $mp[length], $page_num);
			printf("page_num: %d<br>\n", $page_num);
		}
		else
		{
			printf("自动分页没有达到条件.<br>\n");
		}
	}
	else
	{
		printf("没有开启分页功能<br>\n");
	}

	return $spos;
	
	
}




function genMuitiPageFile($mp, $spos, $html, $doc_filename, $doc_url)
{

	if($mp[method] == 1)
	{
		$page_num = count($spos);

		for($j=0; $j<$page_num; $j++)
		{
			$page_list = gen_page_list($doc_url, $j+1, $page_num);
			$content =  $spos[$j] . $page_list;
			
			$rep1 = sprintf('${%s}', $mp[cname]);
			$multi_html =  $html;
			
			$multi_html = str_replace($rep1, $content, $multi_html);

			
			$pos = strrpos($doc_filename, ".");
			if($pos)
			{
				$pre_path = substr($doc_filename, 0, $pos);
				$post_path = substr($doc_filename, $pos);
			}
			else
			{
				$pre_path = $doc_filename;
				$post_path = "";
			}
			
			
			
			$multi_doc_filename = $doc_filename;
			if($j>0)
			{
				$multi_doc_filename = sprintf("%s_%d%s", $pre_path, $j+1, $post_path);
			}
			printf("%s<br>\n", $multi_doc_filename);
			//printf("%s<br>\n", $page_list);
			$ret=writeFile($multi_doc_filename, $multi_html);
			if($ret == 0)
			{
				printf("%s", $error_message);
			}
		}
	}
	else if($mp[method] == 2 && $spos != "")
	{
		$j = 0;
		foreach($spos as $sp)
		{
			$content = substr($mp[data], $sp[begin], $sp[end] -$sp[begin] +1);
			
			$page_num = count($spos);
			$page_list = gen_page_list($doc_url, $j+1, $page_num);
			$content .= $page_list;
			
			$rep1 = sprintf('${%s}', $mp[cname]);
			$multi_html =  $html;
			
			$multi_html = str_replace($rep1, $content, $multi_html);
			
			$pos = strrpos($doc_filename, ".");
			if($pos)
			{
				$pre_path = substr($doc_filename, 0, $pos);
				$post_path = substr($doc_filename, $pos);
			}
			else
			{
				$pre_path = $doc_filename;
				$post_path = "";
			}
			
			
			$multi_doc_filename = $doc_filename;
			if($j>0)
			{
				$multi_doc_filename = sprintf("%s_%d%s", $pre_path, $j+1, $post_path);
			}
			printf("%s<br>\n", $multi_doc_filename);
			//printf("%s<br>\n", $page_list);
			$ret=writeFile($multi_doc_filename, $multi_html);
			if($ret == 0)
			{
				printf("%s", $error_message);
			}
			
			$j++;
		}
	}
	else
	{
		$rep1 = sprintf('${%s}', $mp[cname]);
		$html = str_replace($rep1, $mp[data], $html);
		writeFile($doc_filename, $html, 0);
	}
}







function getMultiPage($t_id)
{
	global $proj_mysql;
	
	$sqlstr = sprintf("select arithmetic, f_name from tempdef where t_id=%d and type='Multi_Page'", $t_id);
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr . "\n");
	
	$row = mysql_fetch_array($res, MYSQL_ASSOC);
	if($row == "")
	{
		printf("模板%d 没有Multi_Page模板域<br>\n", $t_id);
		return "";
	}
	
	$arithmetic = $row[arithmetic];
	$f_name = $row[f_name];
	$this_TF = getTF($f_name,  "f_name");
	if($this_TF == "")
	{
		$error_message = sprintf("%s not found in TF.", $f_name);
		return "";
	}
	
	$method = $this_TF[f_value];
	
	$fieldname = readFieldData($arithmetic, "#fieldname");
	if($fieldname == "")
	{
		$error_message = sprintf("fieldname not found in %s !", $arithmetic);
		return "";
	}
	
	$length = readFieldData($arithmetic, "#length");
	if($length == "")
	{
		$error_message = sprintf("length not found in %s !", $arithmetic);
		return "";
	}
	
	$max_length = readFieldData($arithmetic, "#max_length");
	if($max_length == "")
	{
		$error_message = sprintf("max_length not found in %s !", $arithmetic);
		return "";
	}
	
	
	
	$mp = array();
	$mp['length'] = $length;
	$mp['max_length'] = $max_length;
	
	$this_TF = getTF($fieldname,  "cname");
	if($this_TF == "")
	{
		$error_message = sprintf("%s not found in TF.", $fieldname);
		return "";
	}
	
	
	$mp[cname] =  $this_TF[cname];
	$mp[f_name] = $this_TF[f_name];
	$mp[data] = $this_TF[f_value];
	$mp[method] = $method;
	
	//	print_r($mp);
	
	return $mp;
}


function page_split($data, $page_length, &$pnum)
{
	$sp = array();
	$this_sp = array('begin'=>0, 'end'=>0);
	$dlen = strlen($data);
	
	print "dlen: $dlen<br>\n";
	
	for($ii=0, $len=0, $flag=0; $ii<$dlen; $ii++)
	{
		$ch = $data{$ii};
		if($ch == '<' && $flag ==0)
		{
			if($len >= $page_length)
			{
				$this_sp['end'] = $ii - 1;
				$sp[] = $this_sp;
				
				$this_sp[begin] = $ii;
				$this_sp[end] = $dlen;
				$len  = 0;
			}
			$flag = 1;
			continue;
		}
		else if($ch  == '>' && $flag ==1)
		{
			if($len >= $page_length)
			{
				$this_sp['end'] = $ii;
				$sp[] = $this_sp;
				
				$this_sp[begin] = $ii + 1;
				$this_sp[end] = $dlen;
				$len  = 0;
			}
			$flag = 0;
			continue;
		}
		else if($flag == 0)
		{
			$len++;
		}
	}
	
	if($this_sp[end] - $this_sp[begin] > 0) $sp[] = $this_sp;
	$pnum = count($sp);
	
	return $sp;
}

function gen_page_list($doc_url, $index, $page_num)
{
	
//	print "index: $index, page_num:$page_num<br>\n";
	
	$pos = strrpos($doc_url, ".");
	if($pos)
	{
		$pre_url = substr($doc_url, 0, $pos);
		$post_url = substr($doc_url, $pos);
	}
	else
	{
		$pre_url = $doc_url;
		$post_url = "";
	}
	
	
	for($i=1; $i<= $page_num;  $i++)
	{
		$this_url = $doc_url;
		if($i > 1)
		{
			$this_url = sprintf("%s_%d%s", $pre_url, $i, $post_url);
		}
		
		if($i==$index -1)
		{
			$last_page = sprintf(" <span class=page> <a href=%s>上一页</a> </span> ", $this_url);
		}
		
		if($i==$index +1)
		{
			$next_page = sprintf(" <span class=page> <a href=%s>下一页</a> </span> ", $this_url);
		}
		
		if($i == $index)
		{
			$page_list .= sprintf(" <span class=page_current>&nbsp;%d&nbsp;</span>",  $i);
		}
		else
		{
			$page_list .= sprintf(" <span class=page>&nbsp;<a href=%s>%d</a>&nbsp;</span> ", $this_url, $i);
		}
	}
	
	$result = sprintf("\n\n<br><br><p align=right>%s%s%s</p>", $last_page, $page_list, $next_page);
	return $result;
}

