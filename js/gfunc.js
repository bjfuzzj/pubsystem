
// type: "error", "notice"
function showMessage(id, msg, type) {
	var div = document.createElement("div");
	div.id = "gsps_" + type + "_bar";
	div.className = "gsps_" + type;
	div.innerHTML = '<a style="float:right" href="#" onclick="gspsMsgRead('+id+', \''+type+'\');return false;">[关闭]</a>' + msg ;
	
	if (type == "notice") {
		return document.body && document.body.insertBefore(div, document.body.firstChild);
	} else {
		window.onload = function(){
			if (document.body)
				document.body.insertBefore(div, document.body.firstChild);
		}
	}
}

// GLOABAL error handler, yongbin090506
// commented in 143.13, for debug
/*
window.onerror = function (msg, url, line) {
	var html = '<b style="color:red">JavaScript Error: </b>' + msg +' -- 行：' + line +' @ ' + url + ' 部分功能可能无法使用，请将错误信息通知到发布系统后台。';
	showMessage(null, html, "error");
	return true;
}
*/

// Prototype IO.Script method
if(typeof IO=='undefined')IO={};IO.Script=function(){this.Init.apply(this,arguments);};IO.Script.prototype={Init:function(opts,c){if(opts){if(opts.randomURL)this.m=true;}if(c)this.l=c;else this.l=document.getElementsByTagName('head')[0];},load:function(url,callback,callback_valname){if(this.m){var tok=(url.indexOf('?')==-1)?'?':'&';url+=tok+Math.round(Math.random()*2147483648);}var s=this.f();if(callback){if(typeof callback=='string'&&callback_valname){this.k(s,url,callback,callback_valname);}else this.j(s,url,callback);}else this.g(s,url);},j:function(s,url,cb){s.src=url;this.l.appendChild(s);if(s.addEventListener){s.addEventListener('load',this.d(s,cb),false);}else if(s.attachEvent){s.attachEvent('onreadystatechange',this.d(s,cb));}},k:function(s,url,cb_name,cb_valname){var tok=(url.indexOf('?')==-1)?'?':'&';url+=tok+cb_valname+'='+cb_name;s.src=url;this.l.appendChild(s);this.e(s);},g:function(s,url){s.src=url;this.l.appendChild(s);this.e(s);},f:function(){var s=document.createElement('script');s.type='text/javascript';return s;},e:function(s){if(s.addEventListener){s.addEventListener('load',this.n(s),false);}else if(s.attachEvent){s.attachEvent('onreadystatechange',this.n(s));}},d:function(s,cb){return function(){if(s.readyState){if(s.readyState!='loading'){cb(s);}}else{cb(s);}}},n:function(s){return function(){if(s.readyState){if(s.readyState=='loaded'||s.readyState=='complete'){s.parentNode.removeChild(s);}}else{s.parentNode.removeChild(s);}}}} 

/*
var gsps_notice_url = "http://236.pub.sina.com.cn:8080/sina/standard/interface/44/2009/0506/1.js";
try {
	window.setTimeout(function() {
		(new IO.Script()).load(gsps_notice_url, gspsNotice);
	}, 10000);
}catch(e){
}
*/


// callback function
function gspsNotice() {
	
	if (gsps_msg && gsps_msg.msg != "") {
		
		var gsps_cookie = getCookie("gsps_store_notice");
		
		var remote_id = gsps_msg.id;
		
		function setCookieAndShowMsg(){
			
			setCookie("gsps_store_notice", gsps_msg.id + "|unread", 365, "", "pub.sina.com.cn");
			showMessage(gsps_msg.id, gsps_msg.msg, "notice");
		}
		
		// cookie exists
		if (gsps_cookie) {
			var local_id = gsps_cookie.split("|")[0];
			var msg_read = gsps_cookie.split("|")[1];
						
			if (remote_id == local_id) {
				// same id, check if the message is not read
				if (msg_read == "unread") {
					showMessage(gsps_msg.id, gsps_msg.msg, "notice");
				}
			} else {
				// this is a new message from gsps
				setCookieAndShowMsg();
			}
		} else {
			// no cookie for now, this is a new msg
			setCookieAndShowMsg();
		}
	}
}

// when "close" button cicked
function gspsMsgRead(id, type) {
	if (type == "notice") {
		
		setCookie("gsps_store_notice", id + "|read", 365, "", "pub.sina.com.cn");
		
		var notice_div = document.getElementById("gsps_notice_bar");
		document.body.removeChild(notice_div);
	}else if (type == "error") {
		var error_div = document.getElementById("gsps_error_bar");
		document.body.removeChild(error_div);
	}
	
}

// gsps notice end

