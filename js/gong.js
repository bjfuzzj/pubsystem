////////////////////////////////
///
///Html preview
///
////////////////////////////////
function text_preview(text)
{
	//win2=open("","preview_html","toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes");
	//win2=open("","preview_html","toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
	//alert(text);
	win2=open("","preview_html");//,"toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
	win2.document.open("text/html");
	agent=navigator.appName;
	//alert(agent);
	
	win2.document.writeln("<html>");
	win2.document.writeln("<head>");
	win2.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html;charset=gb2312\">");
	win2.document.writeln("</head>");
	win2.document.writeln("<body>");
	
	if(agent == 'Netscape')
	{
		text=escape(text);
		re = /%/gi;
		newtext=text.replace(re, "\\x");
		
		text =eval ( '"' + newtext +'"');
		
		win2.document.writeln(text);
	}
	else
	{
		win2.document.writeln(text);
	}
	win2.document.writeln("</body>");
	win2.document.writeln("</html>");
	win2.document.close();
	win2.focus();
	return 1;
	
}

////////////////////////////////
///
///Format text
///
////////////////////////////////
function ispun(c1,c2)
{
	if ((c1 == '\xa1' && c2 == '\xa3') || (c1 == '\xa3' && c2 == '\xac')|| (c1 == '\xa3' && c2 == '\xbb') || (c1 == '\xa3' && c2 == '\xba') || (c1 == '\xa3' && c2 == '\xa1') || (c1 == '\xa1' && c2 == '\xb1') || (c3 == '\xa3' && c2 == '\xbf') || (c3 == '\xa1' && c2 == '\xb7'))
	return 1;
	return 0;
}

function ishalfpun(c2)
{
	if (c2 == '\x2c' || c2 =='\x2e' || c2 =='\x3b' || c2 =='\x2e' || c2 =='\x3a' || c2 =='\x21' || c2 =='\x3f' || c2 =='\x3e')
	return 1;
	return 0;
}

