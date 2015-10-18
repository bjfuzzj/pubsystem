
var bodyID,htmlableID;

var WBTB_yToolbars = new Array();

var WBTB_YInitialized = false;

var WBTB_filterScript = false;

var WBTB_charset="UTF-8";
var isIE=false;
var userAgent = navigator.userAgent.toLowerCase();
if ((userAgent.indexOf('msie') != -1) && (userAgent.indexOf('opera') == -1)) {
	isIE = true;
}

var view_flag = false;
var html_code_body1 = "";
var html_code_body2 = "";


String.prototype.replaceAll = function(s1,s2)
{
	return this.replace(new RegExp(s1,"igm"),s2);
}  


function print_r(oo)
{
        v_html = "";

	try{
        for(p in oo)
        {
                vv = oo[p] ? oo[p] : "NULL";
                v_html += "\n==================================\n" + p + "\n" + vv + "\n";
                v_html += "\n==================================\n" + p + "\n";
        }
	}catch(e){ alert(e) }

	alert(v_html);
}


function getIFrameHTML(idfrm)
{
        var iFrameHTML = "";
        var objIFrame;
        if (document.all)
        {
                objIFrame = eval( "document.all." + idfrm);
        }
        else
        {
                objIFrame = document.getElementById(idfrm).contentDocument;
        }


        if (objIFrame.contentDocument)
        {
                // For NS6
                iFrameHTML = objIFrame.contentDocument.innerHTML;
        }
        else if (objIFrame.contentWindow)
        {
                // For IE5.5 and IE6
                iFrameHTML = objIFrame.contentWindow.document.body.innerHTML;
        }
        else if (objIFrame.document)
        {
                // For IE5
                iFrameHTML = objIFrame.document.body.innerHTML;
        }
        else if(objIFrame.body)
        {
		// Firefox 
                iFrameHTML = objIFrame.body.innerHTML;
		if(iFrameHTML.indexOf('<br>') === 0)
		{
			iFrameHTML=iFrameHTML.substr(4);
		}
        }

	return iFrameHTML;
}



function write_temp_data(tdata)
{

	/*
	try{
	myReg = /([\s\S]*?<body[\s\S]*?>)[\s\S]*?(<\/body>[\s\S]*)/i
	ret = myReg.exec(tdata);
	if(ret)
	{
		html_code_body1 = ret[1];
		html_code_body2 = ret[2];
	}
	}catch(e) {}
	*/

	
	var xmlhttp;
	var flag = 0;
	try{
		xmlhttp = new XMLHttpRequest();
		}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}
	
	cgi_prog  =  'temp_write.php?p_id=' + p_id + '&t_id=' + t_id;
	var tm = new Date();
	cgi_prog += "&tm=" + tm.getTime();


	tdata = tdata.replaceAll('%', '%25');
	tdata = tdata.replaceAll('&', '%26');
	tdata = tdata.replaceAll("\\+", "%2B");
	post_data = 'tdata=' + tdata;

	//alert(cgi_prog);
	xmlhttp.open("POST", cgi_prog, false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xmlhttp.send(post_data);
	ret = xmlhttp.responseText;

	//alert(ret);
	body_mark = "\n========================MARK_OF_GHC=============================\n";
	pos = ret.indexOf(body_mark);
	if(pos < 0 )
	{
		alert(ret);
		return false;
	}

	html_code_body1 = ret.substr(0, pos);
	html_code_body2 = ret.substr(pos+body_mark.length);

	return true;
}


function OpenUpload(url,pid,setFrm)
{
	url = url + "?_p_id=" + pid + "&_setfrm=" + setFrm;
	newWindow = window.open(url,"upload","width=450,height=300,left=0,top=0,scrollbars=1,status=1,resizable=1");
	newWindow.focus();
}

//function document.onreadystatechange()
window.onload=function()
{
	if (WBTB_YInitialized) return;
	WBTB_YInitialized = true;

	var i, curr;

	var yTb = document.getElementById("yToolbar");	
	if(yTb != null) {
		for (i = 0; i < yTb.length; i++) {
			curr = yTb[i];
			WBTB_InitTB(curr);
			WBTB_yToolbars[WBTB_yToolbars.length] = curr;
		}
	}	
}

function WBTB_InitBtn(btn)
{
	btn.onmouseover = WBTB_BtnMouseOver;
	btn.onmouseout = WBTB_BtnMouseOut;
	btn.onmousedown = WBTB_BtnMouseDown;
	btn.onmouseup	= WBTB_BtnMouseUp;
	btn.ondragstart = WBTB_YCancelEvent;
	btn.onselectstart = WBTB_YCancelEvent;
	btn.onselect = WBTB_YCancelEvent;
	btn.YUSERONCLICK = btn.onclick;
	btn.onclick = WBTB_YCancelEvent;
	btn.YINITIALIZED = true;
	return true;
}

function WBTB_InitTB(y)
{
	y.TBWidth = 0;
	if (!WBTB_PopulateTB(y)) return false;
	y.style.posWidth = y.TBWidth;
	return true;
}


function WBTB_YCancelEvent()
{
	event.returnValue=false;
	event.cancelBubble=true;
	return false;
}

function WBTB_BtnMouseOver()
{
	if (event.srcElement.tagName != "IMG") return false;
	var image = event.srcElement;
	var element = image.parentElement;

	if (image.className == "WBTB_Ico") element.className = "WBTB_BtnMouseOverUp";
	else if (image.className == "WBTB_IcoDown") element.className = "WBTB_BtnMouseOverDown";

	event.cancelBubble = true;
}

function WBTB_BtnMouseOut()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		return false;
	}

	var image = event.srcElement;
	var element =	image.parentElement;
	yRaisedElement = null;

	element.className = "WBTB_Btn";
	image.className = "WBTB_Ico";

	event.cancelBubble = true;
}

function WBTB_BtnMouseDown()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		event.returnValue=false;
		return false;
	}

	var image = event.srcElement;
	var element = image.parentElement;

	element.className = "WBTB_BtnMouseOverDown";
	image.className = "WBTB_IcoDown";

	event.cancelBubble = true;
	event.returnValue=false;
	return false;
}

