<?php

class PAGE
{
	var $notify;
	var $pagelist;
	var $total_rec;
	var $limit_length;
	var $total;
	var $time_use;

	function getCGI()
	{
		$cgi=array();
		foreach($_POST as $key => $value)
		{
                	$vv = str_replace("　", " ", $value);
			if(get_magic_quotes_gpc()) $vv = stripcslashes($vv);
			$cgi[$key] = trim($vv);
		}

		foreach($_GET as $key => $value)
		{
			$vv = str_replace("　", " ", $value);
			if(get_magic_quotes_gpc()) $vv = stripcslashes($vv);
			$cgi[$key] = trim($vv);
		}
		return $cgi;
	}


	public function __construct()
	{
		$ret = func_num_args();
		$args=func_get_args();

		if($ret == 4)
		{
			$this->PAGE4($args[0], $args[1], $args[2], $args[3]);
		}
		else
		{
			print "PAGE: error parameter.\n";
		}
	}
	

	public function PAGE4($count, $total, $time_use, $length) 
	{


		//print "PAGE4($count, $total, $time_use, $length)<br>\n";
		$this->total_rec = $count;
		$this->limit_length = $length;
		$this->time_use = $time_use;
		$this->total = $total;
		$this->genPage();
	}

	public function genPage() 
	{

		$total_rec = $this->total_rec;
		$limit_length = $this->limit_length;

		$cgi = $this->getCGI();
		$page = $cgi[page]?$cgi[page]:0;

		$ppage = intval($page / 10);
		if($ppage % $limit_length == 0 && $ppage > 0) $ppage--;


		$cgi_para = "";
		foreach($cgi as $kk=>$vv)
                {
                        if($kk == 'page' || $kk == 'ppage' || $kk == 'limit_length') continue;
                        if($cgi_para == "")
                                $cgi_para .= sprintf("%s=%s", $kk, urlencode($vv));
                        else
                                $cgi_para .= sprintf("&%s=%s", $kk, urlencode($vv));

		}




		$limit_begin=$page * $limit_length;
		$limit_end = $limit_begin + $limit_length;
		if($limit_end > $total_rec) $limit_end = $total_rec;

		$last_page=$page-1;
		$next_page=$page+1;
		$first_page=0;
		$end_page= intval($total_rec/$limit_length);
		
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
			$last_page_buf =  "";
		}
		
		
		if(($page+1)*$limit_length<$total_rec)
		{
			$next_page_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>下一页</a>", $cgi_prog, $cgi_para, $next_page, ($page+1)%10==0?$ppage+1:$ppage);
		}
		else
		{
			$next_page_buf="";
		}
		
		
		
		
		if($first_page != $page)
		{
			$first_page_buf = sprintf("<a href=# onclick=\"return talkdata($vtype,%d);\">首页</a>", $first_page);
		}
		else
		{
			$first_page_buf = "首页";
		}
		
		
		if($end_page!=$page)
		{
			$end_page_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>%d</a>", $cgi_prog, $cgi_para, $end_page, $end_page/10, $end_page + 1);
		}
		else
		{
			$end_page_buf =  "";
		}
		
		
		
		
		
		
		if($ppage > 0)
		{
			//$last_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>&lt;&lt;</a> \n", $cgi_prog, $cgi_para, ($ppage-1)*10, $ppage-1);
			$last_buf = sprintf("<a href=%s?%s&page=%d>&lt;&lt;</a> \n", $cgi_prog, $cgi_para, ($ppage-1)*10);
		}
		else
		{
			$last_buf = "";
		}
		
		
		if( ($ppage+1)*10 < $end_page )
		{
			//$next_buf = sprintf("<a href=%s?%s&page=%d&ppage=%d>&gt;&gt;</a> \n", $cgi_prog, $cgi_para, ($ppage+1)*10, $ppage+1);
			$next_buf = sprintf("<a href=%s?%s&page=%d>&gt;&gt;</a> \n", $cgi_prog, $cgi_para, ($ppage+1)*10);
		}
		else
		{
			$next_buf = "";
		}
		

		$bpage = $page - 3;
		$epage = $page + 9;
		if($bpage < 0) $bpage = 0;
		if($epage > $end_page) $epage = $end_page;
		
		for($i=$bpage, $page_buf=''; $i <= $epage; $i++)
		{
			if($i != $page)
			{
				$page_buf .= sprintf("<a href=%s?%s&page=%d>%d</a>\n", $cgi_prog, $cgi_para,  $i, $i+1);
			}
			else
			{
				$page_buf .= sprintf("<a class=cur href=###>%d</a>\n", $i+1);
			}
		}
		
		
		//$this->notify = sprintf("共%d页%d条信息",  ceil($total_rec/$limit_length), $total_rec);
		//$this->notify = sprintf("获得约 %d 条结果，以下是第 %d-%d 条。 （用时 %.4f 秒）", $this->total, $limit_begin +1, $limit_end, $this->time_use/1000);
		$this->notify = sprintf("获得约 %d 条结果，用时 %.4f 秒", $this->total,  $this->time_use/1000);


		//$this->pagelist = "$first_page_buf $last_page_buf $last_buf $page_buf $next_buf $next_page_buf $end_page_buf";
		if($page + 9 >= $epage)
			$this->pagelist = "$last_page_buf\n $page_buf \n  $next_page_buf";
		else
			$this->pagelist = "$last_page_buf\n $page_buf \n <a class=nobg>...</a>\n $end_page_buf $next_page_buf";

		if($bpage == $epage) $this->pagelist = "";
		
		return 0;
	}

}

?>