function formattext(text,addp)
{
	sbcarray = new Array("ａ","ｂ","ｃ","ｄ","ｅ","ｆ","ｇ","ｈ","ｉ","ｊ","ｋ","ｌ","ｍ","ｎ","ｏ","ｐ","ｑ","ｒ","ｓ","ｔ","ｕ","ｖ","ｗ","ｘ","ｙ","ｚ","Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ","Ｎ","Ｏ","Ｐ","Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ","．");
	dbcarray = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",".");
	headarray = new Array("本报讯","消息","日电");
	var flag=0;
	//首先把"<p>"、"<br>"、"</p>"、"<p>　　"全部滤掉
	var naivete_array =text.split("<p>　　");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("<p>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("<P>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("<br>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("<BR>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("</p>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	naivete_array =text.split("</P>");
	if (naivete_array.length >=0){
		text="";
		for (loop=0; loop < naivete_array.length;loop++){
			text = text + naivete_array[loop];
		}
	}
	
	oldlen = text.length;
	
	//modified by jxz 2000.6.13
	//以下为清除文中全角字符间的空格,且保留半角字符间的空格
	result1 = "";
	spaceflg = 0;
	for(i=0; i<oldlen; i++){
		c1 = text.charAt(i);
		
		codevalue1 = text.charCodeAt(i);
		c2 = text.charAt(i+1);
		codevalue2 = text.charCodeAt(i+1);
		c3 = text.charAt(i+2);
		codevalue3 = text.charCodeAt(i+2);
		if( (codevalue1 <127) && (codevalue3 <127))
		spaceflg=0;
		else
		spaceflg=1;
		
		result1 += c1;
		if((c1 != ' ') && (c1 != '　') &&  (c1 != ' ') && (c3 != '　')){
			if( (spaceflg == 1) && (c2 == ' ') || (c2 == '　') ){
				//alert("spaceflg="+spaceflg+"c1="+c1+" codevalue1="+codevalue1+"c3="+c3+" codevalue3="+codevalue3+"i="+i);
				i+=1;
			}
		}
	}
	
	text = result1;
	oldlen = text.length;
	//modified by jxz 2000.6.13
	
	
	firstflg = 0; //清除文章头部空格回车标志
	linenumber = 0;
	lcount = 0;
	
	tmpstring = "";
	oneline =1;
	for(i=0; i<oldlen; i++){
		c1 = text.charAt(i);
		
		tmpstring +=c1;
		
		if((c1 != '\n') && (c1!=' ') && (c1!='　')){
			firstflg=1;
			linenumber =1;
		}
		if(firstflg == 1){
			if(c1 == '\n'){
				linenumber=0;
				dcount=0;
			}
			if(linenumber==1){
				if(c1!='\r'){
					if(c1 > '\xff')
					lcount +=2;
					else {
						lcount+=1;
					}
				}
				
			}
			if(linenumber == 0){
				
				for(j=i;j<=i+4;j++){
					if(text.charAt(j) == '　'){
						dcount +=2;
						}else if(text.charAt(j) == '\x20'){
						dcount += 1;
					}
				}
				if(navigator.appName.indexOf("Netscape") != 0){
					if(dcount >= 3){
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
	for(i=0; i<oldlen; i++){
		c1 = text.charAt(i);
		
		if (c1 == '\r'){
			del = '1';
			continue;
			}else{
			
			if((c1 != '\n') && (c1!=' ') && (c1!='　')){
				firstflg=1;
			}
			
			if(firstflg == 0){
				continue;
			}
			
			if(c1 > '\xff'){
				if ((c1 == ' ' && (del == '1')) || c1 == '\x09'){
					continue;
					}else{
					if (c1 == ' ' || c1 == '\n' || c1 == '\x09')
					del = '1';
					else
					del = '0';
					tmpstr += c1;
				}
				}else{
				if(c1 > '\x80'){
					del = '1';
					tmpstr += c1;
					}else{
					if ((c1 == ' ' && (del == '1')) || c1 == '\x09'){
						continue;
						}else{
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
	if((text.charAt(0) != '\n') ||  (text.charAt(0) != '　')){
		result = ""; //用全角空格代替
		}else{
		result = (addp) ? "<p>　　" : "    "; //用全角空格代替
		
	}
	count = 4;
	oneretn = 0;
	for(i=0; i<oldlen-1; i++){
		c1 = text.charAt(i);
		c2 = text.charAt(i+1);
		c3 = text.charAt(i+2);
		c4 = text.charAt(i+3);
		c5 = text.charAt(i+4);
		c6 = text.charAt(i+5);
		c7 = text.charAt(i+6);
		c8 = text.charAt(i+7);
		if (c1 == '\n'){
			
			if (c2 == '\n'){
				
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
			}else{
			if (c1 == " " && count == 0){
				continue;
				}else{
				if ((c1 == '\xa1' && c2 == '\xa1') && count == 0){
					i++;
					continue;
				}
			}
			oneretn = 0;
			if(c1 > '\xff'){
				if(c1 == '）'){
					result += ')';
					count+=1;
					continue;
					
				}
				if(c1 == '（'){
					result += '(';
					count+=1;
					continue;
				}
				if(c1 == '「'){
					result += '“';
					count+=1;
					continue;
				}
				
				if(c1 == '」'){
					result += '”';
					count+=1;
					continue;
				}
				if(c1 == '■'){
					result += '-';
					count += 1;
					continue;
				}
				
				if(c1 == '．'){
					result += '.';
					count += 1;
					continue;
				}
				if(c1 == '－'){
					result += '-';
					count += 1;
					continue;
				}
				if(c1 == '１'){
					result += '1';
					count += 1;
					continue;
				}
				
				if(c1 == '２'){
					result += '2';
					count += 1;
					continue;
				}
				
				if(c1 == '３'){
					result += '3';
					count += 1;
					continue;
				}
				
				if(c1 == '４'){
					result += '4';
					count += 1;
					continue;
				}
				
				if(c1 == '５'){
					result += '5';
					count += 1;
					continue;
				}
				
				if(c1 == '６'){
					result += '6';
					count += 1;
					continue;
				}
				
				if(c1 == '７'){
					result += '7';
					count += 1;
					continue;
				}
				if(c1 == '８'){
					result += '8';
					count += 1;
					continue;
				}
				
				if(c1 == '９'){
					result += '9';
					count += 1;
					continue;
				}
				
				if(c1 == '０'){
					result += '0';
					count += 1;
					continue;
				}
				
				if(c1 == ''){
					result += '0';
					count += 1;
					continue;
				}
				
				if(c1 == '％'){
					result += '%';
					count += 1;
					continue;
				}
				
				for(var cstep=0;cstep<52;cstep++){
					if(c1 == sbcarray[cstep]){
						flag = 1;
						break;
					}
				}
				if(flag == 1){
					result += dbcarray[cstep];
					count += 1;
					flag = 0;
					continue;
				}
				
				result += c1;
				count+=1;
				if (c2 != '\n'){
					if (ishalfpun(c2)){
						result += c2;
						count+=1;
						i++;
						}else{
						if (c2 == '\x22' || c2== '\x27'){
							result += c2;
							count+=1;
							i++;
							if (c3 != '\n'){
								if (ishalfpun(c3)){
									result += c3;
									count+=1;
									i++;
								}
								}else{
								if (ishalfpun(c4)){
									result += c4;
									count+=1;
									i+=2;
								}
							}
						}
					}
					}else{
					if (ishalfpun(c3)){
						result += c3;
						count+=1;
						i+=2;
						}else{
						if (c3 == '\x22' || c3== '\x27'){
							result += c3;
							count+=1;
							i+=2;
							if (c4 != '\n'){
								if (ishalfpun(c4)){
									result += c4;
									count+=1;
									i+=2;
								}
								}else{
								if (ishalfpun(c5)){
									result += c5;
									count+=1;
									i+=3;
								}
							}
						}
					}
				}
				}else if(c1 > '\x80'){
				if (c1 == '\xa1' && c2 == '\xa1'){//　space
					i++;
					continue;
				}
				result += c1;
				result += c2;
				count+=2;
				i++;
				if (c3 == '\n'){
					if (c4 == '\xa1' && c5 == '\xa3'){//。
						result += c4;
						result += c5;
						count+=2;
						i+=3;
						}else{
						if (c4 == '\xa3' && c5 == '\xac'){ //，
							result += c4;
							result += c5;
							count+=2;
							i+=3;
							}else{
							if (c4 == '\xa3' && c5 == '\xbb'){ //；
								result += c4;
								result += c5;
								count+=2;
								i+=3;
								}else{
								if (c4 == '\xa3' && c5 == '\xba'){ //：
									result += c4;
									result += c5;
									count+=2;
									i+=3;
									}else{
									if (c4 == '\xa3' && c5 == '\xa1'){ //！
										result += c4;
										result += c5;
										count+=2;
										i+=3;
										}else{
										if ((c4 == '\xa1' && c5 == '\xb1') || (c4 == '\xa1' && c5 == '\xaf')){ //”
											result += c4;
											result += c5;
											count+=2;
											i+=3;
											if (c6 == '\n'){
												if (ispun(c7,c8) == 1){
													result += c7;
													result += c8;
													count+=2;
													i+=3;
												}
												}else{
												if ((a =ispun(c6,c7)) == 1){
													result += c6;
													result += c7;
													count+=2;
													i+=2;
												}
											}
											}else{
											if (c3 == '\xa3' && c4 == '\xbf'){ //？
												result += c3;
												result += c4;
												count+=2;
												i+=3;
												}else{
												if (c3 == '\xa1' && c4 == '\xb7'){ //》
													result += c3;
													result += c4;
													count+=2;
													i+=3;
													}else{
												}
											}
										}
									}
								}
							}
						}
					}
					}else{
					if (c3 == '\xa1' && c4 == '\xa3'){ //。
						result += c3;
						result += c4;
						count+=2;
						i+=2;
						}else{
						if (c3 == '\xa3' && c4 == '\xac'){ //，
							result += c3;
							result += c4;
							count+=2;
							i+=2;
							}else{
							if (c3 == '\xa3' && c4 == '\xbb'){//；
								result += c3;
								result += c4;
								count+=2;
								i+=2;
								}else{
								if (c3 == '\xa3' && c4 == '\xba'){//：
									result += c3;
									result += c4;
									count+=2;
									i+=2;
									}else{
									if (c3 == '\xa3' && c4 == '\xa1'){//！
										result += c3;
										result += c4;
										count+=2;
										i+=2;
										}else{
										if ((c3 == '\xa1' && c4 == '\xb1') || (c3 == '\xa1' && c4 == '\xaf')){//” or  ’
											result += c3;
											result += c4;
											count+=2;
											i+=2;
											if (c5 == '\n'){
												if (ispun(c6,c7) == 1){
													result += c6;
													result += c7;
													count+=2;
													i+=3;
												}
												}else{
												if (ispun(c5,c6) == 1){
													result += c5;
													result += c6;
													count+=2;
													i+=2;
												}
											}
											}else{
											if (c3 == '\xa3' && c4 == '\xbf'){  //？
												result += c3;
												result += c4;
												count+=2;
												i+=2;
												}else{
												if (c3 == '\xa1' && c4 == '\xb7'){//  》
													result += c3;
													result += c4;
													count+=2;
													i+=2;
													}else{
												}
											}
										}
									}
								}
							}
						}
					}
				}
				}else{
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
	if (naivete_array.length >1){
		result="";
		for (loop=0; loop < naivete_array.length;loop++){
			if(result != ""){ result = result +"<br>　　"+ naivete_array[loop];}
			else{ result = naivete_array[loop];}
		}
	}
	var naivete_array =result.split("<p>　　　　");
	if (naivete_array.length >1){
		result="";
		for (loop=0; loop < naivete_array.length;loop++){
			if(naivete_array[loop] !=""){
				result = result +"<p>　　"+ naivete_array[loop];
			}
		}
		}else{
		result = "<p>　　"+naivete_array;
	}
	
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
	
	
	
	//最后把"http://xxxx[ ]"替换为"<a href=http://xxx>xxx</a>"滤掉
	var naivete_array =result.split("http:");
	var mytag=0;
	var checkflag;
	if (naivete_array.length >=0){
		result="";
		for (loop=0; loop < naivete_array.length;loop++){
			mytag=naivete_array[loop].indexOf(' ');
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".html");
				if(mytag>0){mytag+=5;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf("\n.html");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".\nhtml");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".h\ntml");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".ht\nml");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".htm\nl");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".shtml");
				if(mytag>0){mytag+=6;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf("\n.shtml");
				if(mytag>0){mytag+=7;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".\nshtml");
				if(mytag>0){mytag+=7;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".s\nhtml");
				if(mytag>0){mytag+=7;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".sh\ntml");
				if(mytag>0){mytag+=7;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".sht\nml");
				if(mytag>0){mytag+=7;}
			}
			if(mytag<0){
				mytag=naivete_array[loop].indexOf(".shtm\nl");
				if(mytag>0){mytag+=7;}
			}
			if(naivete_array[loop].substring(0,2) =='//'){
				naivete_array[loop]='http:'+naivete_array[loop];
				mytag=mytag+5;
			}
			checkflag = 0;
			if(naivete_array[loop].substring(0,7) =='http://' && mytag>5 && mytag<100){
				for(var step1=1;step1<mytag;step1++ ){
					temp = naivete_array[loop].substring(step1-1,step1);
					if((temp>'\x7f')){
						checkflag = 1;
						break;
					}
				}
				
				if(checkflag == 1){
					var temp="";
					for(step=8;step<mytag;step++){
						temp = naivete_array[loop].substring(step-1,step);
						if((temp<'\x30' && temp != '\x2e' && temp != '\x2d' && temp != '\x2c') || (temp>'\x7a') || ( (temp>'\x5a')&&(temp<'\x61')&&(temp != '\x5f') )){
							mytag = step-1;
							break;
						}
					}
				}
				
				myurl=naivete_array[loop].substring(0,mytag);
				tmpurl = naivete_array[loop].substring(7,mytag);
				myurl_true=myurl.replace("\n","");
				myurl_href='<a href='+myurl_true+'>'+tmpurl+'</a>';
				if(loop>0){
					if(naivete_array[loop-1].substring(naivete_array[loop-1].length-1,naivete_array[loop-1].length)=='>' || naivete_array[loop-1].substring(naivete_array[loop-1].length-5,naivete_array[loop-1].length)=='href='){
						result = result + naivete_array[loop];
						}else{
						result = result + naivete_array[loop].replace(myurl,myurl_href);
					}
				}
				}else if(naivete_array[loop].substring(0,7) =='http://' && mytag==4){
				var temp="";
				for(var step=8;step<100;step++){
					temp = naivete_array[loop].substring(step-1,step);
					if((temp<'\x30' && temp != '\x2e' && temp != '\x2d' && temp != '\x2c') || (temp>'\x7a') || ( (temp>'\x5a')&&(temp<'\x61')&&(temp != '\x5f') )){
						mytag = step-1;
						break;
					}
				}
				
				myurl=naivete_array[loop].substring(0,mytag);
				tmpurl = naivete_array[loop].substring(7,mytag);
				myurl_true=myurl.replace("\n","");
				myurl_href='<a href='+myurl_true+'>'+tmpurl+'</a>';
				if(loop>0){
					if(naivete_array[loop-1].substring(naivete_array[loop-1].length-1,naivete_array[loop-1].length)=='>' || naivete_array[loop-1].substring(naivete_array[loop-1].length-5,naivete_array[loop-1].length)=='href='){
						result = result + naivete_array[loop];
						}else{
						result = result + naivete_array[loop].replace(myurl,myurl_href);
					}
				}
				
				}else{
				result = result + naivete_array[loop];
			}
		}
	}
	
	
	
	return result;
}


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



function adjust(f)
{
	document.news.state.value = "正在排版。。。";
	document.news._body.value = formattext(document.news._body.value,1);
	document.news.state.value = "排版结束！";
}




////////////////////////////////
///
///open a new navigator window
///
///js_callpage("http://192.168.23.200:8344/publish/sys_entry.html","Sina_publish_system","toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes");
////////////////////////////////
function js_callpage(htmlurl,title,winattrib) {
	if(navigator.appName.indexOf("Netscape") != -1)
	{
		var newwin=window.open(htmlurl,title,winattrib);
		//self.window.close();
		//self.window=null;
		newwin.focus();
	}
	else
	{
		if(self.newwin != null)
		{
			self.newwin.close();
			newwin = null;
			var   newwin=window.open(htmlurl,title,winattrib);
		}
		else
		{
			var  newwin=window.open(htmlurl,title,winattrib);
		}
		//self.window=null;
		//self.window.close();
		newwin.focus();
	}
	
	return true;
}




////////////////////////////////
//
//HomepageSetup
//
////////////////////////////////
function HomepageSetup(key,value,msg) {
	
	//if(key == 'homepage'){key='sp_homepage';}
	
	var answer = confirm(msg);
	
	if (answer) {
		toCookie(key,value,1);
		//refresh menubar
		//alert(parent.length);
		if(parent.length >0){parent.frames[0].location.reload();}
	}
	//  else {
	//         toCookie(key,'$publish_server_homepage');
	//  }
	
	
	//key1="operator_id";
	//value1="$operator_id";
	
	//toCookie(key1,value1);
	
	return false;
}




////////////////////////////////
//
//Set Cookie
//expire(year_num)
///////////////////////////////

function toCookie(cookiename,cookievalue,expire)
{
	document.cookie = '';
	if(expire != 0)
	{
		expr =makeYearExpDate(expire) ;
	}
	cookiename = fixSep(cookiename) ;
	astr = fixSep(cookievalue) ;
	if(expire != 0)
	{
		astr = cookiename + '=' + astr + ';expires=' + expr + ';path=/'  + '';
	}
	else
	{
		astr = cookiename + '=' + astr + ';path=/'  + '';
	}
	document.cookie=astr ;
	return false;
}


function makeYearExpDate(yr)
{
	var expire = new Date ();
	expire.setTime (expire.getTime() + ((yr *365) *24 * 60 * 60 * 1000));
	expire = expire.toGMTString() ;
	return expire;
}

function fixSep(what)
{
	n=0 ;
	while ( n >= 0 ) {
		n = what.indexOf(';',n) ;
		if (n < 0) return what ;
		else {
			what = what.substring(0,n) + escape(';') + what.substring(n+1,what.length) ;
			n++ ;
		}
	}
	return what ;
}

function y2k(number)
{
	return (number < 1000) ? number + 1900 : number;
}

function getTime()
{
	
	var LocalNow = new Date();
	
	var AbsoluteNow = new Date();
	
	AbsoluteNow.setTime(LocalNow.getTime() + LocalNow.getTimezoneOffset()*60);
	
	//alert(LocalNow.getTimezoneOffset());
	
	var PekingDate = new Date();
	
	PekingDate.setTime(AbsoluteNow.getTime()+8*3600);
	
	var thisYear = PekingDate.getYear();
	
	var thisMonth = PekingDate.getMonth() + 1;
	
	var thisDay = PekingDate.getDate();
	
	var str = y2k(thisYear)+'年';
	
	if (thisMonth < 10)
	
	str += '0';
	
	str += thisMonth + '月';
	
	if (thisDay < 10)
	
	str += '0';
	
	str += thisDay + '日';
	
	return str;
	
}






//*****************************************************



function getEscapeValue(val)
{
	val=escape(val);
	re = /%/gi;
	//val=val.replace(re, "\\x");
	val=val.replace(re, "\\");
	val =eval ("\"" + val +"\"");
	return val;
}


function do_Rel_Result(agent,cgi,form,target,title)
{
	var collection= form.elements;
	var browse;
	var strName;
	var url;
	var keyArray = new Array();
	url = cgi;
	regExp = new RegExp(target+"_","ig");
	if(collection != null)
	{
		var j = 0;
		for(var i=0;i<collection.length; i++)
		{
			var ele = form.elements[i];
			strName = ele.name;
			if((strName.indexOf("_rel_result_") != -1) && (strName.indexOf(target) != -1))
			{
				if(ele.value == "")
				{
					alert("请完成输入!");
					ele.focus();
					return false;
				}
				keyArray[j] = strName;
				j++;
			}
		}
	}
	var newwin;
	var key_element;
	var screen_width = window.screen.width;
	var screen_height = window.screen.height;
	var left = (screen_width - 600)/2;
	var top = (screen_height - 400)/2;
	var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
	property = property + ",top="+top+",left="+left;
	var myname;
	if(navigator.appName.indexOf("Netscape") != -1)
	{
		newwin=window.open("",null,property);
	}
	else
	{
		if(self.newwin != null)
		{
			self.newwin.close();
			newwin = null;
			newwin=window.open("",null,property);
		}
		else
		{
			newwin=window.open("",null,property);
		}
	}
	b_agent=navigator.appName;
	if(b_agent == 'Netscape')
	{
		title = getEscapeValue(title);
		browse = "Netscape";
	}
	else
	{
		browse = "IE";
	}

	newwin.focus();
	newwin.document.open("text/html");
	newwin.document.writeln("<html>");
	newwin.document.writeln("<head>");
	newwin.document.writeln("<title>" + title + "</title>");
	newwin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
	newwin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">");
	newwin.document.writeln("</head>");
	newwin.document.writeln("<body>");
	newwin.document.writeln("<form method=post name=this_form action=\""+ agent +"\">");
	newwin.document.writeln("Please Waiting.....");
	//修正2000-4-30出现的Submit时的Bug:取不出review_date
	newwin.document.writeln("<input type=hidden name=\"test\" value=\"1000\">");

	
	for(i=0;i<keyArray.length;i++)
	{
		var current_object;
		key_element = new String(keyArray[i]);
		key = key_element.replace(/_rel_result_/,"");
		key = key.replace(regExp,"");
		current_object = form.elements[key_element];
		type = current_object.type;

		if(type == "select-one")
		{
			key_value = current_object.options[current_object.selectedIndex].value;
		}
		else
		{
			key_value = current_object.value;
			if(type == "hidden")
			{
				var form_element = "";
				if(b_agent == 'Netscape')
				{
					var start_pos = key_value.indexOf("${");
					var end_pos = key_value.indexOf("}");
					if((start_pos != -1) && (end_pos != -1) && (end_pos > start_pos + 2))
					{
						form_element = key_value.substring(start_pos+2,end_pos);
					}
				}
				else
				{
					var re = new RegExp("^\\${(.*)}$");
					var arr = re.exec(key_value);
					form_element = RegExp.$1;
				}
				if(form_element != "")
				{
					form_element = "_fieldvalue_"+form_element;
					var my_current_object = form.elements[form_element];
					var form_element_type = my_current_object.type;
					if(form_element_type == "select-one")
					{
						key_value = my_current_object.options[my_current_object.selectedIndex].value;
					}
					else
					{
						key_value = my_current_object.value;
					}
				}
			}
		}
		if(b_agent == "Netscape")
		{
			key_value = key_value.replace(/\"/g,"&quot;");
			key_value = getEscapeValue(key_value);
		}
		else
		{
			if(key_value != "")
			{
				// key_value = document.myApplet.getEncodeValue(key_value);
				// key_value = myApplet.getEncodeValue(key_value);
				key_value = escape(key_value);
			}
		}
		newwin.document.writeln("<input type=hidden name=\"" + key +  "\" value=\"" + key_value + "\">");
	}
	target_value = form.elements[target].value;
	newwin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");
	newwin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + target + "\">");
	if(b_agent == "Netscape")
	{
		target_value = target_value.replace(/\"/g,"&quot;");
		target_value = getEscapeValue(target_value);
	}
	else
	{
		if(target_value != "")
		{
			// target_value = document.myApplet.getEncodeValue(target_value);
			target_value = escape(target_value);
		}
	}
	newwin.document.writeln("<input type=hidden name=\"" + target +  "\" value=\"" + target_value+ "\">");
	if(b_agent == "Netscape")
	{
		cgi =  getEscapeValue(cgi);
	}
	newwin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
	newwin.document.writeln("</form>");
	newwin.document.writeln("</body>");
	newwin.document.writeln("</html>");
	newwin.document.close();
	newwin.document.this_form.submit();
	return true;
}






//******************************************************