function WBTB_BtnMouseUp()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		return false;
	}

	var image = event.srcElement;
	var element = image.parentElement;

	if (element.YUSERONCLICK) eval(element.YUSERONCLICK + "anonymous()");

	element.className = "WBTB_BtnMouseOverUp";
	image.className = "WBTB_Ico";

	event.cancelBubble = true;
	return false;
}

function WBTB_PopulateTB(y)
{
	var i, elements, element;

	elements = y.children;
	for (i=0; i<elements.length; i++) {
	element = elements[i];
	if (element.tagName== "SCRIPT" || element.tagName == "!") continue;

	switch (element.className) {
		case "WBTB_Btn":
			if (element.YINITIALIZED == null) {
				if (! WBTB_InitBtn(element))
					return false;
			}

			element.style.posLeft = y.TBWidth;
			y.TBWidth	+= element.offsetWidth + 1;
			break;

		case "WBTB_TBGen":
			element.style.posLeft = y.TBWidth;
			y.TBWidth	+= element.offsetWidth + 1;
			break;

			//default:
			//  return false;
		}
	}

	y.TBWidth += 1;
	return true;
}

function WBTB_DebugObject(obj)
{
	var msg = "";
	for (var i in TB) {
		ans=prompt(i+"="+TB[i]+"\n");
		if (! ans) break;
	}
}

// NOTE: "////" modified by jingtao for support multi-HtmlEditor
////function WBTB_validateMode()
function WBTB_validateMode(bTextMode,frm)
{
	////if (!WBTB_bTextMode) return true;
	if (!bTextMode) return true;
	
	alert("请取消“查看HTML源代码”选项再使用系统编辑功能或者提交!");
	
	////WBTB_Composition.focus();
	var editor = eval(frm);
	editor.focus();
	
	return false;
}

////function WBTB_format1(what,opt)
function WBTB_format1(what,frm,opt)
{
	if (opt=="removeFormat")
	{
		what=opt;
		opt=null;
	}
	
	////WBTB_Composition.focus();
	var editor=document.getElementById(frm).contentWindow;
	//firefox不支持execCommand里的删除选中链接的功能
	if(what=="Unlink"&&!isIE){
		var oContainer=editor.getSelection().getRangeAt(0).startContainer;
		while ( oContainer )
		{
			if ( oContainer.nodeName == 'A' ){
				RemoveOuterTags(oContainer);
							}
			oContainer = oContainer.parentNode ;
		}
		editor.focus();
		return;

	}
	if((what=="cut"||what=="copy"||what=="paste")&&!isIE)
	{
		alert("该浏览器不支持 剪切 复制 粘贴 操作");
		return;
	}
	


		////WBTB_Composition.document.execCommand(what,"",opt);
		editor.document.execCommand(what,"",opt);
	
	
	WBTB_pureText = false;
	
	////WBTB_Composition.focus();
	editor.focus();	
}
function RemoveOuterTags(e){
	var oFragment = e.ownerDocument.createDocumentFragment() ;

	for ( var i = 0 ; i < e.childNodes.length ; i++ )
		oFragment.appendChild( e.childNodes[i].cloneNode(true) ) ;

	e.parentNode.replaceChild( oFragment, e ) ;
}
////function WBTB_format(what,opt)
function WBTB_format(what,bTextMode,frm,opt)
{
	  ////if (!WBTB_validateMode()) return;
	  if (!WBTB_validateMode(bTextMode,frm)) return;

	  ////WBTB_format1(what,opt);
	  WBTB_format1(what,frm,opt);
}


function WBTB_setMode(objField,container,tabHtml,tabDesign,bTextMode,frm)
{

	
	if (bTextMode)
	{

		v_html = "";

		try{
		v_html = getIFrameHTML(frm);
		v_html = v_html.replaceAll("<ghc_write_mark>[\\s\\S]*?</ghc_write_mark>", "");
		}catch(e) { alert(e); }
		document.getElementById(container).style.display='none';
		
		objField.style.display = "inline";
		/*
		alert(html_code_body1);
		alert(v_html);
		*/

		objField.value = html_code_body1 +  v_html + html_code_body2;
	}
	else
	{
		ret = write_temp_data(objField.value);
		if(!ret) return;

		document.getElementById(container).style.display='';
		objField.style.display = "none";

		isrc = document.getElementById(frm).src;
		pos = isrc.indexOf('&tm=');
		if(pos > 0)
		{
			isrc = isrc.substr(0, pos);
		}

		tm = new Date();
		isrc += "&tm=" + tm.getTime();

		document.getElementById(frm).src = isrc;

	}

	
	WBTB_setTab(tabHtml,tabDesign,bTextMode);
	WBTB_setStyle(bTextMode,frm);
}

////function WBTB_setStyle()
function WBTB_setStyle(bTextMode,frm)
{
	////bs = WBTB_CompositioN.document.body.runtimeStyle;
	
	//var editor = eval(frm);
	var editor = document.getElementById(frm).contentWindow;
	var  bs = editor.document.body.style;
	



	//bs = editor.document.body.runtimeStyle;
	
	//根据mode设置iframe样式表
	////if (WBTB_bTextMode) {
	if (bTextMode) {
		bs.fontFamily="宋体,Arial";
		bs.fontSize="10pt";
	}else{
		bs.fontFamily="宋体,Arial";
		bs.fontSize="10.5pt";
	}
	bs.scrollbar3dLightColor= '#D4D0C8';
	bs.scrollbarArrowColor= '#000000';
	bs.scrollbarBaseColor= '#D4D0C8';
	bs.scrollbarDarkShadowColor= '#D4D0C8';
	bs.scrollbarFaceColor= '#D4D0C8';
	bs.scrollbarHighlightColor= '#808080';
	bs.scrollbarShadowColor= '#808080';
	bs.scrollbarTrackColor= '#D4D0C8';
	bs.border='0';
}

