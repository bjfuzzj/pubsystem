//==============================================================================================
//PART I(通用) JavaScript Function
//==============================================================================================


//----------------------------------------------------------------------------------------------
// 处理TextArea对象的的预览
// 参数:
//		oForm:当前的表单对象
//		idTextArea:需要预览的对象ID
//		idIFrame:提供预览操作的内部帧对象ID
//		oSender:事件发送者
//----------------------------------------------------------------------------------------------
function On_TextArea_PreviewClick(oForm, idTextArea, idIFrame, oSender)
{
	if(oSender.checked)
	{
		oForm[idTextArea].style.display = "none";
		document.getElementById(idIFrame).style.display = "block";
		document.getElementById(idIFrame).bgColor = "#C0C0C0";
		document.getElementById(idIFrame).contentWindow.document.body.innerHTML = oForm[idTextArea].value;
		ifPreviewed = true;
	
	}
	else
	{
		oForm[idTextArea].style.display = "inline";
		document.getElementById(idIFrame).style.display = "none";
	}
}



//----------------------------------------------------------------------------------------------
// 处理口令的产生
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oTarget:数据回送对象
//----------------------------------------------------------------------------------------------
function On_Password_GenClick(oForm, oSender, oTarget)
{
	var out;
	out = window.showModalDialog("/gsps/pwdgen.html","","dialogHeight: 350px; dialogWidth: 350px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
	if(out != null && out[1] == "true")
	{
		oTarget.value = out[0];
	}
}


function On_TextField_Check(oForm, oSender, prefix, ffParam, l, t, w, h, pid, tid, fid, did)
{
	var url = "textfield_check.cgi?_p_id=" + pid + "&_t_id=" + tid + "&_f_id=" + fid;
	if (did != null && did != "undefined" && did != "" && did > 0)
	{
		url = url + "&_d_id=" + did;
	}
	for (var i = 0; i < ffParam.length; i++)
	{
		var oField = oForm.elements(prefix + ffParam[i]);
		if (oField != null && oField != "undefined")
		{
			url = url + "&" + ffParam[i] + "=" + oField.value;
		}
	}
	var option = "width=" + w + ",height=" + h + ",left=" + l + ",top=" + t + ",scrollbars=1,status=1,resizable=1";
	var newWindow = window.open(url, "check", option);
	newWindow.focus();
}


//==============================================================================================
//处理关键字相关的 JavaScript Function
//==============================================================================================
//----------------------------------------
//响应"相关报道检索"button
//----------------------------------------
function checkKeywordSerachSubmit(eve,form, obj)
{
	var e=window.event?window.event:eve;
	if(e.keyCode == 13)
	{
		obj.click();
	}
}



function exit_in_array(arr, val)
{
	var i;
	for(i = 0; i < arr.length; i++)
	{
		if(arr[i] == val)
			return 1;
	}
	return 0;
}



//----------------------------------------
//	用途: 处理关键字搜索
//	参数：
//		oForm: 当前表单对象
//		oSender: 触发事件的对象
//		cgi: 触发事件的对象
//		target: 当前相关报道绑定的模板域字段名称
//		addtofield: 当前搜索关键字绑定的目标模板域名称(Form类型)
//			对每次检索的关键字都将通过JavaScript追加到该模板域对应的文本框中
//----------------------------------------
function On_DocumentForm_KeywordSearch(oForm, oSender, cgi, target, keyset_field)
{
	var key;
	var val;
	var i;
	var search_keylist;
	var search_keyarray;
	var keyset_value;
	var keyset_array;
	var search_obj = target + "_key"; 
	
	// 查询检索关键字(+或-或空格分隔)
	if ((search_keylist = getValueByName(oForm, search_obj)) == "")
	{
		alert("请输入关键字!");
		return false;
	}

	//如果提供的目标关键字集合字段存在
	if(keyset_field != "")
	{
		keyset_field = "_FORM_PF_" + keyset_field;

		//分拆检索的关键字列表
		search_keyarray = search_keylist.split(/\+|-|\s/);

		//获取目标关键字集合列表值
		keyset_value = getValueByName(oForm, keyset_field);
		if(keyset_value == "")
		{
			if(search_keyarray.length > 1)
			{
				keyset_value = search_keyarray.join(",");
			}
			else
			{
				keyset_value = search_keyarray[0];
			}
		}
		else
		{
			keyset_array = keyset_value.split(/,/);
			if(keyset_array.length == 1)
			{
				if(search_keyarray.length == 1)
				{
					if(search_keyarray[0] != keyset_array[0])
					{
						keyset_value = search_keyarray + "," + 	keyset_array;
					}
				}
				else
				{
					if(!exit_in_array(search_keyarray, keyset_array[0]))
					{
						keyset_value = search_keyarray.join(",") + "," + keyset_array[0];
					}
				}
			}
			else
			{
				if(search_keyarray.length == 1)
				{
					if(!exit_in_array(keyset_array, search_keyarray[0]))
					{
						keyset_value = search_keyarray[0] + "," + 	keyset_array.join(",");		
					}
				}
				else
				{
					for(i=0; i<search_keyarray.length; i++)
					{
						val = search_keyarray[i];
						if(!exit_in_array(keyset_array, val))
						{
							keyset_array.unshift(val);
						}
					}
					keyset_value = keyset_array.join(",");		
				}
			}
		}
		setValueByName(oForm, keyset_field, keyset_value);
	}

	var title = getValueByRef(oForm, oSender);
	var agent = "/cgi-bin/gsps/keyword/www_search_agent.cgi";
	do_Rel_Result(agent, cgi, oForm, target, title, false);
}


//----------------------------------------
//处理关键字搜索,调用www_agent.cgi
//----------------------------------------
function do_Rel_Result(agent,cgi,form,target,title,pause)
{

	var b_agent=navigator.appName;
	if(b_agent == 'Netscape')
	{
		title = getEscapeValue(title);
		browse = "Netscape";
	}
	else
	{
		browse = "IE";
		// yongbin 080912
		try {
			document.myApplet.getEncodeValue("");
		}catch(e){
			alert("Java虚拟机未安装或版本太低！");
			return;
		}
	}
	var browse;
	var pid;
	var url = cgi;
	//var subReplaceWin;
	var targetEleName = "_FORM_PF_"+target;
	var modeEleName = target + "_mode";
	var keyEleName = target + "_key";
	var screen_width = window.screen.width;
	var screen_height = window.screen.height;
	var left = (screen_width - 600)/2;
	var top = (screen_height - 400)/2;
	var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
	property = property + ",top="+top+",left="+left;
	var myname;
	if(navigator.appName.indexOf("Netscape") != -1)
	{
		subReplaceWin=window.open("",null,property);
	}
	else
	{ 
		if(subReplaceWin != null)
		{
			//subReplaceWin.close();
			subReplaceWin = null;
			subReplaceWin=window.open("","",property);
		}
		else
		{
			subReplaceWin=window.open("","",property);
		}	 
	}
	var target_value = form.elements[targetEleName].value;
	target_value = target_value.replace(/镕/gi,"#Rong#");		
	target_value = target_value.replace(/—/gi,"#Squote#");
	var key_value = form.elements[keyEleName].value;
	var mode_value = form.elements[modeEleName].value;
	
	if (form.elements[target+'_if_check_video'].checked == true)
	{
		key_value = "视频 " + key_value;
	}

	
	subReplaceWin.focus();
	subReplaceWin.document.open("text/html");
	subReplaceWin.document.writeln("<html>");
	subReplaceWin.document.writeln("<head>");
	subReplaceWin.document.writeln("<title>" + title + "</title>");
	subReplaceWin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
	subReplaceWin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">");
	subReplaceWin.document.writeln("</head>");
	subReplaceWin.document.writeln("<body>"); 
	subReplaceWin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
	subReplaceWin.document.writeln("Please Waiting.....");
	subReplaceWin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + targetEleName + "\">");
	if(b_agent == "Netscape")
	{
		target_value = target_value.replace(/\"/g,"&quot;");
		target_value = getEscapeValue(target_value);
		//key_value = key_value.replace(/\"/g,"&quot;");
		//key_value = getEscapeValue(key_value);
		mode_value = mode_value.replace(/\"/g,"&quot;");
		mode_value = getEscapeValue(mode_value);
	}
	else
	{
		if(target_value != "")
		{
			target_value = target_value.replace(/·/gi,".");
			target_value = document.myApplet.getEncodeValue(target_value);
		}
		
		key_value = document.myApplet.getEncodeValue(key_value);
		mode_value = document.myApplet.getEncodeValue(mode_value);
	}
	subReplaceWin.document.writeln("<input type=hidden name=\"" + targetEleName +  "\" value=\"" + target_value+ "\">");
	subReplaceWin.document.writeln("<input type=hidden name=\"mode\" value=\"" + mode_value + "\">");

	subReplaceWin.document.writeln("<input type=hidden name=\"key\" value=\"" + key_value + "\">");
	
	if(b_agent == "Netscape")
	{
		cgi =  getEscapeValue(cgi);
	}
	subReplaceWin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
	subReplaceWin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
	//如果是在本项目搜索
	if (form.elements[target+'_if_pid'].checked==true)
	{
		pid = form.elements['p_id'].value;
		subReplaceWin.document.writeln("<input type=hidden name=\"pid\" value=\"" + pid+ "\">");
	}

	if (form.elements[target+'_if_check_spider'] != null && form.elements[target+'_if_check_spider'].checked==false)
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"type\" value=\"e\" >");
	}
	//是否需要检索播客
	if (form.elements[target+'_if_use_podcast'] != null && form.elements[target+'_if_use_podcast'].checked==true)
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"ifUsePodcast\" value=\"Y\" >");
	}//wyend
	//是否需要显示媒体名称
	if (form.elements[target+'_if_use_medianame'].checked==true)
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"ifUseMedia\" value=\"Y\">");	
	}
	else
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"ifUseMedia\" value=\"N\">");
	}

	//是否热点相关新闻
	if (form.elements[target+'_if_use_hotnews'].checked==true) 
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"ifHotNewsSearch\" value=\"Y\">");	
	}
	else
	{
		subReplaceWin.document.writeln("<input type=hidden name=\"ifHotNewsSearch\" value=\"N\">");
	}

	if(pause)
	{
		subReplaceWin.document.writeln("<input type=submit value=\"submit\">");
		subReplaceWin.document.writeln("</form>");
		subReplaceWin.document.writeln("</body>"); 
		subReplaceWin.document.writeln("</html>");
	}
	else
	{
		subReplaceWin.document.writeln("</form>");
		subReplaceWin.document.writeln("</body>"); 
		subReplaceWin.document.writeln("</html>");
		subReplaceWin.document.close();
		subReplaceWin.document.this_form.submit();
	}
	return true;
}