//----------------------------------------------------------------------
//PART-I
//数据字典表单处理
//----------------------------------------------------------------------
var FIELD_RULE = "_PF_";
var VERIFY_RULE = "_VF_";
var FIELD_CNAME_RULE = "_FCR_";
var FIELD_TYPE_RULE = "_FTR_";


var field_rule = FIELD_RULE;
var verify_rule = VERIFY_RULE;
var field_cname_rule = FIELD_CNAME_RULE;
var field_type_rule = FIELD_TYPE_RULE;
var field_rule_len = field_rule.length;
var verify_rule_len = verify_rule.length;
	
	
function verifyNotNull(value)
{
	if(value == null)
	{
		return false;
	}
	else
	{
		if(value.length ==0 )
		{
			return false;
		}
	}
	return true;
}

function verifyNumeric(value)
{
	if(value == null || value == '')
	{
		return false;
	}
	return !isNaN(value);
}

function verifyCommon(object,title,value,type)
{
	if(type == 'Int' || type == 'Float')
	{
		if(!verifyNumeric(value))
		{
			alert(title+"必须为数值！");
			object.focus();
			return false;
		}
	}
	return true;
}


function verifyLength(value,maxLen)
{
	if(value == null || value == '')
	{
		return false;
	}
	var len = value.length;
	var count = 0;
	for(var i=0;i<len;i++)
	{
		var ascii = value.charCodeAt(i);
		if(ascii > 127)
		{
			count += 2;
		}
		else
		{
			count++;
		}
	}
	if(count > maxLen)
	{
		return false;
	}
	return true;
}


function getFormFieldValue(form,fieldName)
{
	var len;
	var index = 0;
	var object;
	var type;
	var value;
	len = form.elements.length;
	for(index=0;index<len;index++)
	{
		object = form.elements[index];
		if(object.name == fieldName)
		{
			type = object.type;
			if(type == "text" || type == "password" || type == "textarea" || type == "file" || type == "hidden")
			{
				return object.value;
			}
			else if(type == "select-one")
			{
				return object.options[object.selectedIndex].value;
			}			
			else
			{
				return null;
			}
		}
	}
	return null;
}



function actionclick(form)
{
	len = form.elements.length;
	var index;
	var fieldName;
	var very_notnull_fieldName;
	var very_numeric_fieldName;
	var very_length_fieldName;
	var verify_cname_fieldName;
	var verify_cname_fieldName_value;
	var verify_type_fieldName;
	var verify_type_fieldName_value;
	var value;
	for(index=0;index<len;index++)
	{
		var object = form.elements[index];
		fieldName = form.elements[index].name;
		if(fieldName.substr(0,field_rule_len) == field_rule)
		{
			if(object.disabled)
			{
				continue;
			}
			verify_cname_fieldName = field_cname_rule + fieldName.substr(field_rule.length);
			verify_cname_fieldName_value = getFormFieldValue(form,verify_cname_fieldName);
			value = getFormFieldValue(form,fieldName);
			verify_notnull_fieldName = verify_rule + "_NOTNULL_" + fieldName.substr(field_rule.length);
			var verify_notnull_value = getFormFieldValue(form,verify_notnull_fieldName);



			if(verify_notnull_value == "TRUE")
			{
				if(!verifyNotNull(value))
				{
					alert(verify_cname_fieldName_value+"不能为空！");
					form.elements[index].focus();
					return true;
				}
			}
			else
			{
				continue;
			}


			very_length_fieldName = verify_rule + "_LENGTH_" + fieldName.substr(field_rule.length);
			var verify_length_value = getFormFieldValue(form,very_length_fieldName);
			var i_verify_length_value = parseInt(verify_length_value);
			if(i_verify_length_value != 0)
			{
				if(!verifyLength(value,i_verify_length_value))
				{
					alert(verify_cname_fieldName_value+"的最大长度为"+i_verify_length_value);
					form.elements[index].focus();
					return true;
				}
			}

			
			very_numeric_fieldName = verify_rule + "_NUMERIC_" + fieldName.substr(field_rule.length);
			var verify_numeric_value = getFormFieldValue(form,very_numeric_fieldName);
			if(verify_numeric_value == "TRUE")
			{
				if(!verifyNumeric(value))
				{
					alert(verify_cname_fieldName_value+"必须为数值");
					form.elements[index].focus();
					return true;
				}
			}
			
			//通用验证：为Int,Float类型
			verify_type_fieldName = field_type_rule + fieldName.substr(field_rule.length);
			verify_type_fieldName_value = getFormFieldValue(form,verify_type_fieldName);
			
			if(!verifyCommon(object,verify_cname_fieldName_value,value,verify_type_fieldName_value))
			{
				return true;
			}
		}
	}
	form.submit();
}



