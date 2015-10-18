//==============================================================================================
// 文本排版函数集
//==============================================================================================


//-------------------------------------------------------------
// 功能:
// 参数:
// 返回值:
//-------------------------------------------------------------
function ispun(c1,c2)
{
	if ((c1 == '\xa1' && c2 == '\xa3') || (c1 == '\xa3' && c2 == '\xac')|| (c1 == '\xa3' && c2 == '\xbb') || (c1 == '\xa3' && c2 == '\xba') || (c1 == '\xa3' && c2 == '\xa1') || (c1 == '\xa1' && c2 == '\xb1') || (c3 == '\xa3' && c2 == '\xbf') || (c3 == '\xa1' && c2 == '\xb7'))
	return 1;
	return 0;
}

//-------------------------------------------------------------
// 功能:
// 参数:
// 返回值:
//-------------------------------------------------------------
function ishalfpun(c2)
{
	if (c2 == '\x2c' || c2 =='\x2e' || c2 =='\x3b' || c2 =='\x2e' || c2 =='\x3a' || c2 =='\x21' || c2 =='\x3f' || c2 =='\x3e')
	return 1;
	return 0;
}


//-------------------------------------------------------------
// 功能:
// 参数:
// 返回值:
//-------------------------------------------------------------
function formattext(text,addp)
{
	/* 去除分页标记 start  2008.1.8 zhangping1 modify*/
	var splitPageReg = /\[page\s+title=(.*)*\s+subtitle=(.*)*\]/g;
	text = text.replace(splitPageReg,'');
	/* 去除分页标记 end */

	sbcarray = new Array("ａ","ｂ","ｃ","ｄ","ｅ","ｆ","ｇ","ｈ","ｉ","ｊ","ｋ","ｌ","ｍ","ｎ","ｏ","ｐ","ｑ","ｒ","ｓ","ｔ","ｕ","ｖ","ｗ","ｘ","ｙ","ｚ","Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ","Ｎ","Ｏ","Ｐ","Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ","．");
	dbcarray = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",".");
	headarray = new Array("本报讯","消息","日电");
	var flag=0;
   
	//首先把"<p>"、"<br>"、"</p>"、"<p>　　"全部滤掉
	var naivete_array =text.split("<p>　　");
	if (naivete_array.length >=0)
	{
		text="";
	    for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
	    }
	}
	
	naivete_array =text.split("<p>");
    if (naivete_array.length >=0)
	{
		text="";
        for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
        }
    }
        
		//增加删除大写空格+小写空格 modify by zm 2001.8.8
	naivete_array =text.split("　 ");
	if (naivete_array.length >=0)
	{
		text="";
        for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
        }
    }  

	naivete_array =text.split("<P>");
	if (naivete_array.length >=0)
	{
		text="";
		for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
		}
	}

	naivete_array =text.split("<br>");
	if (naivete_array.length >=0)
	{
		text="";
		for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
		}
	}
 
 	naivete_array =text.split("<BR>");
	if (naivete_array.length >=0)
	{
		text="";
	    for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
		}
	}
        
	naivete_array =text.split("</p>");
    if (naivete_array.length >=0)
	{
		text="";
	    for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
	    }
	}

	naivete_array =text.split("</P>");
	if (naivete_array.length >=0)
	{
		text="";
	    for (loop=0; loop < naivete_array.length;loop++)
		{
			text = text + naivete_array[loop];
	    }
	}
	//2004.2.4 赵熠修改
	//将原来最后处理的http://的步骤，放到剔除所有<p>,</p>之后做，并将该功能单独成为一个函数
	//text = replaceHttp(text);	
		
	oldlen = text.length;
        
	firstflg = 0; //清除文章头部空格回车标志
	linenumber = 0;
	lcount = 0;

	tmpstring = "";
	oneline =1;
	for(i=0; i<oldlen; i++)
	{	
		c1 = text.charAt(i);
        tmpstring +=c1;
			
		if((c1 != '\n') && (c1!=' ') && (c1!='　'))
		{
			firstflg=1;
			linenumber =1;
		}
        if(firstflg == 1)
		{
			if(c1 == '\n')
			{
				linenumber=0;
				dcount=0;
		    }
		    if(linenumber==1)
			{
				if(c1!='\r')
				{
					if(c1 > '\xff')
					lcount +=2;
					else
					{
			          lcount+=1;
					}
		       }
			}
			if(linenumber == 0)
			{
				for(j=i;j<=i+4;j++)
				{
					if(text.charAt(j) == '　')
					{
						dcount +=2;
					}
					else if(text.charAt(j) == '\x20')
					{
						dcount += 1;
					}
				}
				if(navigator.appName.indexOf("Netscape") != 0)
				{
					if(dcount >= 3)
					{
		               tmpstring +="\r\n\r\n";
		            }
		         }
			     lcount = 0;
		         linenumber=1;
		         oneline ++;
		    }
		}
	}
	text = tmpstring;
	
	oldlen = text.length;
	tmpstr = "";
	del = '1';
	
	firstflg = 0; 
	linenumber = 0;
	for(i=0; i<oldlen; i++)
	{
		c1 = text.charAt(i);
		if (c1 == '\r')
		{
			del = '1';
			continue;
		}
		else
		{
			if((c1 != '\n') && (c1!=' ') && (c1!='　'))
			{
				firstflg=1;
			}	
			if (c1=='　' && i>0)
			{
				if (text.charAt(i-1) == '\r' || text.charAt(i-1) == '\n')
				{
					firstflg = 0;
				}
			}

			if(firstflg == 0)
			{
				continue;
			}
			if(c1 > '\xff')
			{
				if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
				{
					continue;
				}
				else
				{
					//if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
					if (c1 == '\n' || c1 == '\x09')
					del = '1';
					else
					del = '0';
					tmpstr += c1;
				}
			}
			else
			{
				if(c1 > '\x80')
				{
					del = '1';
					tmpstr += c1;
				}
				else
				{
					if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
					{
						continue;
					}
					else
					{
						//if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
						if ( c1 == '\n' || c1 == '\x09')
						del = '1';
						else
						del = '0';
						tmpstr += c1;
					}
				}
			}
		}
	}
	text = tmpstr;
	//替换超链
	text = newReplaceHttp(text);
	//替换MAIL
	text = replaceMail(text);
	oldlen = text.length;
	tmpstr = "";
	for(i=0; i<oldlen; i++)
	{
		c1 = text.charAt(i);
		c2 = text.charAt(i+1);
		c3 = text.charAt(i+2);
		if (c1 == '\n' && c2 == '\n' && c3 == '\n')
		continue;
		tmpstr += c1;
	}
	
	text = tmpstr;
   	
   	oldlen = text.length;
	//result = (addp) ? "<p>" : "    "; //用全角空格代替
	if((text.charAt(0) != '\n') ||  (text.charAt(0) != '　'))
	{
		result = ""; //用全角空格代替
	}
	else
	{
		result = (addp) ? "<p>　　" : "    "; //用全角空格代替
	}
	
	count = 4;
	oneretn = 0;
	for(i=0; i<oldlen-1; i++)
	{
		c1 = text.charAt(i);
		c2 = text.charAt(i+1);
		c3 = text.charAt(i+2);
		c4 = text.charAt(i+3);
		c5 = text.charAt(i+4);
		c6 = text.charAt(i+5);
		c7 = text.charAt(i+6);
		c8 = text.charAt(i+7);
		if (c1 == '\n')
		{
			if (c2 == '\n'  )
			{
				
			}
			else
			{
				if (oneretn == 1)
				result += (addp) ? "</p>\n<p>" : "\n";
				else
				result += (addp) ? "</p>\n\n<p>" : "\n\n";
				//result += "    ";
				result += "　　"; //用全角空格代替
				count = 4;
				//i++;
				oneretn = 0;
			}
			continue;
		}
		else
		{
			if (c1 == " " && count == 0)
			{
				continue;
			}
			else
			{
				if ((c1 == '\xa1' && c2 == '\xa1') && count == 0)
				{
					i++;
					continue;
				}
			}
			oneretn = 0;
            if(c1 > '\xff')
			{
				if(c1 == '）')
				{
					result += ')';
					count+=1;
					continue;
				}
				if(c1 == '（')
				{
					result += '(';
					count+=1;
					continue;
				}
				if(c1 == '「')
				{
					result += '“';
					count+=1;
					continue;
				}
			    if(c1 == '」')
				{
					result += '”';
					count+=1;
					continue;
				}
				if(c1 == '■')
				{
					result += '-';
					count += 1;
					continue;
				}
				if(c1 == '．')
				{
					result += '.';
					count += 1;
					continue;
				}
				if(c1 == '－')
				{
					result += '-';
					count += 1;
					continue;
				}
				if(c1 == '１')
				{
					result += '1';
					count += 1;
					continue;
				}
			    if(c1 == '２')
				{
					result += '2';
					count += 1;
					continue;
				}
				if(c1 == '３')
				{
					result += '3';
					count += 1;
					continue;
				}
			    if(c1 == '４')
				{
					result += '4';
					count += 1;
					continue;
				}
				if(c1 == '５')
				{
					result += '5';
					count += 1;
					continue;
				}
				if(c1 == '６')
				{
					result += '6';
					count += 1;
					continue;
				}
			    if(c1 == '７')
				{
					result += '7';
					count += 1;
					continue;
				}
			    if(c1 == '８')
				{
					result += '8';
					count += 1;
					continue;
				}
				if(c1 == '９')
				{
					result += '9';
					count += 1;
					continue;
				}
				if(c1 == '０')
				{
					result += '0';
					count += 1;
					continue;
				}
				if(c1 == '')
				{
					result += '0';
					count += 1;
					continue;
				}
				if(c1 == '％')
				{
					result += '%';
					count += 1;
					continue;
				}
				for (var cstep=0;cstep<52;cstep++)
				{
					if (c1 == sbcarray[cstep])
					{
						flag = 1;
						break;
				    }
				}   
				if(flag == 1)
				{
					result += dbcarray[cstep];
					count += 1;
					flag = 0;
					continue;
				}
                result += c1;
				count+=1;
				if (c2 != '\n')
				{
					if (ishalfpun(c2))
					{
						result += c2;
						count+=1;
						i++;
					}
					else
					{
						if (c2 == '\x22' || c2== '\x27')
						{
							result += c2;
							count+=1;
							i++;
							if (c3 != '\n')
							{
								if (ishalfpun(c3))
								{
									result += c3;
									count+=1;
									i++;
								}
							}
							else
							{
								if (ishalfpun(c4))
								{
									result += c4;
									count+=1;
									i+=2;
								}
							}
						}
					}
				}
				else
				{
					if (ishalfpun(c3))
					{
						result += c3;
						count+=1;
						i+=2;
					}
					else
					{
						if (c3 == '\x22' || c3== '\x27')
						{
							result += c3;
							count+=1;
							i+=2;
							if (c4 != '\n')
							{
								if (ishalfpun(c4))
								{
									result += c4;
									count+=1;
									i+=2;
								}
							}
							else
							{
								if (ishalfpun(c5))
								{
									result += c5;
									count+=1;
									i+=3;
								}
							}
						}
					}
				}
			}
			else if(c1 > '\x80')
			{
				if (c1 == '\xa1' && c2 == '\xa1')
				{//　space
					i++;
					continue;
				}
				result += c1;
				result += c2;
				count+=2;
				i++;
				if (c3 == '\n')
				{
					if (c4 == '\xa1' && c5 == '\xa3')
					{//。
						result += c4;
						result += c5;
						count+=2;
						i+=3;
					}
					else
					{
						if (c4 == '\xa3' && c5 == '\xac')
						{ //，
							result += c4;
							result += c5;
							count+=2;
							i+=3;
						}
						else
						{
							if (c4 == '\xa3' && c5 == '\xbb')
							{ //；
								result += c4;
								result += c5;
								count+=2;
								i+=3;
							}
							else
							{
								if (c4 == '\xa3' && c5 == '\xba')
								{ //：
									result += c4;
									result += c5;
									count+=2;
									i+=3;
								}
								else
								{
									if (c4 == '\xa3' && c5 == '\xa1')
									{ //！
										result += c4;
										result += c5;
										count+=2;
										i+=3;
									}
									else
									{
										if ((c4 == '\xa1' && c5 == '\xb1') || (c4 == '\xa1' && c5 == '\xaf'))
										{ //”
											result += c4;
											result += c5;
											count+=2;
											i+=3;
											if (c6 == '\n')
											{
												if (ispun(c7,c8) == 1)
												{
													result += c7;
													result += c8;
													count+=2;
													i+=3;
												}
											}
											else
											{
												if ((a =ispun(c6,c7)) == 1)
												{
													result += c6;
													result += c7;
													count+=2;
													i+=2;
												}
											}
										}
										else
										{
											if (c3 == '\xa3' && c4 == '\xbf')
											{ //？
												result += c3;
												result += c4;
												count+=2;
												i+=3;
											}
											else
											{
												if (c3 == '\xa1' && c4 == '\xb7')
												{ //》
													result += c3;
													result += c4;
													count+=2;
													i+=3;
												}
												else
												{
												}
											}
										}
									}
								}
							}
						}
					}
				}
				else
				{
					if (c3 == '\xa1' && c4 == '\xa3')
					{ //。
						result += c3;
						result += c4;
						count+=2;
						i+=2;
					}
					else
					{
						if (c3 == '\xa3' && c4 == '\xac')
						{ //，
							result += c3;
							result += c4;
							count+=2;
							i+=2;
						}
						else
						{
							if (c3 == '\xa3' && c4 == '\xbb')
							{//；
								result += c3;
								result += c4;
								count+=2;
								i+=2;
							}
							else
							{
								if (c3 == '\xa3' && c4 == '\xba')
								{//：
									result += c3;
									result += c4;
									count+=2;
									i+=2;
								}
								else
								{
									if (c3 == '\xa3' && c4 == '\xa1')
									{//！
										result += c3;
										result += c4;
										count+=2;
										i+=2;
									}
									else
									{
										if ((c3 == '\xa1' && c4 == '\xb1') || (c3 == '\xa1' && c4 == '\xaf'))
										{//” or  ’
											result += c3;
											result += c4;
											count+=2;
											i+=2;
											if (c5 == '\n')
											{  
												if (ispun(c6,c7) == 1)
												{
													result += c6;
													result += c7;
													count+=2;
													i+=3;
												}
											}
											else
											{
												if (ispun(c5,c6) == 1)
												{
													result += c5;
													result += c6;
													count+=2;
													i+=2;
												}
											}
										}
										else
										{
											if (c3 == '\xa3' && c4 == '\xbf')
											{  //？
												result += c3;
												result += c4;
												count+=2;
												i+=2;
											}
											else
											{
												if (c3 == '\xa1' && c4 == '\xb7')
												{//  》
													result += c3;
													result += c4;
													count+=2;
													i+=2;
												}
												else
												{
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			else
			{
			    result += c1;
				count++;
			}
		}
                //if(count>=55){
		 //   result += (addp) ? "\n" : "\n";
		  //  oneretn = 1;
                   // count = 0;
               // }
	}
    if(i<oldlen)
    result += text.charAt(i);
   	if(addp)
    result +="</p>\n";
    
                
	//"<p>　　　　"=>"<p>　　"
    var naivete_array =result.split("<br>　　　　");
    if (naivete_array.length >1)
	{
		result="";
        for (loop=0; loop < naivete_array.length;loop++)
		{
			if(result != ""){ result = result +"<br>　　"+ naivete_array[loop];}
            else{ result = naivete_array[loop];}
		}
	}

	var naivete_array =result.split("<p>　　　　");
    if (naivete_array.length >1)
	{
		result="";
        for (loop=0; loop < naivete_array.length;loop++)
		{
			if(naivete_array[loop] !="")
			{
				result = result +"<p>　　"+ naivete_array[loop];
			}
		}
	}
	else
	{
	    result = "<p>　　"+naivete_array;
	}
	//return result;
	        //modified by jxz 2000.6.13
        //以下为清除文中全角字符间的空格,且保留半角字符间的空格
        
        //modified by zhaoming 2001.8.7  jxz功能改进
				//先将全角空格转成半角空格
			  //如果小写空格两边是半角字符(字母数字),则该空格保留
			  //如果小写空格两边不是半角字符(字母数字)或全角数字字母,则去掉空格
	/*
		oldlen = result.length;
        result1 = "";
        spaceflg = 0;
        for(i=0; i<oldlen; i++)
		{
			c1 = result.charAt(i);

            codevalue1 = result.charCodeAt(i);
            c2 = result.charAt(i+1);
            codevalue2 = result.charCodeAt(i+1);
            c3 = result.charAt(i+2);
            codevalue3 = result.charCodeAt(i+2);
            if( (codevalue1 <127) && (codevalue3 <127))
                   spaceflg=0;
            else
                   spaceflg=1;
                
            result1 += c1;
            if( (spaceflg == 0) &&  ((c2 == '　')||(c2 == ' ')) )
			{
               	result1 += ' ';
               	i+=1;
            }
            if( (spaceflg == 1) &&  ((c2 == '　')||(c2 == ' '))&&((c1 != '>')&&(c1 != '　')) )
			{
				// alert("c1="+c1);
				i+=1;
			}
        }
        
        result = result1;
        oldlen = result.length;
	*/
        //modified by jxz 2000.6.13
	
	
        //最后把结尾处的"<br>　　<br>"滤掉
       var naivete_array =result.split("<br>　　<br>");
       if (naivete_array.length >1){
	result="";
        for (loop=0; loop < naivete_array.length;loop++){
                 result = result + naivete_array[loop];
                 }
	}


        //最后把结尾处的"<p>　　</p>"滤掉
       var naivete_array =result.split("<p>　　</p>");
       if (naivete_array.length >1){
	result="";
        for (loop=0; loop < naivete_array.length;loop++){
                 result = result + naivete_array[loop];
                 }
	}
	
        //最后把结尾处的"　　<br>"滤掉
       var naivete_array =result.split("　　<br>");
       if (naivete_array.length >1){
	result="";
        for (loop=0; loop < naivete_array.length;loop++){
                 result = result + naivete_array[loop];
                 }
	}
	
	return result;
}	


	//2004.2.4 赵熠修改，将此部分单独做为一个函数
       //最后把"http://xxxx[ ]"替换为"<a href=http://xxx>xxx</a>"滤掉
function replaceHttp(result)
{	
	var naivete_array =result.split("http:");
	var mytag=0;
	var checkflag;
	if (naivete_array.length >=0)
	{
		result="";
        	for (loop=0; loop < naivete_array.length;loop++)
        	{
                	mytag=naivete_array[loop].indexOf(' ');
                	if(mytag<0)
                	{
                        	mytag=naivete_array[loop].indexOf(".shtml");
                        	if(mytag>0)
                        	{
                        		mytag+=6;
                        	}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf("\n.shtml");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".\nshtml");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".s\nhtml");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".sh\ntml");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".sht\nml");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".shtm\nl");
				if(mytag>0)
				{
					mytag+=7;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".html");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf("\n.html");
				if(mytag>0)
				{
					mytag+=6;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".\nhtml");
				if(mytag>0)
				{
					mytag+=6;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".h\ntml");
				if(mytag>0)
				{
					mytag+=6;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".ht\nml");
				if(mytag>0)
				{
					mytag+=6;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".htm\nl");
				if(mytag>0)
				{
					mytag+=6;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".htm");
				if(mytag>0)
				{
					mytag+=4;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf("\n.htm");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".\nhtm");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".h\ntm");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".ht\nm");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".asp");
				if(mytag>0)
				{
					mytag+=4;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf("\n.asp");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".\nasp");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".a\nsp");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			if(mytag<0)
			{
				mytag=naivete_array[loop].indexOf(".as\np");
				if(mytag>0)
				{
					mytag+=5;
				}
			}
			
			if(naivete_array[loop].substring(0,2) =='//')
			{
				naivete_array[loop]='http:'+naivete_array[loop];
				mytag=mytag+5;
			}
			
			checkflag = 0;
			if(naivete_array[loop].substring(0,7) =='http://' && mytag>5 && mytag<100)
			{
				for(var step1=1;step1<mytag;step1++ )
				{
					temp = naivete_array[loop].substring(step1-1,step1);
					if((temp>'\x7f'))
					{
						checkflag = 1;
						break;
					}
		        	}
		        	
		        	if(checkflag == 1)
		        	{
		        	        var temp="";
		        	        for(step=8;step<mytag;step++)
		        	        {
		        	        	temp = naivete_array[loop].substring(step-1,step);
		        	        	if((temp<'\x30' && temp != '\x2e' && temp != '\x2d' && temp != '\x2c' && temp != '\x2f') || (temp>'\x7a') || ( (temp>'\x5a')&&(temp<'\x61')&&(temp != '\x5f') ))
		        	        	{
		        	        		mytag = step-1;
		        	        		break;
		        	        	}
		        	        }
		        	}
		        	
		        	myurl=naivete_array[loop].substring(0,mytag);
		        	tmpurl = naivete_array[loop].substring(7,mytag);
		        	myurl_true=myurl.replace("\n","");
		        	myurl_href='<a href='+myurl_true+'>'+tmpurl+'</a>';
		        	if(loop>0)
		        	{
		        		if(naivete_array[loop-1].substring(naivete_array[loop-1].length-1,naivete_array[loop-1].length)=='>' || naivete_array[loop-1].substring(naivete_array[loop-1].length-5,naivete_array[loop-1].length)=='href=')
		        		{
		        		       result = result + naivete_array[loop];
		        		}
		        		else
		        		{
		        		       result = result + naivete_array[loop].replace(myurl,myurl_href);
		        		}  
		        	}  
			}
			else if(naivete_array[loop].substring(0,7) =='http://' && mytag==4)
			{
				var temp="";
				for(var step=8;step<100;step++)
				{
					temp = naivete_array[loop].substring(step-1,step);
					if((temp<'\x30' && temp != '\x2e' && temp != '\x2d' && temp != '\x2c' && temp != '\x2f') || (temp>'\x7a') || ( (temp>'\x5a')&&(temp<'\x61')&&(temp != '\x5f') ))
					{
							mytag = step-1;
							break;
					}
			        }
			          
			        myurl=naivete_array[loop].substring(0,mytag);
			        tmpurl = naivete_array[loop].substring(7,mytag);
			        myurl_true=myurl.replace("\n","");
			        myurl_href='<a href='+myurl_true+'>'+tmpurl+'</a>';
			        if(loop>0)
			        {
					if(naivete_array[loop-1].substring(naivete_array[loop-1].length-1,naivete_array[loop-1].length)=='>' || naivete_array[loop-1].substring(naivete_array[loop-1].length-5,naivete_array[loop-1].length)=='href=')
					{
						result = result + naivete_array[loop];
					}
					else
					{
						result = result + naivete_array[loop].replace(myurl,myurl_href);
					}  
			        }  
			}
			else
			{
				result = result + naivete_array[loop];
                   	}
		}
	}
        return result;
}

//将出现的xx@xxx.xx替换成<a href=mailto:xx@xxx.xx>xx@xxx.xx</a>
function replaceMail(result)
{	
	var naivete_array =result.split("mailto:");
	var mytag=0;
	var mytag2=0;
	var checkflag;
	var oneArray;
	var hrefStr,urlStr;
	if (naivete_array.length >=0)
	{
		result=naivete_array[0];
        	for (loop=1; loop<naivete_array.length; loop++)
        	{
                	oneArray = naivete_array[loop];
                	//如果数组中出现"</a>",则认为此mailto出现在<a href...></a>中，不做处理
                	if ((oneArray.indexOf("</a>") >0 && oneArray.indexOf("<a")  <0) ||
                		(0<oneArray.indexOf("</a>")<oneArray.indexOf("<a")) )
                	{
                		result = result + "mailto:" + naivete_array[loop];
                		continue;	
                	}
                	
                	//查找空格位置，如果没有则查找第一个出现的中文或换行
                	mytag=oneArray.indexOf(' '); 
                	mytag2 = oneArray.indexOf("\n");
			for(var i=0;i<oneArray.length;i++ )
			{
				temp = oneArray.charAt(i);
				if(temp>='\x81' || temp=='\x29')
				{
					if (mytag2>0)
					{
						mytag2 = (mytag2>i) ? i :mytag2;	
					}
					else
					{
						mytag2 = i;
					}
					break;
				}
			}
                	
                	
                	if (mytag2 >0)
                	{
                		if(mytag<0)
                		{
                        		mytag = mytag2;
		        	}
				else
				{
					mytag = (mytag<mytag2) ? mytag : mytag2;	
				}
			}
                	/*if(mytag<0)
                	{
                        	mytag = oneArray.indexOf("\n");
				for(var i=0;i<oneArray.length;i++ )
				{
					temp = oneArray.charAt(i);
					if(temp>='\x81' || temp=='\x29')
					{
						if (mytag>0)
						{
							mytag = (mytag>i) ? i :mytag;	
						}
						else
						{
							mytag = i;
						}
						break;
					}
				}
		        	
		        }*/
			
			if (mytag <0 && loop == naivete_array.length-1)
			{
				mytag = oneArray.length;	
			}
			
			//判断在mytag前是否出现@
			if (mytag <0 || oneArray.indexOf('@')<0 || oneArray.indexOf('@')>mytag)
			{
				result = result + "mailto:" + naivete_array[loop];
                		continue;	
			}
			urlStr = oneArray.substring(0, mytag);
			hrefStr = "<a href=\"mailto:" + urlStr + "\">" + urlStr + "</a>";
			
			result = result + hrefStr + oneArray.substring(mytag);
		}
			
	}
        return result;
}

	//2004.11.23 修改http替换的函数
       //最后把"http://xxxx[ ]"替换为"<a href=http://xxx>xxx</a>"滤掉
function newReplaceHttp(result)
{	
	var naivete_array =result.split("http://");
	var mytag=0;
	var mytag2=0;
	var checkflag;
	var oneArray;
	var hrefStr,urlStr;
	if (naivete_array.length >=0)
	{
		result=naivete_array[0];
        	for (loop=1; loop<naivete_array.length; loop++)
        	{
                	oneArray = naivete_array[loop];
                	//如果数组中出现"</a>",则认为此http://出现在<a href...></a>中，不做处理
                	if (oneArray.indexOf("<img") >0 || (oneArray.indexOf("</a>") >0 && oneArray.indexOf("<a")  <0) ||
                		(0<oneArray.indexOf("</a>")<oneArray.indexOf("<a")) )
                	{
                		result = result + "http://" + naivete_array[loop];
                		continue;	
                	}
                	
                	//查找空格位置，如果没有则查找第一个出现的中文或换行的位置
                	mytag=oneArray.indexOf(' '); 
                	
                	mytag2 = oneArray.indexOf("\n");
			for(var i=0;i<oneArray.length;i++ )
			{
				temp = oneArray.charAt(i);
				if(temp>='\x81' || temp=='\x29')
				{
					if (mytag2>0)
					{
						mytag2 = (mytag2>i) ? i :mytag2;	
					}
					else
					{
						mytag2 = i;
					}
					break;
				}
			}
                	
                	
                	if (mytag2 >0)
                	{
                		if(mytag<0)
                		{
                        		mytag = mytag2;
		        	}
				else
				{
					mytag = (mytag<mytag2) ? mytag : mytag2;	
				}
			}
			
			
			if (mytag <0 && loop == naivete_array.length-1)
			{
				mytag = oneArray.length;	
			}
			
			if (mytag <0)
			{
				result = result + "http://" + naivete_array[loop];
                		continue;	
			}
			urlStr = oneArray.substring(0, mytag);
			hrefStr = "<a href=\"http://" + urlStr + "\" class=akey target=_blank>" + urlStr + "</a>";
			
			result = result + hrefStr + oneArray.substring(mytag);
		}
			
	}
        return result;
}


//-------------------------------------------------------------
// 功能:
// 参数:
// 返回值:
//-------------------------------------------------------------
function formattext2(text,addp)
{
        //首先把"<p>"、"<br>"、"</p>"、"<p>　　"全部滤掉
       var naivete_array =text.split("<p>　　");
       if (naivete_array.length >0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
	}
	
	naivete_array =text.split("<p>");
        if (naivete_array.length >0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }
        
	naivete_array =text.split("<br>　　");
        if (naivete_array.length >0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }


	naivete_array =text.split("<br>");
        if (naivete_array.length >0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }
        
	naivete_array =text.split("</p>");
        if (naivete_array.length >0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
         }

        oldlen = text.length;
		tmpstr = "";
		del = '1';
        for(i=0; i<oldlen; i++)
		{
			c1 = text.charAt(i);
			if (c1 == '\r')
			{
				del = '1';
				continue;
			}
			else
			{
                if(c1 > '\xff')
				{
					if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
					{
						continue;
					}
					else
					{
						if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
							del = '1';
						else
							del = '0';
						tmpstr += c1;
					}
				}
				else
				{
					if(c1 > '\x80')
					{
						del = '1';
						tmpstr += c1;
					}
					else
					{
						if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
						{
							continue;
						}
						else
						{
							if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
								del = '1';
							else
								del = '0';
							tmpstr += c1;
						}
					}
				}
			}
		}
		text = tmpstr;
        oldlen = text.length;
		tmpstr = "";
        for(i=0; i<oldlen; i++)
		{
			c1 = text.charAt(i);
			c2 = text.charAt(i+1);
			c3 = text.charAt(i+2);
			if (c1 == '\n' && c2 == '\n' && c3 == '\n')
				continue;
			tmpstr += c1;
		}
		text = tmpstr;
        oldlen = text.length;
        //result = (addp) ? "<p>" : "    "; //用全角空格代替
        result = (addp) ? "<p>　　" : "    "; //用全角空格代替
        count = 4;
		oneretn = 0;
        for(i=0; i<oldlen-1; i++)
        {
                c1 = text.charAt(i);
                c2 = text.charAt(i+1);
                c3 = text.charAt(i+2);
                c4 = text.charAt(i+3);
                c5 = text.charAt(i+4);
                c6 = text.charAt(i+5);
                c7 = text.charAt(i+6);
                c8 = text.charAt(i+7);
                if (c1 == '\n')
                {
					if (c2 == '\n')
					{
						if (oneretn == 1)
							result += (addp) ? "</p>\n<p>" : "\n";
						else
							result += (addp) ? "</p>\n\n<p>" : "\n\n";
						//result += "    ";
						result += "　　"; //用全角空格代替
						count = 4;
						i++;
						oneretn = 0;
					}
					continue;
                }
                else
                {
						if (c1 == " " && count == 0)
						{
							continue;
						}
						else
						{
							if ((c1 == '\xa1' && c2 == '\xa1') && count == 0)
							{
								i++;
								continue;
							}
						}
						oneretn = 0;
                        if(c1 > '\xff')
                        {
							result += c1;
							count+=1;
							if (c2 != '\n')
							{
								if (ishalfpun(c2))
								{
									result += c2;
									count+=1;
									i++;
								}
								else
								{
									if (c2 == '\x22' || c2== '\x27')
									{
										result += c2;
										count+=1;
										i++;
										if (c3 != '\n')
										{
											if (ishalfpun(c3))
											{
												result += c3;
												count+=1;
												i++;
											}
										}
										else
										{
											if (ishalfpun(c4))
											{
												result += c4;
												count+=1;
												i+=2;
											}
										}
									}
								}
							}
							else
							{
								if (ishalfpun(c3))
								{
									result += c3;
									count+=1;
									i+=2;
								}
								else
								{
									if (c3 == '\x22' || c3== '\x27')
									{
										result += c3;
										count+=1;
										i+=2;
										if (c4 != '\n')
										{
											if (ishalfpun(c4))
											{
												result += c4;
												count+=1;
												i+=2;
											}
										}
										else
										{
											if (ishalfpun(c5))
											{
												result += c5;
												count+=1;
												i+=3;
											}
										}
									}
								}
							}
                        }
                        else if(c1 > '\x80')
                        {
							if (c1 == '\xa1' && c2 == '\xa1')
							{
								i++;
								continue;
							}
							result += c1;
							result += c2;
							count+=2;
							i++;
							if (c3 == '\n')
							{
								if (c4 == '\xa1' && c5 == '\xa3') //。
								{
									result += c4;
									result += c5;
									count+=2;
									i+=3;
								}
								else
								{
									if (c4 == '\xa3' && c5 == '\xac') //，
									{
										result += c4;
										result += c5;
										count+=2;
										i+=3;
									}
									else
									{
										if (c4 == '\xa3' && c5 == '\xbb') //；
										{
											result += c4;
											result += c5;
											count+=2;
											i+=3;
										}
										else
										{
											if (c4 == '\xa3' && c5 == '\xba') //：
											{
												result += c4;
												result += c5;
												count+=2;
												i+=3;
											}
											else
											{
												if (c4 == '\xa3' && c5 == '\xa1') //！
												{
													result += c4;
													result += c5;
													count+=2;
													i+=3;
												}
												else
												{
													if ((c4 == '\xa1' && c5 == '\xb1') || (c4 == '\xa1' && c5 == '\xaf'))//”
													{
														result += c4;
														result += c5;
														count+=2;
														i+=3;
														if (c6 == '\n')
														{
															if (ispun(c7,c8) == 1)
															{
																result += c7;
																result += c8;
																count+=2;
																i+=3;
															}
														}
														else
														{
															if ((a =ispun(c6,c7)) == 1)
															{
																result += c6;
																result += c7;
																count+=2;
																i+=2;
															}
														}
													}
													else
													{
														if (c3 == '\xa3' && c4 == '\xbf') //？
														{
															result += c3;
															result += c4;
															count+=2;
															i+=3;
														}
														else
														{
															if (c3 == '\xa1' && c4 == '\xb7') //》
															{
																result += c3;
																result += c4;
																count+=2;
																i+=3;
															}
															else
															{
															}
														}
													}
												}
											}
										}
									}
								}
							}
							else
							{
								if (c3 == '\xa1' && c4 == '\xa3')
								{
									result += c3;
									result += c4;
									count+=2;
									i+=2;
								}
								else
								{
									if (c3 == '\xa3' && c4 == '\xac')
									{
										result += c3;
										result += c4;
										count+=2;
										i+=2;
									}
									else
									{
										if (c3 == '\xa3' && c4 == '\xbb')
										{
											result += c3;
											result += c4;
											count+=2;
											i+=2;
										}
										else
										{
											if (c3 == '\xa3' && c4 == '\xba')
											{
												result += c3;
												result += c4;
												count+=2;
												i+=2;
											}
											else
											{
												if (c3 == '\xa3' && c4 == '\xa1')
												{
													result += c3;
													result += c4;
													count+=2;
													i+=2;
												}
												else
												{
													if ((c3 == '\xa1' && c4 == '\xb1') || (c3 == '\xa1' && c4 == '\xaf'))
													{
														result += c3;
														result += c4;
														count+=2;
														i+=2;

														if (c5 == '\n')
														{
															if (ispun(c6,c7) == 1)
															{
																result += c6;
																result += c7;
																count+=2;
																i+=3;
															}
														}
														else
														{
															if (ispun(c5,c6) == 1)
															{
																result += c5;
																result += c6;
																count+=2;
																i+=2;
															}
														}
													}
													else
													{
														if (c3 == '\xa3' && c4 == '\xbf')
														{
															result += c3;
															result += c4;
															count+=2;
															i+=2;
														}
														else
														{
															if (c3 == '\xa1' && c4 == '\xb7')
															{
																result += c3;
																result += c4;
																count+=2;
																i+=2;
															}
															else
															{
															}
														}
													}
												}
											}
										}
									}
								}
							}
                        }
                        else
                        {
							result += c1;
                            count++;
                        }
                }
                if(count>=57)
                {
					result += (addp) ? "\n" : "\n";
					oneretn = 1;
                    count = 0;
                }
        }
        if(i<oldlen)
                result += text.charAt(i);
        if(addp)
                result +="</p>\n";


	//"<p>　　　　"=>"<p>　　"
       var naivete_array =result.split("<br>　　　　");
       if (naivete_array.length >1)
	{
	      result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 if(result != ""){ result = result +"<br>　　"+ naivete_array[loop];}
                 else{ result = naivete_array[loop];}
                 }
	}
       var naivete_array =result.split("<p>　　　　");
       if (naivete_array.length >1)
	{
	      result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                   if(naivete_array[loop] !=""){
                   result = result +"<p>　　"+ naivete_array[loop];
                   }
                 }
	}
	
        //最后把结尾处的"<br>　　<br>"滤掉
       var naivete_array =result.split("<br>　　<br>");
       if (naivete_array.length >1)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result + naivete_array[loop];
                 }
	}


        //最后把结尾处的"<p>　　</p>"滤掉
       var naivete_array =result.split("<p>　　</p>");
       if (naivete_array.length >1)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result + naivete_array[loop];
                 }
	}


        //最后把结尾处的"　　<br>"滤掉
       var naivete_array =result.split("　　<br>");
       if (naivete_array.length >1)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result + naivete_array[loop];
                 }
	}


        return result;                                                      
}