////function WBTB_setTab()
function WBTB_setTab(tabHtml,tabDesign,bTextMode)
{
	//alert(tabDesign);
	//html和design按钮的样式更改
	////var mhtml=document.all.WBTB_TabHtml;
	var mhtml=document.getElementById(tabHtml);
	
	////var mdesign=document.all.WBTB_TabDesign;
	var mdesign=document.getElementById(tabDesign);
	
	////if (WBTB_bTextMode)		
	if (bTextMode)
	{
		mhtml.className="WBTB_TabOn";
		mdesign.className="WBTB_TabOff";
	}else{
		mhtml.className="WBTB_TabOff";
		mdesign.className="WBTB_TabOn";
	}
}

function WBTB_getEl(sTag,start)
{
	while ((start!=null) && (start.tagName!=sTag)) start = start.parentElement;
	return start;
}

function WBTB_replaceFontSize(cont)
{
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=1([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:11px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=2([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:12px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=3([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:14px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=4([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:16px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=5([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:18px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=6([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:24px\"$2>");
	cont = cont.replace(/<font(\s*[^<>]*\s*)size=7([^<>]*)>/gi, "<font$1style=\"FONT-SIZE:36px\"$2>");
	return cont;
}
	
function WBTB_FontSize(bTextMode,frm,size)
{
	if (!WBTB_validateMode(bTextMode,frm)) return;
	if (size < 1 || size > 7) return;
	
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	editor.document.execCommand("fontsize","",size);
	editor.document.body.innerHTML = WBTB_replaceFontSize(editor.document.body.innerHTML);
	editor.focus();
}

////function WBTB_UserDialog(what)
function WBTB_UserDialog(what,bTextMode,frm)
{
	var editor=document.getElementById(frm).contentWindow;
		if (!WBTB_validateMode(bTextMode,frm))	return;
	WBTB_showdlg("/richtext/image.htm",frm,300,300,'&pageType=image');
}

////function WBTB_foreColor()
function WBTB_foreColor(bTextMode,frm)
{
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	if(isIE){
		var arg = new Array();
		var arr = showModalDialog("/richtext/selcolor.htm?iframeid="+frm+"&oprtype=forecolor", window, "dialogWidth:300px;dialogHeight:270px;status:no;scroll:no;help:no");
	}
	else{
		var win = window.open("/richtext/selcolor.htm?iframeid="+frm+"&oprtype=forecolor", null, "Width=300,Height=270");
		win.dialogArguments = window;
	}	
}

////function WBTB_backColor()
function WBTB_backColor(bTextMode,frm)
{
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	if(isIE){
		var arg = new Array();
		var arr = showModalDialog("/richtext/selcolor.htm?iframeid="+frm+"&oprtype=backcolor", window, "dialogWidth:300px;dialogHeight:270px;status:no;scroll:no;help:no");
	}
	else{
		var win = window.open("/richtext/selcolor.htm?iframeid="+frm+"&oprtype=backcolor", null, "Width=300,Height=270");
		win.dialogArguments = window;
	}
}
function WBTB_showdlg(win_url,frm,win_width,win_height,parm){
	if(!parm)parm='';
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	if(isIE){
		var arg = new Array();
		var arr = showModalDialog(win_url+"?iframeid="+frm+parm, window, "dialogWidth:"+win_width+"px;dialogHeight:"+win_height+"px;status:no;scroll:no;help:no");
	}
	else{
		var win = window.open(win_url+"?iframeid="+frm+parm, null, "Width="+win_width+",Height="+win_height);
		win.dialogArguments = window;
	}	
}
////function WBTB_fortable()
function WBTB_fortable(bTextMode,frm)
{
	if (!WBTB_validateMode(bTextMode,frm))	return;
	WBTB_showdlg("/richtext/table.htm",frm,400,400);	
}

//// Added by jingtao for insert hyperlink
function WBTB_forhl(frm)
{
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	
	var oSel = editor.document.selection;
	if(oSel){
		var oRange = oSel.createRange();
		var oElem = (oSel.type == "Control") ? oRange(0) : oRange.parentElement();
		var sTag = oElem.tagName.toUpperCase();
		
		if ((oSel.type == "Control" && sTag != "IMG")
				|| sTag == "CAPTION" || sTag == "COL" || sTag == "COLGROUP" || sTag == "FRAMESET" || sTag == "HTML" || sTag == "TEXTAREA"
				|| sTag == "TABLE" || sTag == "TBODY" || sTag == "TFOOT" || sTag == "TH" || sTag == "THEAD" || sTag == "TR") {
			alert("只能给文本或图片插入超链接");
			editor.focus();
			return;
		}
	}
	var win;
	if(isIE){
		var arg = new Array();
		var arr = showModalDialog("/richtext/hyperlink.htm?iframeid="+frm, window, "dialogWidth:21em;dialogHeight:19em;status:no;scroll:no;help:no");
	}
	else{
		win = window.open("/richtext/hyperlink.htm?iframeid="+frm, null, "Width=350,Height=370");
		win.dialogArguments = window;
	}


}
//// End added

////function WBTB_forswf()
function WBTB_forswf(frm)
{
	WBTB_showdlg("/richtext/image.htm",frm,300,300,'&pageType=flash');	
}

////function WBTB_forwmv()
function WBTB_forwmv(frm)
{
	WBTB_showdlg("/richtext/image.htm",frm,300,300,'&pageType=wmv');
}

////function WBTB_forrm()
function WBTB_forrm(frm)
{
	WBTB_showdlg("/richtext/image.htm",frm,300,300,'&pageType=rm');
}

////function WBTB_InsertRow()
function WBTB_InsertRow(frm)
{
	////editor = WBTB_Composition;
	//var editor = eval(frm);
	var editor=document.getElementById(frm).contentWindow;
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	var rowIndex;
	var cellIndex;
	var parentTable;
	switch(objReference.tagName)
	{
	case 'TABLE' :
		parentTable=objReference;	
		rowIndex=parentTable.rows.length-1;
		break;
		
	case 'TBODY' :
		parentTable=objReference;       
                rowIndex=parentTable.rows.length-1;	
		break;
		
	case 'TR' :
		var rowIndex = objReference.rowIndex;
		var parentTable=objReference.parentElement.parentElement;
		var newTable=parentTable.cloneNode(true);
		var newRow = newTable.insertRow(rowIndex+1);
		for(x=0; x< newTable.rows[0].cells.length; x++)
		{
			var newCell = newRow.insertCell();
		}
		parentTable.outerHTML=newTable.outerHTML;
		break;
		
	case 'TD' :
		var parentRow=objReference.parentNode;
		rowIndex = parentRow.rowIndex;
		cellIndex=objReference.cellIndex;
		parentTable=objReference.parentNode.parentNode.parentNode;			
		break;
		
	default :
		return;
	}
	var newTable=parentTable.cloneNode(true);
	var newRow = newTable.insertRow(rowIndex+1);
	/**/
	for(x=0; x< newTable.rows[0].cells.length; x++)
	{
	
		var newCell = newRow.insertCell(0);
		newCell.innerHTML='&nbsp;';
		if (x==cellIndex)newCell.id='ura';
	}
	if(isIE){
		parentTable.outerHTML=newTable.outerHTML;
		var r = editor.document.body.createTextRange();
			var item=editor.document.getElementById('ura');
		item.id='';
		r.moveToElementText(item);
		r.moveStart('character',r.text.length);
		r.select();
	}
	else
	{
		parentTable.innerHTML=newTable.innerHTML;
	}	
}

////function WBTB_DeleteRow()
function WBTB_DeleteRow(frm)
{
	////editor=WBTB_Composition;
	var editor=document.getElementById(frm).contentWindow;
	
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TR' :
		var rowIndex = objReference.rowIndex;//Get rowIndex
		var parentTable=objReference.parentNode.parentNode;
		parentTable.deleteRow(rowIndex);
		break;
	
	case 'TD' :
		var cellIndex=objReference.cellIndex;
		var parentRow=objReference.parentNode;//Get Parent Row
		var rowIndex = parentRow.rowIndex;//Get rowIndex
		var parentTable=objReference.parentNode.parentNode.parentNode;
		parentTable.deleteRow(rowIndex);
		if (rowIndex>=parentTable.rows.length)
		{
			rowIndex=parentTable.rows.length-1;
		}
		if(isIE){
			if (rowIndex>=0)
			{
				var r = editor.document.body.createTextRange();
				r.moveToElementText(parentTable.rows[rowIndex].cells[cellIndex]);
				r.moveStart('character',r.text.length);
				r.select();
			}
			else
			{
				parentTable.removeNode(true);
			}
		}
		break;
	
	default :return;
	}	
}


////function WBTB_InsertColumn()
function WBTB_InsertColumn(frm)
{
	////editor = WBTB_Composition;
	var editor=document.getElementById(frm).contentWindow;
	
	objReference= WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TABLE' :// IF a table is selected, it adds a new column on the right hand side of the table.
		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell(0);
		}
		newCell.focus();
		if(isIE){
			objReference.outerHTML=newTable.outerHTML;
		}
		else{
			objReference.innerHTML=newTable.innerHTML;
		}
		break;
	
	case 'TBODY' :// IF a table is selected, it adds a new column on the right hand side of the table.
		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell(0);
		}
		objReference.outerHTML=newTable.outerHTML;
		break;
	
	case 'TR' :// IF a table is selected, it adds a new column on the right hand side of the table.
		objReference=objReference.parentNode.parentNode;

		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell(0);
		}
		 if(isIE){
                        parentTable.outerHTML=newTable.outerHTML;
                }
                else{
                        parentTable.innerHTML=newTable.innerHTML;
                } 

		break;
	
	case 'TD' :// IF the cursor is in a cell, or a cell is selected, it adds a new column to the right of that cell.
		var cellIndex = objReference.cellIndex;//Get cellIndex
		var rowIndex=objReference.parentNode.rowIndex;
		var parentTable=objReference.parentNode.parentNode.parentNode;
		var newTable=parentTable.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell(cellIndex+1);
			if (x==rowIndex)newCell.id='ura';
		}
		if(isIE){
			parentTable.outerHTML=newTable.outerHTML;
			var r = editor.document.body.createTextRange();
			var item=editor.document.getElementById('ura');
			item.id='';
			r.moveToElementText(item);
			r.moveStart('character',r.text.length);
			r.select();
		}
		else{
			parentTable.innerHTML=newTable.innerHTML;
		}
		break;

	default :
		return;
	}	
}


////function WBTB_DeleteColumn()
function WBTB_DeleteColumn(frm)
{
	////editor = WBTB_Composition;
		var editor=document.getElementById(frm).contentWindow;
	
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TD' :
		var rowIndex=objReference.parentNode.rowIndex;
		var cellIndex = objReference.cellIndex;//Get cellIndex
		var parentTable=objReference.parentNode.parentNode.parentNode;
		var newTable=parentTable.cloneNode(true);
		
		if (newTable.rows[0].cells.length==1)
		{
			//parentTable.removeNode(true);
			parentTable.deleteRow(rowIndex);
			return;
		}
		for(x=0; x<newTable.rows.length; x++)
		{//alert(newTable.rows[x].cells[cellIndex]);
			if(isIE){
				if (newTable.rows[x].cells[cellIndex]=='[object]')
				{
					newTable.rows[x].deleteCell(cellIndex);
				}
			}
			else{
				newTable.rows[x].deleteCell(cellIndex);
			}
		}
		if (cellIndex>=newTable.rows[0].cells.length)
		{
			cellIndex=newTable.rows[0].cells.length-1;
		}
		if (cellIndex>=0)  newTable.rows[rowIndex].cells[cellIndex].id='ura';
		if(isIE){
			parentTable.outerHTML=newTable.outerHTML;
			if (cellIndex>=0){
				var r = editor.document.body.createTextRange();
				var item=editor.document.getElementById('ura');
				item.id='';
				r.moveToElementText(item);
				r.moveStart('character',r.text.length);
				r.select();
			}
		}
		else{
			parentTable.innerHTML=newTable.innerHTML;
		}
		break;
		
	default :return;
	}	
}


function WBTB_GetRangeReference(editor)
{
	editor.focus();
	var objReference = null;
	

	//var selectedRange
	if(isIE){
		var RangeType = editor.document.selection.type;
		var selectedRange = editor.document.selection.createRange();
		
		switch(RangeType)
		{
			case 'Control' :
				if (selectedRange.length > 0 )
				{
					objReference = selectedRange.item(0);
				}
				break;

			case 'None' :
				objReference = selectedRange.parentElement();
				break;

			case 'Text' :
				objReference = selectedRange.parentElement();
				break;
		}
	}
	else{
		var oContainer=editor.getSelection().getRangeAt(0).startContainer;
		
		switch(oContainer.nodeName)
		{
			case '#text' :
				objReference = oContainer.parentNode;
				break;
			default :
				objReference = oContainer;
				break;
		}
	}	
	return objReference;	
}

function WBTB_CheckTag(item,tagName)
{
	if (tagName.indexOf(item.nodeName)!=-1)
	{
		return item;
	}
	
	if (item.nodeName=='BODY')
	{
		return false;
	}
	item=item.parentNode;
	return WBTB_CheckTag(item,tagName);	
}

function WBTB_code(frm)
{
	WBTB_specialtype(frm,"<div class=quote style='cursor:hand'; title='Click to run the code' onclick=\"preWin=window.open('','','');preWin.document.open();preWin.document.write(this.innerText);preWin.document.close();\">","</div>");
}


function WBTB_replace(frm)
{
	var editor = eval(frm);
	var oRange = editor.document.selection.createRange();
	var arr = showModalDialog("/richtext/replace.htm", oRange, "dialogWidth:23em;dialogHeight:15.5em;status:no;scroll:no;help:no");
	editor.focus();
}



function WBTB_RemoveElem(obj,tag)
{
	var aElem = obj.getElementsByTagName(tag);
	for (var i = aElem.length - 1; i >= 0; i--) {
		aElem[i].removeNode(true)
	}
}

function WBTB_RemoveComment(str)
{
	var start, end;
		
	while(true) {
		start = str.indexOf("<!--");
		if (start == -1) // not find
			break;
		
		end = str.indexOf("-->");	
		end = start > end ? (start + 3) : (end + 2);
		str = str.replace(str.substring(start, end), "");
	}
	
	return str;
}

function WBTB_CleanCode(frm)
{
	var editor = eval(frm);
	editor.focus();
	
	var arr = showModalDialog("/richtext/cleancode.htm", "", "dialogWidth:22.5em;dialogHeight:27em;status:no;scroll:no;help:no");

	if (arr != null) {
		var eBody = editor.document.body;
		var eHtml = eBody.innerHTML;
		var RegExp;
		
		// 清理注释
		eHtml = WBTB_RemoveComment(eHtml);
		eBody.innerHTML = eHtml;
		
		if (arr[0] == 0) { // 彻底清理
			// Regex based cleaning
			eHtml = eBody.innerHTML;
			eHtml = eHtml.replace(/<o:p>&nbsp;<\/o:p>/gi, "");
			eHtml = eHtml.replace(/o:/gi, "");
			eBody.innerHTML = eHtml;
	
			// 清理STYLE、SCRIPT、NOSCRIPT、EMBED、OBJECT、IFRAME、TEXTAREA、SELECT
			WBTB_RemoveElem(eBody, "STYLE");
			WBTB_RemoveElem(eBody, "SCRIPT");
			WBTB_RemoveElem(eBody, "NOSCRIPT");
			WBTB_RemoveElem(eBody, "EMBED");
			WBTB_RemoveElem(eBody, "OBJECT");
			WBTB_RemoveElem(eBody, "IFRAME");
			WBTB_RemoveElem(eBody, "TEXTAREA");
			WBTB_RemoveElem(eBody, "SELECT");
			
			// 为了彻底地清理（上面的方法可能有残留）
			eHtml = eBody.innerHTML;
			RegExp = /<(STYLE|SCRIPT|NOSCRIPT|EMBED|OBJECT|IFRAME|TEXTAREA|SELECT)[^<>]*>[^<>]*<\/\1\s*>/gi;
 			eHtml = eHtml.replace(RegExp, "");
 			eBody.innerHTML = eHtml;

			
			eHtml = eBody.innerHTML;
			RegExp = /<(\/?)(P|A|BR|IMG)([^<>]*)>/gi;
			eHtml = eHtml.replace(RegExp, "{3D718B82-C1DF-48f0-9181-5F5AA7081E49}$1$2$3{B0EF3E72-55CD-4cd3-899C-36365592C752}");
			
			// 清除所有标记
			RegExp = /<[^<>]*>/gi;
			eHtml = eHtml.replace(RegExp, "");
			
			// 恢复
			RegExp = /\{3D718B82-C1DF-48f0-9181-5F5AA7081E49\}/gi;
			eHtml = eHtml.replace(RegExp, "<");
			RegExp = /\{B0EF3E72-55CD-4cd3-899C-36365592C752\}/gi;
			eHtml = eHtml.replace(RegExp, ">");			
			eBody.innerHTML = eHtml;
			
			// 清理非sina链接	
			var aLink = eBody.getElementsByTagName("A");
			for (var i = aLink.length - 1; i >= 0; i--) {
				var sURL = aLink[i].getAttribute("href");
				if (sURL != null && sURL.search(/sina\./i) == -1) {// NOT "sina" hyperlink
					aLink[i].outerHTML = aLink[i].innerText;
				}
			}
			
			// 只保留JPG/JPEG图片
			var aImg = eBody.getElementsByTagName("IMG");
			for (var i = aImg.length - 1; i >= 0; i--) {
				var sSrc = aImg[i].getAttribute("SRC");
				if (sSrc != null && sSrc.search(/\.(jpg|jpeg)$/i) == -1) { // NOT "sina" image
					aImg[i].removeNode(true);
				}
			}				
		}
		else { // 选择清理
			// 清理SCRIPT
			if (arr[1]) {
				WBTB_RemoveElem(eBody, "STYLE");
				WBTB_RemoveElem(eBody, "SCRIPT");
				WBTB_RemoveElem(eBody, "NOSCRIPT");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(STYLE|SCRIPT|NOSCRIPT)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理IFRAME
			if (arr[2]) {
				WBTB_RemoveElem(eBody, "IFRAME");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(IFRAME)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理Flash等
			if (arr[3]) {
				WBTB_RemoveElem(eBody, "EMBED");
				WBTB_RemoveElem(eBody, "OBJECT");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(EMBED|OBJECT)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理GIF广告图片
			if (arr[4]) {
				// Image				
				var aGIF = eBody.getElementsByTagName("IMG");
				for (var i = aGIF.length - 1; i >= 0; i--) {
					var sSrc = aGIF[i].getAttribute("SRC");
					if (sSrc != null && sSrc.search(/\.gif$/i) != -1) { // Is gif
						var cond1 = (aGIF[i].getAttribute("width") <= arr[5]);
						var cond2 = (aGIF[i].getAttribute("height") <= arr[7]);
						var cond = ((arr[6] == "And") ? (cond1 && cond2) : (cond1 || cond2));
						if (cond) {
							aGIF[i].removeNode(true);
						}
					}
				}
				
				// Background		
				for (var i = eBody.all.length - 1; i >= 0; i--) {
					var aElem = eBody.all[i];
					var sBG = aElem.getAttribute("BACKGROUND");
					if (sBG != null && sBG.search(/\.gif$/i) != -1) { // Is gif
						var cond1 = (aElem.getAttribute("width") <= arr[5]);
						var cond2 = (aElem.getAttribute("height") <= arr[7]);
						var cond = ((arr[6] == "And") ? (cond1 && cond2) : (cond1 || cond2));
						if (cond) {
							aElem.removeAttribute("BACKGROUND","",0);
						}
					}
				}
			}
			
			// 清理非sina链接
			if (arr[8]) {
				var aLink = eBody.getElementsByTagName("A");
				for (var i = aLink.length - 1; i >= 0; i--) {
					var sURL = aLink[i].getAttribute("href");
					if (sURL != null && sURL.search(/sina\./i) == -1) {// NOT "sina" hyperlink
						aLink[i].outerHTML = aLink[i].innerText;
					}
				}
			}
			
			// 清理非sina图片
			if (arr[9]) {
				// Image
				var aImg = eBody.getElementsByTagName("IMG");
				for (var i = aImg.length - 1; i >= 0; i--) {
					var sSrc = aImg[i].getAttribute("SRC");
					if (sSrc != null && sSrc.search(/sina\./i) == -1) { // NOT "sina" image
						aImg[i].removeNode(true);
					}
				}
				
				// Background		
				for (i = eBody.all.length - 1; i >= 0; i--) {
					var aElem = eBody.all[i];
					var sBG = aElem.getAttribute("BACKGROUND");
					if (sBG != null && sBG.search(/sina\./i) == -1) { // NOT "sina" image
						aElem.removeAttribute("BACKGROUND","",0);
					} // end if
				} // end for
			} // end if
		} // end if...else
		
		// 清理注释
		eHtml = eBody.innerHTML;
		eHtml = WBTB_RemoveComment(eHtml);
	
		// 清理多余空行	
		RegExp = /(<P>\s*<\/P>)+/gi;
 		eHtml = eHtml.replace(RegExp, "<P></P>");
 		RegExp = /(<BR>\s*)+/gi;
 		eHtml = eHtml.replace(RegExp, "<BR>");
 		eBody.innerHTML = eHtml;
	} // end if
 				
	editor.focus();
}
// End modified


function WBTB_Do_P_Tag(str)
{
	var res = str;
	
	// 先替换掉<p>...</p>
	str = str.replace(/<p(>|\s+[^>]*>)([\s\S]*?)<\/p>/gi, "$2");
			
	// 如果还存在<p>，则添加</p>
	var n = 0;
	var r1 = str.match(/<p>/i);
	if (r1 != null)
	{
		n += r1.length;
	}
	var r2 = str.match(/<p\s+[^>]*>/i);
	if (r2 != null)
	{
		n += r2.length;
	}
				
	for (i = 0; i < n; i++)
	{
		res = res + "</p>";
	}
				
	// 如果还存在</p>，则添加<p>
	var r3 = str.match(/<\/p>/i);
	if (r3 != null)
	{
		for (i = 0; i < r3.length; i++)
		{
			res = "<p>" + res;
		}
	}
	
	return res;
}
	
	
function WBTB_InsertPage(frm)
{
	var editor=document.getElementById(frm).contentWindow;
	var article = frm.replace(/^WBTB_Composition_/, "_FORM_AP_");

	if (document.myform[article].value != "Article.Content")	
	{
		alert("此模板域非文章正文属性，不能使用分页功能！");
	}
	else
	{
		WBTB_InsertSymbol(frm, "[page ======================================================================== split]");
		
		// 处理<p>...</p>
		var s = editor.document.body.innerHTML;
		var str = "";
		var tag = "";
		var temp1, temp2;
		var idx = 0;
		var i;
		while(idx != -1)
		{
			idx = s.search(/\[page\ title=[^\]]*\]/);
			if (idx != -1)
			{
				// 截为两部分
				temp1 = s.substring(0, idx);
				s = s.substr(idx);
				var j = s.search("]");
				if (j != -1)
				{
					tag = s.substring(0, j + 1);
					s = s.substr(j + 1);
				}
				else
				{
					return;
				}
				
				temp2 = WBTB_Do_P_Tag(temp1);
				
				// 拼接str
				str += temp2 + tag;
			}
			else
			{
				temp2 = WBTB_Do_P_Tag(s);
				str += temp2;
			}
		}
		
		str = str.replace(/<p>\s*<\/p>\s*(\[page\ title=[^\]]*\])/gi, "$1");
		str = str.replace(/(\[page\ title=[^\]]*\])\s*<p>\s*<\/p>/gi, "$1");
		
		editor.document.body.innerHTML = str;
	}
	
	editor.focus();
}

function WBTB_InsertTag(frm,arr,i,r,f)
{
	var editor = eval(frm);
	editor.focus();
	
	// 参数检验
	if (arr == null || arr == undefined || arr.length == undefined)
		return;		
	if (i == "")
		return;
	var index = eval(i);
	if (index == undefined || index < 0 || index >= arr.length)
		return;
	if (arr[index] == null || arr[index] == undefined || arr[index].length == undefined)
		return;
	
	// 获取标记
	var tagbegin = "";
	var tagend = "";
	if (arr[index].length > 0)
	{
		tagbegin = arr[index][0];
	}
	if (arr[index].length > 1)
	{
		tagend = arr[index][1];
	}
	
	// 加上标记
	var oSel = editor.document.selection;
	var oRange = oSel.createRange();
	
	if (oSel.type == "None" && f == 0)
		return;
		
	var txt = "";
	var str = "{C6E3CD26-4754-45e7-9664-7661524639E3}";
	if (oSel.type == "Control")
	{
		var oElem = oRange(0);
		if (!r)
		{
			txt = oElem.outerHTML;
		}
		oElem.outerHTML = str + tagbegin + txt + tagend;
	}
	else
	{
		if (!r)
		{
			txt = oRange.htmlText;
		}
		oRange.pasteHTML(str + tagbegin + txt + tagend);
		oRange.select();
	}
	
	editor.document.body.innerHTML = editor.document.body.innerHTML.replace(str, "");
	editor.focus();
}
// End added

function WBTB_FilterScript(content)
{
	content = WBTB_rCode(content, 'javascript:', 'javascript :');
	var RegExp = /<script[^>]*>/ig;
	content = content.replace(RegExp, "<!-- Script Filtered/n");
	RegExp = /<\/script>/ig;
	content = content.replace(RegExp, "/n-->");
	return content;
}

////function WBTB_cleanHtml()
function WBTB_cleanHtml(frm)
{
	var editor = document.getElementById(frm).contentWindow;
	var fonts=editor.document.getElementsByTagName("FONT");
	var curr;
	for (var i = fonts.length - 1; i >= 0; i--) {
		curr = fonts[i];
		if (curr.style.backgroundColor == "#ffffff") curr.outerHTML = curr.innerHTML;
	}
}

////function WBTB_getPureHtml()
function WBTB_getPureHtml(frm)
{
	var str = "";
	var editor = eval(frm);
	str = editor.document.body.innerHTML;
	
	str=WBTB_correctUrl(str);
	return str;
}

function WBTB_updateContent(cont)
{
	cont = WBTB_replaceFontSize(cont);
	
	// 将所有标记转换成小写
	var re = new RegExp("<\/?[A-Z]+", "g");
	var arr, str;
	while ((arr = re.exec(cont)) != null) {
		str = arr.toString();
		cont = cont.replace(str, str.toLowerCase());
	}
	
	// 替换转意字符
	while (cont.match(r1) != null || cont.match(r2) != null
			|| cont.match(r3) != null || cont.match(r4) != null)
	{
		var r1 = new RegExp("<([^>]*)&lt;([^<>]*)>", "g");
		cont = cont.replace(r1, "<$1<$2>");
		
		var r2 = new RegExp("<([^>]*)&gt;([^<>]*)>", "g");
		cont = cont.replace(r2, "<$1>$2>");
		
		var r3 = new RegExp("<([^>]*)&amp;([^<>]*)>", "g");
		cont = cont.replace(r3, "<$1&$2>");
		
		var r4 = new RegExp("<([^>]*)&quot;([^<>]*)>", "g");
		cont = cont.replace(r4, "<$1\"$2>");
	}
	
	return cont;
}

function WBTB_correctUrl(cont)
{
	// 先替换特殊字符
	cont = WBTB_updateContent(cont);
	
	var url=location.href.substring(0,location.href.lastIndexOf("/")+1);
	cont=WBTB_rCode(cont,location.href+"#","#");
	cont=WBTB_rCode(cont,url,"");

	//解决切换HTML/Design时误加内部绝对链接
	var url2 = 'href=\"' + location.href.substring(0,location.href.indexOf("/cgi-bin"));
	cont=WBTB_rCode(cont,url2,'href="');
	return cont;
}

var WBTB_bLoad=false
var WBTB_pureText=true
////var WBTB_bTextMode=true

WBTB_public_description=new WBTB_Editor

function WBTB_Editor()
{
	this.put_HtmlMode=WBTB_setMode;
	this.put_value=WBTB_putText;
	this.get_value=WBTB_getText;
}

////function WBTB_getText()
function WBTB_getText(bTextMode,frm)
{
	var editor = eval(frm);
	
	////if (WBTB_bTextMode)
	if (bTextMode)
		return editor.document.body.innerText;
	else
	{
		WBTB_cleanHtml(frm);
		
		return editor.document.body.innerHTML;
	}
}

////function WBTB_putText(v)
function WBTB_putText(v,bTextMode,frm)
{
	var editor = eval(frm);
	
	if (bTextMode)
		editor.document.body.innerText = v;
	else
		editor.document.body.innerHTML = v;
}

////function WBTB_InitDocument(hiddenid, charset)
function WBTB_InitDocument(hiddenid,charset,bTextMode,frm)
{

	return;
	if (charset!=null)
		WBTB_charset=charset;
	var WBTB_bodyTag="<html><head><style type=text/css>.quote{margin:5px 20px;border:1px solid #CCCCCC;padding:5px; background:#F3F3F3 }\nbody{boder:0px}</style></head><BODY bgcolor=\"#FFFFFF\" >";
	
	var editor = document.getElementById(frm).contentWindow;
	
	var h=document.getElementById(hiddenid);
	if(isIE){
		editor.document.designMode="On";
	}

	/*
	editor.document.open();
	editor.document.write(WBTB_bodyTag);
	if (h.value!="")
	{
		editor.document.write(h.value);
	}
	editor.document.write("</html>");
	editor.document.close();
	*/

	editor.document.charset=WBTB_charset;
	
	WBTB_bLoad=true;
	
	WBTB_setStyle(bTextMode,frm);
	
}


////function WBTB_doSelectClick(str,el) {
function WBTB_doSelectClick(str,el,bTextMode,frm) {
	var Index = el.selectedIndex;
	if (Index != 0){
		el.selectedIndex = 0;
		////WBTB_format(str,el.options[Index].value);
		WBTB_format(str,bTextMode,frm,el.options[Index].value);
	}
}

var WBTB_bIsIE5 = (navigator.userAgent.indexOf("IE 5")  > -1) || (navigator.userAgent.indexOf("IE 6")  > -1) || (navigator.userAgent.indexOf("IE 7")  > -1);
var WBTB_edit;	//selectRang
var WBTB_RangeType;
var WBTB_selection;

//应用html
function WBTB_specialtype(frm, Mark1, Mark2){
	var editor=document.getElementById(frm).contentWindow;
	var strHTML;
	if (WBTB_bIsIE5) {
		WBTB_selection = editor.document.selection;
		WBTB_edit = editor.document.selection.createRange();
	
		WBTB_RangeType =editor.document.selection.type;
	
		if (WBTB_RangeType == "Text"){
			if (Mark2==null)
			{
				strHTML = "<" + Mark1 + ">" + WBTB_edit.htmlText + "</" + Mark1 + ">";
			}else{
				strHTML = Mark1 + WBTB_edit.htmlText +  Mark2;
			}
			WBTB_edit.pasteHTML(strHTML);
			var execs = frm + ".focus()";
			eval(execs);
			WBTB_edit.select();
		}
	}
	else {
		var oContainer=editor.getSelection().getRangeAt(0).startContainer;
		if(oContainer.nodeName=="#text"){
			var selection=editor.getSelection();
	
		
			var txt = selection+'' ? selection : '';
			
			if (Mark2==null)
			{
				strHTML = "<" + Mark1 + ">" + txt + "</" + Mark1 + ">";
			}else{
				strHTML = Mark1 + txt +  Mark2;
			}
			
		}
		editor.document.execCommand("inserthtml",null,strHTML);
	}	
}

//选择内容,插入图片
function WBTB_InsertImageTag(frm,obj)
{
	//WBTB_Composition.focus();
	var execs = frm + ".focus()";
	eval(execs);
	if (WBTB_bIsIE5) {
		WBTB_selectRange(frm);
		WBTB_edit.pasteHTML(obj.value);
	}
	else
	{
		var editor=document.getElementById(frm).contentWindow;
		editor.focus();
		editor.document.execCommand("inserthtml",null, obj.value);
	}
}


//选择内容替换文本
function WBTB_InsertSymbol(frm,str1)
{
	var editor=document.getElementById(frm).contentWindow;
	editor.focus();
	if(isIE) {
			var oRng = editor.document.selection.createRange();
			oRng.pasteHTML(str1);
			oRng.collapse(false);
			oRng.select();
		
	}
	else {
		editor.document.execCommand("inserthtml",null,str1);
	}	
}


//插入视频播放代码
function WBTB_InsertVideo(frm)
{
	var win;
	if(isIE){
		var arg = new Array();
		window.open("/richtext/video.htm?iframeid="+frm, null, "width=530,height=450,status=no,scroll=no,center=yes");
	}
	else{
		win = window.open("/richtext/video.htm?iframeid="+frm, null, "Width=530,Height=450");
		win.dialogArguments = window;
	}	
}


function WBTB_selectRange(frm){
	var execs = "WBTB_selection = " + frm + ".document.selection";
	eval(execs);
	execs = "WBTB_edit = " + frm + ".document.selection.createRange()";
	eval(execs);
	execs = "WBTB_RangeType =  " + frm + ".document.selection.type";
	eval(execs);
}


function WBTB_rCode(s,a,b,i){
	//s原字串，a要换掉pattern，b换成字串，i是否区分大小写
	a = a.replace("?","\\?");
	if (i==null)
	{
		var r = new RegExp(a,"gi");
	}else if (i) {
		var r = new RegExp(a,"g");
	}
	else{
		var r = new RegExp(a,"gi");
	}
	return s.replace(r,b);
}



////function WBTB_View(objField)
function WBTB_View(url, frm, objField)
{
	if (!WBTB_bTextMode)
	{
		v_html = "";
		try{
		v_html = getIFrameHTML(frm);
		}catch(e) { alert(e); }
		objField.value = html_code_body1 +  v_html + html_code_body2;
	}

	write_temp_data(objField.value);
	window.open(url,"模板浏览");
	return;
}


// 修改编辑栏高度,design状态有效
function WBTB_Size(num,container,bTextMode)
{
	if (!bTextMode)
	{
		var obj=document.getElementById(container);
		
		if(isIE)
		{
			if (parseInt(obj.height)+num>=300)
			{
				obj.height = parseInt(obj.height) + num;
			}
		}
		else
		{
			obj.style.height = parseInt(obj.scrollHeight)  + num + "px";
		}


		if (num>0)
		{
			obj.width="100%";
		}
	}
}


// 拷贝frame数据到模板域Input对象,可保证在design状态正确提交表单
////function WBTB_CopyData(oFrm,objField)
//yongbin modified 080815
function WBTB_CopyData(oFrm,objField,bTextMode)
{
	////if (WBTB_bTextMode)
	var iframe = document.getElementById(oFrm);
	var oDoc = iframe.document;
	if (iframe.contentDocument) oDoc = iframe.contentDocument; // for NN
	if (iframe.contentWindow) oDoc = iframe.contentWindow.document; // for Mozilla etc.

	if (bTextMode) {
		return;	//支持自动排版、相关报道等
		cont = oDoc.body.innerText;
	} else {
		cont = oDoc.body.innerHTML;
	}
	cont=WBTB_correctUrl(cont);
	if (WBTB_filterScript) {
		cont=WBTB_FilterScript(cont);
	}
	
	if (cont == '<p>&nbsp;</p>') {
		cont = '';
	}
		
	objField.value = cont;
}



function WBTB_help()
{
	window.open ("/richtext/htmleditor.htm", "_blank");
}