function validclick(form)
{
	len = form.elements.length;
	var index;
	var fieldName;
	var very_notnull_fieldName;
	var very_numeric_fieldName;
	var very_length_fieldName;
	var verify_cname_fieldName;
	var verify_cname_fieldName_value;
	var verify_type_fieldName;
	var verify_type_fieldName_value;
	var value;
	for(index=0;index<len;index++)
	{
		var object = form.elements[index];
		fieldName = form.elements[index].name;
		if(fieldName.substr(0,field_rule_len) == field_rule)
		{
			if(object.disabled)
			{
				continue;
			}
			verify_cname_fieldName = field_cname_rule + fieldName.substr(field_rule.length);
			verify_cname_fieldName_value = getFormFieldValue(form,verify_cname_fieldName);
			value = getFormFieldValue(form,fieldName);
			verify_notnull_fieldName = verify_rule + "_NOTNULL_" + fieldName.substr(field_rule.length);
			var verify_notnull_value = getFormFieldValue(form,verify_notnull_fieldName);
			if(verify_notnull_value == "TRUE")
			{
				if(!verifyNotNull(value))
				{
					alert(verify_cname_fieldName_value+"不能为空！");
					form.elements[index].focus();
					return false;
				}
			}
			else
			{
				continue;
			}


			very_length_fieldName = verify_rule + "_LENGTH_" + fieldName.substr(field_rule.length);
			var verify_length_value = getFormFieldValue(form,very_length_fieldName);
			var i_verify_length_value = parseInt(verify_length_value);
			if(i_verify_length_value != 0)
			{
				if(!verifyLength(value,i_verify_length_value))
				{
					alert(verify_cname_fieldName_value+"的最大长度为"+i_verify_length_value);
					form.elements[index].focus();
					return false;
				}
			}

			
			very_numeric_fieldName = verify_rule + "_NUMERIC_" + fieldName.substr(field_rule.length);
			var verify_numeric_value = getFormFieldValue(form,very_numeric_fieldName);
			if(verify_numeric_value == "TRUE")
			{
				if(!verifyNumeric(value))
				{
					alert(verify_cname_fieldName_value+"必须为数值");
					form.elements[index].focus();
					return false;
				}
			}
			
			//通用验证：为Int,Float类型
			verify_type_fieldName = field_type_rule + fieldName.substr(field_rule.length);
			verify_type_fieldName_value = getFormFieldValue(form,verify_type_fieldName);
			
			if(!verifyCommon(object,verify_cname_fieldName_value,value,verify_type_fieldName_value))
			{
				return false;
			}
		}
	}
	return true;
}

/*
 * Duplicated with JSFunc.js
 * Yongbin commented, 090506
 */

//----------------------------------------------------------------------
//PART-II
//Cookie处理
//----------------------------------------------------------------------


// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
/*
function setCookie(name, value, expires, path, domain, secure)
{
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}


// name - name of the desired cookie
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name)
{
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1)
  {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } 
  else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}


// name - name of the cookie
// [path] - path of the cookie (must be same as path used to create cookie)
// [domain] - domain of the cookie (must be same as domain used to create cookie)
// * path and domain default if assigned null or omitted if no explicit argument proceeds
function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" + 
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}
*/

// date - any instance of the Date object
// * hand all instances of the Date object to this function for "repairs"
function fixDate(date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}


/**
* 检查长度
* @param string tagname 表单元素name
* @return true,false, true表示长度符合要求。长度上下限数值在模板域中设置
* @yongbin 080901
*/
function checkLength(tagname) {
	
	var maxName = tagname.replace(/_FORM_(?:.*?)_sp_(.*?)/i, "_FORM_VF__MAX_LENGTH_sp_$1");
	var minName = tagname.replace(/_FORM_(?:.*?)_sp_(.*?)/i, "_FORM_VF__MIN_LENGTH_sp_$1");

	var tagvalue = document.getElementsByName(tagname)[0].value;
	
	var areaTagName = tagname.replace(/_FORM_(?:.*?)_sp_(.*?)/i, "_FORM_FCR_sp_$1");
	
	var areaName;
	try {
		areaName = document.getElementsByName(areaTagName)[0].value;
	}catch(e) {
		areaName = tagname;
	}
	
	var max, min;
	try {
		max = parseInt(document.getElementsByName(maxName)[0].value, 10);
	}catch(e) {
		max = 0;
	}
	try {
		min = parseInt(document.getElementsByName(minName)[0].value, 10);
	}catch(e) {
		min = 0;
	}
	// calc real length
	function strLen(str){
		var len;var i;len=0;
		for (i=0;i<str.length;i++){
			if (str.charCodeAt(i)>255) len+=2; else len++;
		}
		return len;
	}
	var l = strLen(tagvalue.replace("\n", "\r\n"));
	if (max != 0) {
		if (l > max) {
			// 文本长度超标
			alert(areaName + "区域，文本长度超出限制(" + max + "字节)，无法发布！");
			return false;
		}
	}
	if (min != 0) {				
		if (l < min) {
			// 文本长度过短
			alert(areaName + "元素文本长度过短(下限"+min+"字节)，无法发布！");
			return false;
		}
	}

	return true;
}