//-------------------------------------------------------------
// 功能:
// 参数:
// 返回值:
//-------------------------------------------------------------
function formattext1(text,addp)
{
       //var text=text_obj.value; 
        //首先把"<p>"、"<br>"、"</p>"、"<p>　　"、"<br>　　"全部滤掉
       var naivete_array =text.split("<p>　　");
       if (naivete_array.length >=0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
	}
	
	naivete_array =text.split("<p>");
        if (naivete_array.length >=0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }
        
	naivete_array =text.split("<br>　　");
        if (naivete_array.length >=0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }

	naivete_array =text.split("<br>");
        if (naivete_array.length >=0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
        }
        
	naivete_array =text.split("</p>");
        if (naivete_array.length >=0)
	{
	text="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 text = text + naivete_array[loop];
                 }
         }

        oldlen = text.length;
		tmpstr = "";
		del = '1';
        for(i=0; i<oldlen; i++)
		{
			c1 = text.charAt(i);
			if (c1 == '\r')
			{
				del = '1';
				continue;
			}
			else
			{
                if(c1 > '\xff')
				{
					if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
					{
						continue;
					}
					else
					{
						if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
							del = '1';
						else
							del = '0';
						tmpstr += c1;
					}
				}
				else
				{
					if(c1 > '\x80')
					{
						del = '1';
						tmpstr += c1;
					}
					else
					{
						if ((c1 == ' ' && (del == '1')) || c1 == '\x09')
						{
							continue;
						}
						else
						{
							if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
								del = '1';
							else
								del = '0';
							tmpstr += c1;
						}
					}
				}
			}
		}
		text = tmpstr;
        oldlen = text.length;
		tmpstr = "";
        for(i=0; i<oldlen; i++)
		{
			c1 = text.charAt(i);
			c2 = text.charAt(i+1);
			c3 = text.charAt(i+2);
			if (c1 == '\n' && c2 == '\n' && c3 == '\n')
				continue;
			tmpstr += c1;
		}
		text = tmpstr;
        oldlen = text.length;
        //result = (addp) ? "<p>" : "    "; //用全角空格代替
        result = (addp) ? "<br>　　" : "    "; //用全角空格代替
        count = 4;
		oneretn = 0;
        for(i=0; i<oldlen-1; i++)
        {
                c1 = text.charAt(i);
                c2 = text.charAt(i+1);
                c3 = text.charAt(i+2);
                c4 = text.charAt(i+3);
                c5 = text.charAt(i+4);
                c6 = text.charAt(i+5);
                c7 = text.charAt(i+6);
                c8 = text.charAt(i+7);
                if (c1 == '\n')
                {
					if (c2 == '\n')
					{
						if (oneretn == 1)
							result += (addp) ? "\n<br>" : "\n";
						else
							result += (addp) ? "\n\n<br><br>" : "\n\n";
						//result += "    ";
						result += "　　"; //用全角空格代替
						count = 4;
						i++;
						oneretn = 0;
					}
					continue;
                }
                else
                {
						if (c1 == " " && count == 0)
						{
							continue;
						}
						else
						{
							if ((c1 == '\xa1' && c2 == '\xa1') && count == 0)
							{
								i++;
								continue;
							}
						}
						oneretn = 0;
                        if(c1 > '\xff')
                        {
							result += c1;
							count+=1;
							if (c2 != '\n')
							{
								if (ishalfpun(c2))
								{
									result += c2;
									count+=1;
									i++;
								}
								else
								{
									if (c2 == '\x22' || c2== '\x27')
									{
										result += c2;
										count+=1;
										i++;
										if (c3 != '\n')
										{
											if (ishalfpun(c3))
											{
												result += c3;
												count+=1;
												i++;
											}
										}
										else
										{
											if (ishalfpun(c4))
											{
												result += c4;
												count+=1;
												i+=2;
											}
										}
									}
								}
							}
							else
							{
								if (ishalfpun(c3))
								{
									result += c3;
									count+=1;
									i+=2;
								}
								else
								{
									if (c3 == '\x22' || c3== '\x27')
									{
										result += c3;
										count+=1;
										i+=2;
										if (c4 != '\n')
										{
											if (ishalfpun(c4))
											{
												result += c4;
												count+=1;
												i+=2;
											}
										}
										else
										{
											if (ishalfpun(c5))
											{
												result += c5;
												count+=1;
												i+=3;
											}
										}
									}
								}
							}
                        }
                        else if(c1 > '\x80')
                        {
							if (c1 == '\xa1' && c2 == '\xa1')
							{
								i++;
								continue;
							}
							result += c1;
							result += c2;
							count+=2;
							i++;
							if (c3 == '\n')
							{
								if (c4 == '\xa1' && c5 == '\xa3') //。
								{
									result += c4;
									result += c5;
									count+=2;
									i+=3;
								}
								else
								{
									if (c4 == '\xa3' && c5 == '\xac') //，
									{
										result += c4;
										result += c5;
										count+=2;
										i+=3;
									}
									else
									{
										if (c4 == '\xa3' && c5 == '\xbb') //；
										{
											result += c4;
											result += c5;
											count+=2;
											i+=3;
										}
										else
										{
											if (c4 == '\xa3' && c5 == '\xba') //：
											{
												result += c4;
												result += c5;
												count+=2;
												i+=3;
											}
											else
											{
												if (c4 == '\xa3' && c5 == '\xa1') //！
												{
													result += c4;
													result += c5;
													count+=2;
													i+=3;
												}
												else
												{
													if ((c4 == '\xa1' && c5 == '\xb1') || (c4 == '\xa1' && c5 == '\xaf'))//”
													{
														result += c4;
														result += c5;
														count+=2;
														i+=3;
														if (c6 == '\n')
														{
															if (ispun(c7,c8) == 1)
															{
																result += c7;
																result += c8;
																count+=2;
																i+=3;
															}
														}
														else
														{
															if ((a =ispun(c6,c7)) == 1)
															{
																result += c6;
																result += c7;
																count+=2;
																i+=2;
															}
														}
													}
													else
													{
														if (c3 == '\xa3' && c4 == '\xbf') //？
														{
															result += c3;
															result += c4;
															count+=2;
															i+=3;
														}
														else
														{
															if (c3 == '\xa1' && c4 == '\xb7') //》
															{
																result += c3;
																result += c4;
																count+=2;
																i+=3;
															}
															else
															{
															}
														}
													}
												}
											}
										}
									}
								}
							}
							else
							{
								if (c3 == '\xa1' && c4 == '\xa3')
								{
									result += c3;
									result += c4;
									count+=2;
									i+=2;
								}
								else
								{
									if (c3 == '\xa3' && c4 == '\xac')
									{
										result += c3;
										result += c4;
										count+=2;
										i+=2;
									}
									else
									{
										if (c3 == '\xa3' && c4 == '\xbb')
										{
											result += c3;
											result += c4;
											count+=2;
											i+=2;
										}
										else
										{
											if (c3 == '\xa3' && c4 == '\xba')
											{
												result += c3;
												result += c4;
												count+=2;
												i+=2;
											}
											else
											{
												if (c3 == '\xa3' && c4 == '\xa1')
												{
													result += c3;
													result += c4;
													count+=2;
													i+=2;
												}
												else
												{
													if ((c3 == '\xa1' && c4 == '\xb1') || (c3 == '\xa1' && c4 == '\xaf'))
													{
														result += c3;
														result += c4;
														count+=2;
														i+=2;

														if (c5 == '\n')
														{
															if (ispun(c6,c7) == 1)
															{
																result += c6;
																result += c7;
																count+=2;
																i+=3;
															}
														}
														else
														{
															if (ispun(c5,c6) == 1)
															{
																result += c5;
																result += c6;
																count+=2;
																i+=2;
															}
														}
													}
													else
													{
														if (c3 == '\xa3' && c4 == '\xbf')
														{
															result += c3;
															result += c4;
															count+=2;
															i+=2;
														}
														else
														{
															if (c3 == '\xa1' && c4 == '\xb7')
															{
																result += c3;
																result += c4;
																count+=2;
																i+=2;
															}
															else
															{
															}
														}
													}
												}
											}
										}
									}
								}
							}
                        }
                        else
                        {
							result += c1;
                            count++;
                        }
                }
                if(count>=40)
                {
					result += (addp) ? "\n" : "\n";//result += (addp) ? "\n" : "\n";
					oneretn = 1;
                    count = 0;
                }
        }
        if(i<oldlen)
                result += text.charAt(i);
        if(addp)
                result +="<br>"; //"</p>\n";
        
        //text_obj.value = result;                 

	//"<p>　　　　"=>"<p>　　"
       var naivete_array =result.split("<br>　　　　");
       if (naivete_array.length >=0)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result +"<br>　　"+ naivete_array[loop];
                 }
	}
	
        //最后把结尾处的"<br>　　<br>"滤掉
       var naivete_array =result.split("<br>　　<br>");
       if (naivete_array.length >=0)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result + naivete_array[loop];
                 }
	}

        //最后把结尾处的"　　<br>"滤掉
       var naivete_array =result.split("　　<br>");
       if (naivete_array.length >=0)
	{
	result="";
        for (loop=0; loop < naivete_array.length;loop++)
                 {
                 result = result + naivete_array[loop];
                 }
	}

        
        return result;                                                      
}
