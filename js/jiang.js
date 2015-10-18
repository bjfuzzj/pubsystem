////////////////////////////////
///
///表单提交检查
///
////////////////////////////////

function actionclick(my_form,_action){
	my_form._action.value=_action;
	if(_action =="add"){
		my_form.submit();return true;
		}else{
		len     =       my_form.elements.length;
		var     index   =       0;
		var     ele_checked=false;
		for( index=0; index < len; index++ ){
			if( my_form.elements[index].name == "_id" && my_form.elements[index].checked==true){
				ele_checked=true;
				if(_action=="delete"){
					if(prompt("请确认是否真的删除?(yes/no)","no")=="yes"){
						my_form.submit();
						return true;
						}else{
						return false;
					}
					}else{
					my_form.submit();
					return true;
				}
			}
		}
		if(!ele_checked){
			alert("未选择选项，请选择。");
			return false;
		}
	}
	
}

lastName = '';
lastSrc  = '/images/open.gif';

function chgPic(img){
	if( lastName != img ){
		if( lastName != ''){
			window.document.images[lastName].src = lastSrc;
		}
		lastName = img;
		lastSrc  = window.document.images[img].src;
		window.document.images[img].src	= '/images/r.gif';
	}
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
			var checkflag = 0;
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