//-----------------------------------------
// 检查HTML代码合法性
//-----------------------------------------

/**
* 检查标签匹配闭合
* @param string tagname 表单元素name
* @return 1,0, 1表示闭合良好
* @yongbin 080828, 加入注释检测。修订原检测方式。
*/
function checkHTMLTag(tagname, noAlert) {
	var retag = document.getElementsByName(tagname)[0].value;
	
	function compare(txt1, txt2) {
		var txt='<'+'/'+txt1.substr(1);
		return (txt==txt2)?1:0;
	}
	function checkComment(txt) {
		var _ret;
		txt = txt.replace(/\r|\n/ig,'');
		txt = txt.replace(/<style *[^<>]*>.*?<\/style>/ig,'');
		txt = txt.replace(/<script *[^<>]*>.*?<\/script>/ig,'');
		if (/<\!-+>/ig.test(txt) || /<\!--(-+(.*?)|(.*?)-+)-->/ig.test(txt)) {
			_ret = false;
		}else {
			if (txt.split("<!--").length != txt.split("-->").length) _ret = false;
			else {
				_ret = true;
			}
		}
		return _ret;
	}
	
	if (retag == '') {
		return 1;	//空串直接返回1
	}
	
	// pass comment tag checker?
	if (checkComment(retag)==false) {
		if (!noAlert) alert("注释代码错误，请遵守<!-- comment -->格式！\n提示：<!--->, <!----->, <!--- comment -->等等都是不规范的注释，常常会造成问题。");
		return 0;
	} else {
		retag = retag.replace(/\r|\n/ig,'');	//除去回车和换行	
		
		// 去掉不能很好控制的<script>...<\/script>和<!--...-->
		retag = retag.replace(/<style *[^<>]*>.*?<\/style>/ig,'');
		retag = retag.replace(/<script *[^<>]*>.*?<\/script>/ig,'');
		retag = retag.replace(/<\!--.*?-->/ig,'');
		
		var arrIntElement = retag.match(/<\/?[A-Za-z][a-z0-9]*[^>]*>/ig);
		
		if (arrIntElement != null) {

			
			//预处理标签,得到规整的标签数组,去掉所有属性只留下<a>和</a>
			var arrPrElement = Array();
			for (var k=0; k<arrIntElement.length; k++) {
				arrPrElement[k] = arrIntElement[k].replace(/(<\/?[A-Za-z0-9]+) *[^>]*>/ig,"$1>");
				arrPrElement[k]= arrPrElement[k].replace(/[\s]+/g,'').toLowerCase();
			}
	
			//不需要配对的标签,小写
			var arrMinus=new Array('<img>','<input>','<meta>','<hr>','<br>','<link>','<param>','<frame>','<base>','<basefont>','<isindex>','<area>');
	
			//去掉多余的单标签标记,返回新的arrIntElement
			for (var i=0; i<arrPrElement.length; i++) {
				for (var k=0; k<arrMinus.length; k++) {
					if (arrPrElement[i] == arrMinus[k]) {
						arrPrElement.splice(i, 1);
						i--;
					}
				}
			}
	
			//判断<aaa>与</aaa>是配对的html标签
			var stack=new Array();
			stack[0]='#';
			var p = 0;
			var problem;
			for (var j=0; j<arrPrElement.length; j++) {
				if (compare(stack[p], arrPrElement[j])) {
					p--;
					stack.length--;
				}
				else {
					stack[++p]=arrPrElement[j];
				}
			}
	
			if (stack[p]!="#") {
				if (!noAlert) alert("html标签不匹配，请检查是不是漏了</a>,</div>,</li>,</ul>,</font>等等");
				return 0;
			}
			
			//双引号和单引号完整性检查
			for (var k=0; k<arrIntElement.length; k++) {
				var rr = arrIntElement[k].match(/\"/ig);
				var r = arrIntElement[k].match(/\'/ig);
				if (rr != null) {
					if (rr.length % 2 != 0) {
						if (!noAlert) alert("警告：" + arrIntElement[k]+" 双引号不完整");
						return 0;
					}
				}
				if (r != null) {
					if (r.length % 2 != 0) {
						if (!noAlert) alert("警告：" + arrIntElement[k]+" 单引号不完整");
						return 0;
					}
				}
			}
		}
	}

	return 1;
}
