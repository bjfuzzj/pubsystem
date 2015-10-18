
function getCookie( check_name ) {
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false;
	
	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		a_temp_cookie = a_all_cookies[i].split( '=' );
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found ) 
	{
		return null;
	}
}

function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires )
	{
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );

	document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + //expires.toGMTString()
		( ( path ) ? ";path=" + path : "" ) + 
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}

function deleteCookie( name, path, domain ) {
	if ( getCookie( name ) ) document.cookie = name + "=" +
			( ( path ) ? ";path=" + path : "") +
			( ( domain ) ? ";domain=" + domain : "" ) +
			";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

// customize css theme, yongbin 081006
function loadCSS(isIE6){
	var file_name;
	if (isIE6) 
		file_name = "Default";
	else 
		file_name = getCookie("CustomizeTheme") || "Default";
	var file_ref = document.createElement("link");
	file_ref.setAttribute("rel", "stylesheet");
	file_ref.setAttribute("type", "text/css");
	file_ref.setAttribute("href", "/gsps/css/Customize/" + file_name + ".css");
	document.getElementsByTagName("head")[0].appendChild(file_ref);
}
var ie6 = /MSIE/i.test(navigator.userAgent);
loadCSS(ie6);

//----------------------------------------------------------------------
// PART-0
// 数据表单自动校验处理
//----------------------------------------------------------------------

var DICT_FIELD_NAME_PREFIX = "_FORM_PF_";
var DICT_FIELD_NAME_PREFIX_LEN = DICT_FIELD_NAME_PREFIX.length;
var DICT_FIELD_VERIFY_PREFIX = "_FORM_VF_";
var DICT_FIELD_VERIFY_PREFIX_LEN = DICT_FIELD_VERIFY_PREFIX.length;
var DICT_VERIFY_NOT_NULL_PREFIX = DICT_FIELD_VERIFY_PREFIX + "_NOTNULL_";
var DICT_VERIFY_NOT_NULL_PREFIX_LEN = DICT_VERIFY_NOT_NULL_PREFIX.length;
var DICT_VERIFY_MIN_LEN_PREFIX = DICT_FIELD_VERIFY_PREFIX + "_MIN_LENGTH_";
var DICT_VERIFY_MIN_LEN_PREFIX_LEN = DICT_VERIFY_MIN_LEN_PREFIX.length;
var DICT_VERIFY_MAX_LEN_PREFIX = DICT_FIELD_VERIFY_PREFIX + "_MAX_LENGTH_";
var DICT_VERIFY_MAX_LEN_PREFIX_LEN = DICT_VERIFY_MAX_LEN_PREFIX.length;
var DICT_FIELD_CNAME_PREFIX = "_FORM_FCR_";
var DICT_FIELD_DEFAULT_VALUE_PREFIX = "_FORM_FDV_";
var DICT_FIELD_CNAME_PREFIX_LEN = DICT_FIELD_CNAME_PREFIX.length;
var DICT_VERIFY_TYPE_PREFIX = "_FORM_VF__FTR_";
var DICT_VERIFY_TYPE_PREFIX_LEN = DICT_VERIFY_TYPE_PREFIX.length;

//----------------------------------------------------------------------
// 判断指定的对象是否为空
//----------------------------------------------------------------------
function isNULL(value)
{
    if(value == null)
    {
        return true;
    }
    return false;
}

//----------------------------------------------------------------------
// 判断指定的值是否为空
//----------------------------------------------------------------------
function isBlank(value)
{
    if(isNULL(value))
    {
        return true;
    }
    if(value.length ==0 )
    {
        return true;
    }
    return false;
}

//----------------------------------------------------------------------
// 判断指定的串是否为日期格式(YYYY-MM-DD)
//----------------------------------------------------------------------
function isDate(value)
{
	if(isBlank(value))
	{
		return false;
	}
	var re = new RegExp(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
	if(value.match(re))
	{
		if(RegExp.$1 < 1900 || RegExp.$1 > 2037)
		{
			return false;
		}
		if(RegExp.$2 <= 0 || RegExp.$2 > 12)
		{
			return false;
		}
		if(RegExp.$3 <= 0 || RegExp.$3 > 31)
		{
			return false;
		}
		return true;
	}
	return false;
}

//----------------------------------------------------------------------
// 判断指定的串是否为时间格式(HH:MM:SS)
//----------------------------------------------------------------------
function isTime(value)
{
	if(isBlank(value))
	{
		return false;
	}
	var re = new RegExp(/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/);
	if(value.match(re))
	{
		if(RegExp.$1 < 0 || RegExp.$1 >= 24)
		{
			return false;
		}
		if(RegExp.$2 < 0 || RegExp.$2 > 59)
		{
			return false;
		}
		if(RegExp.$3 < 0 || RegExp.$3 > 59)
		{
			return false;
		}
		return true;
	}
	return false;
}

//----------------------------------------------------------------------
// 判断指定的串是否为日期时间格式(YYYY-MM-DD HH:MM:SS)
//----------------------------------------------------------------------
function isDateTime(value)
{
	var re = new RegExp(/^(.*) (.*)$/);
	if(value.match(re))
	{
		var date = RegExp.$1;
		var time = RegExp.$2;
		if(!isDate(date))
		{
			return false;
		}
		if(!isTime(time))
		{
			return false;
		}
		return true;
	}
	return false;
}

//----------------------------------------------------------------------
// 判断指定的串是否为数值
//----------------------------------------------------------------------
function isNumber(value)
{
    if(isBlank(value))
    {
        return false;
    }
    return !isNaN(value);
}

//----------------------------------------------------------------------
// 判断指定的串是否为整数
//----------------------------------------------------------------------
function isInt(value)
{
	if(!isNumber(value))
	{
		return false;
	}
	if(value.indexOf(".") == -1)
	{
		return true;
	}
	return false;
}



//----------------------------------------------------------------------
// 判断指定的串是否为正整数
//----------------------------------------------------------------------
function isUInt(value)
{
	if(!isInt(value))
	{
		return false;
	}
	var iValue = parseInt(value);
	if(iValue < 0)
	{
		return false;
	}
	return true;
}

//----------------------------------------------------------------------
// 判断指定的串是否为浮点数
//----------------------------------------------------------------------
function isDouble(value)
{
	return isNumber(value);
}


//----------------------------------------------------------------------
// 判断指定的串是否为正浮点数
//----------------------------------------------------------------------
function isUDouble(value)
{
	if(!isNumber(value))
	{
		return false;
	}
	if(parseFloat(value) < 0.0)
	{
		return false;
	}
	return true;
}


//----------------------------------------------------------------------
// 获取串的长度(考虑到汉字的双字节编码)
//----------------------------------------------------------------------
function getLength(value)
{
    if(isBlank(value))
    {
        return 0;
    }
    var count = 0;
    var len = value.length;
    for(var i=0; i<len; i++)
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
    return count;
}


//----------------------------------------------------------------------
// 按表单元素名称获取其值
// 如果是多个索引元素那么返回值用||分隔
//----------------------------------------------------------------------
function getValueByName(oForm, name)
{
	//alert(name);
	var obj = oForm[name];
	if(!obj)
	{
		return null;
	}

	var type = obj.type;
	if(type == null || typeof(type) == "object")
	{
		var len = obj.length;
		var iobj;
		var ivalue;
		var value = null;
		var j=0;
		for(var i=0; i<len; i++)
		{
			iobj = oForm[name][i];
			ivalue = getValueByRef(oForm, iobj);
			if(ivalue != null)
			{
				if(j == 0)
				{
					value = ivalue;
				}
				else
				{
					value += "||" + ivalue;
				}
				j++;
			}
		}
		return value;
	}
	else
	{
		return getValueByRef(oForm, obj);
	}
}


//----------------------------------------------------------------------
// 设置表单值为指定的参数值(通过名字引用对象)
//----------------------------------------------------------------------
function setValueByName(oForm, name, value)
{
    var obj = oForm.elements(name);
    if(!obj)
    {
        return null;
    }

    var type = obj.type;
    if(type == null)
    {
        var len = obj.length;
        var iobj;
        for(var i=0; i<len; i++)
        {
            iobj = oForm.elements(name, i);
            setValueByRef(oForm, iobj, value);
        }
    }
    else
    {
        return setValueByRef(oForm, obj, value);
    }
}


//----------------------------------------------------------------------
// 按引用获取指定表单对象的值
// 如果是多个索引元素那么返回值用||分隔
//----------------------------------------------------------------------
function getValueByRef(oForm, obj)
{
	var oColl = obj;

	//------------------------------------------------------------------
	//该表单元素不存在
	//------------------------------------------------------------------
	if(!obj)
	{
		return null;
	}

	//------------------------------------------------------------------
	//该表单元素类型
	//------------------------------------------------------------------
	var type = obj.type;

	if(type == "checkbox" || type == "radio")
	{
		//--------------------------------------------------------------
		// 表单元素为Single CheckBox
		//--------------------------------------------------------------
		if(obj.checked)
		{
			return obj.value;
		}
	}
	else if(type == "select-one")
	{
		//--------------------------------------------------------------
		// 表单元素为Single Select
		//--------------------------------------------------------------
		if(obj.selectedIndex != -1)
		{
            return obj.options[obj.selectedIndex].value;
		}
	}
	else if(type == "select-multiple")
	{
		//--------------------------------------------------------------
		// 表单元素为Multiple Select
		//--------------------------------------------------------------
		var j = 0;
		var value;
		if (obj.length > 0)
		{
			for (i=0; i< obj.options.length; i++)
			{
				if(obj.options(i).selected)
				{
					if(j == 0)
					{
						value = obj.options(i).value;
					}
					else
					{
						value += "||" + obj.options(i).value;
					}
					j++;
				}
			}
		}
		if(j > 0)
		{
			return value;
		}
	}
	else
	{
		return obj.value;
	}
	return null;
}


//----------------------------------------------------------------------
// 设置表单值为指定的参数值(通过引用操作对象)
//----------------------------------------------------------------------
function setValueByRef(oForm, obj, f_value)
{
	var oColl = obj;

	//------------------------------------------------------------------
	//该表单元素不存在
	//------------------------------------------------------------------
	if (!obj)
	{
		return null;
	}

	//------------------------------------------------------------------
	//该表单元素类型
	//------------------------------------------------------------------
	var type = obj.type;

	if (type == "checkbox" || type == "radio")
	{
		//--------------------------------------------------------------
		// 表单元素为Single CheckBox
		//--------------------------------------------------------------
		if (obj.value == f_value)
		{
			obj.checked = true;
		}
		else
		{
			obj.checked = false;
		}
	}
	else if (type == "select-one")
	{
		//--------------------------------------------------------------
		// 表单元素为Single Select
		//--------------------------------------------------------------
		obj.value = f_value;
	}
	else if (type == "select-multiple")
	{
		//--------------------------------------------------------------
		// 表单元素为Multiple Select
		//--------------------------------------------------------------
		if (obj.length > 0)
		{
			var i;
			for (i=0; i< obj.options.length; i++)
			{
  				if (obj.options(i).value == f_value)
				{
					obj.options(i).selected = true;
				}
				else
				{
					obj.options(i).selected = false;
				}
			}
		}
	}
	else
	{
		obj.value = f_value;
	}
}


//-----------------------------------------------------------------------------------------------
// 定义数据字典元对象
// 参数：
//		oForm:表单Form对象
//		obj:表单元素对象
//-----------------------------------------------------------------------------------------------
function FormField(oForm, obj)
{
	this.oForm = oForm;
	this.obj = obj;
	this.name = "";
	if(obj == null)
	{
		this.name = "";
	}
	else
	{
		this.name = obj.name;
	}

	//-------------------------------------------------------------------------------------------
	// 判断当前表单元素是否为字典对象
	//-------------------------------------------------------------------------------------------
	this.isDictField = function()
	{
		if(this.name.substr(0,  DICT_FIELD_NAME_PREFIX_LEN) == DICT_FIELD_NAME_PREFIX)
		{
			return true;
		}
		return false;
	}

	 //-------------------------------------------------------------------------------------------
	// 获取下一个表单字典元素
	//-------------------------------------------------------------------------------------------
	this.Next = function()
	{
		var cobj;
		var field;
		var count = oForm.elements.length;
		var found = false;
		if (obj == null)
		{
			obj = oForm.elements[0];
			return this.Next();
		}
		for(var i=0; i<count; i++)
		{
			cobj = oForm.elements[i];
			field = new FormField(oForm, cobj);
			if(cobj == obj)
			{
				found = true;
				continue;
			}
			if(found && field.isDictField())
			{
				return new FormField(oForm, cobj);
			}
		}
		return null;
	}

	 //-------------------------------------------------------------------------------------------
	// 判断当前字典表单元素是否隐藏元素
	//-------------------------------------------------------------------------------------------
	this.isHidden = function()
	{
		if(obj.type == "hidden")
		{
			return true;
		}
		return false;
	}

	//-------------------------------------------------------------------------------------------
	// 获取当前表单元素的名称
	//-------------------------------------------------------------------------------------------
	this.Name = function()
	{
		return this.name;
	}

	//-------------------------------------------------------------------------------------------
	// 获取当前表单元素的值
	//-------------------------------------------------------------------------------------------
	this.Value = function()
	{
		return getValueByName(this.oForm, this.name);
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段名称
	//-------------------------------------------------------------------------------------------
	this.FieldName = function()
	{
		return this.name.substr(DICT_FIELD_NAME_PREFIX_LEN);
	}

	//-------------------------------------------------------------------------------------------
	// 获取当前字段的值
	//-------------------------------------------------------------------------------------------
	this.FieldValue = function()
	{
		return this.Value();
	}

	//-------------------------------------------------------------------------------------------
	// 设置当前表单字段的值为指定的值，如果为选择列表则选中，如未出现该值，默认不进行任何操作
	//-------------------------------------------------------------------------------------------
	this.setFieldValue = function(f_value)
	{
		setValueByName(this.oForm, DICT_FIELD_NAME_PREFIX + this.FieldName(), f_value);
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段中文名称
	//-------------------------------------------------------------------------------------------
	this.FieldCName = function()
	{
		return getValueByName(this.oForm, DICT_FIELD_CNAME_PREFIX + this.FieldName());
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段缺省值
	//-------------------------------------------------------------------------------------------
	this.FieldDefaultValue = function()
	{
		return getValueByName(this.oForm, DICT_FIELD_DEFAULT_VALUE_PREFIX + this.FieldName());
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段校验类型
	//-------------------------------------------------------------------------------------------
	this.VerifyType = function()
	{
		return getValueByName(this.oForm, DICT_VERIFY_TYPE_PREFIX + this.FieldName());
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段最小校验长度
	//-------------------------------------------------------------------------------------------
	this.VerifyMinLen = function()
	{
		return getValueByName(this.oForm, DICT_VERIFY_MIN_LEN_PREFIX + this.FieldName());
	}

	//-------------------------------------------------------------------------------------------
	// 获取数据字典字段最大校验长度
	//-------------------------------------------------------------------------------------------
	this.VerifyMaxLen = function()
	{
		return getValueByName(this.oForm, DICT_VERIFY_MAX_LEN_PREFIX + this.FieldName());
	}

	//-------------------------------------------------------------------------------------------
	// 是否数据字典字段值不能为空
	//-------------------------------------------------------------------------------------------
	this.VerifyMustNotNULL = function()
	{
		var value = getValueByName(this.oForm, DICT_VERIFY_NOT_NULL_PREFIX + this.FieldName());
		if(value == null
			|| value == "FALSE")
		{
			return false;
		}
		return true;
	}

	//-------------------------------------------------------------------------------------------
	// 该元素是否通过表单空值校验
	//-------------------------------------------------------------------------------------------
	this.PassNotNULLVerify = function()
	{
		if(this.VerifyMustNotNULL())
		{
			var f_value = this.FieldValue();
			//alert(f_value);

			if(f_value == null)
			{
				return false;
			}
			if(isBlank(f_value))
			{
				return false;
			}
		}
		return true;
	}


	//-------------------------------------------------------------------------------------------
	// 是否数据字典字段通过类型校验
	//-------------------------------------------------------------------------------------------
	this.PassTypeVerify = function()
	{
		//---------------------------------------------------------------------------------------
		// 表单字段值
		//---------------------------------------------------------------------------------------
		var f_value = this.FieldValue();

		//---------------------------------------------------------------------------------------
		// 表单字段校验类型
		//---------------------------------------------------------------------------------------
		var vf_type = this.VerifyType();

		if(vf_type == "Int" && !isInt(f_value))
		{
			return false;
		}
		else if(vf_type == "UInt" && !isUInt(f_value))
		{
			return false;
		}
		else if(vf_type == "Double" && !isDouble(f_value))
		{
			return false;
		}
		else if(vf_type == "UDouble" && !isUDouble(f_value))
		{
			return false;
		}
		else if(vf_type == "Date" && !isDate(f_value))
		{
			return false;
		}
		else if(vf_type == "Time" && !isTime(f_value))
		{
			return false;
		}
		else if(vf_type == "DateTime" && !isDateTime(f_value))
		{
			return false;
		}
		return true;
	}


	//-------------------------------------------------------------------------------------------
	// 该元素是否通过表单长度校验
	//-------------------------------------------------------------------------------------------
	this.PassLengthVerify = function()
	{
		//---------------------------------------------------------------------------------------
		// 表单字段值
		//---------------------------------------------------------------------------------------
		var f_value = this.FieldValue();

		//---------------------------------------------------------------------------------------
		// 表单字段值长度
		//---------------------------------------------------------------------------------------
		var value_len = getLength(f_value);

		//---------------------------------------------------------------------------------------
		// 获取表单元素最小长度校验值
		//---------------------------------------------------------------------------------------
		var minLenName = DICT_VERIFY_MIN_LEN_PREFIX + this.FieldName();
		var minLen = getValueByName(this.oForm, minLenName);

		//---------------------------------------------------------------------------------------
		// 获取表单元素最大长度校验值
		//---------------------------------------------------------------------------------------
		var maxLenName = DICT_VERIFY_MAX_LEN_PREFIX + this.FieldName();
		var maxLen = getValueByName(this.oForm, maxLenName);

		if(minLen == 0 && maxLen == 0)
		{
			return true;
		}

		//---------------------------------------------------------------------------------------
		// 不应该出现该情况
		//---------------------------------------------------------------------------------------
		if(minLen > maxLen)
		{
			return true;
		}

		if(value_len < minLen || value_len > maxLen)
		{
			return false;
		}

		return true;
	}


	//-------------------------------------------------------------------------------------------
	// 返回对当前表单对象的引用
	//-------------------------------------------------------------------------------------------
	this.Me = function()
	{
		 return this.obj;
	}

	//-------------------------------------------------------------------------------------------
	// 该元素是否通过表单长度校验
	//-------------------------------------------------------------------------------------------
	this.Focus = function()
	{
		if(this.obj.type != "hidden")
		{
			this.obj.focus();
		}
	}
}


//-----------------------------------------------------------------------------------------------
// 执行表单自动校验
// 参数：
//		oForm:表单Form对象
//		oSender:当前触发事件的对象
//		ifUseKeyword:是否启用关键词判断
// 返回值:
//		true:表单校验通过
//		false:表单检验未通过
//-----------------------------------------------------------------------------------------------

function PassFormVerify(oForm, oSender, ifUseKeyword)
{
	//暂停此校验，包括告警关键字
	//return 0;
	
	// yongbin, trigger save tinyeditor
	try {
		tinyMCE.triggerSave();
	}catch(e){};

	
	var obj;
	var obj_name;
	var field;
	var field_cn_name;
	var field_vtype;
	
	var length = oForm.elements.length;
	var totalString="";
	var alertString = "";
	var elementsAdd = new Array(length);  //记录每个表单元素字符串在合并字符串的结束位置
	var elementsName = new Array(length); //记录每个表单元素的名称
	var objList = new Array(length);
	var size=0;
	
	var total = 0;
	for(var i=0; i<oForm.elements.length; i++)
	{
		total++;
		obj = oForm.elements[i];
		obj_name = obj.name;
		//if(obj_name.substr(0,  DICT_FIELD_NAME_PREFIX_LEN) == DICT_FIELD_NAME_PREFIX)
		field = new FormField(oForm, obj);
		if(field.isDictField())
		{
			// yongbin 090210, 进行文档篇幅的规范性检查（多长或过短）
			var fieldType;
			try {
				fieldType = oForm.elements["_FORM_AP_" + field.FieldName()].value || "";
			}catch(e) {
				fieldType = "";
			}
			// 找到正文部分
			if (fieldType == "Article.Content" && needCheckSplit(oForm)) {
				// 检测分页是否符合规范
				var content = field.Value();
				try {
					var ed = tinyMCE.get("_FORM_PF_" + field.FieldName());
				}catch(e){
					var ed = null;
				}
				if (!checkPageSplit(content, 0, false, ed)) {
					return 2;
				}
			}
			field_cn_name = field.FieldCName();

            if (ifUseKeyword && obj.type!="HIDDEN" && field.VerifyType() == "Text" )
			{
				var temp = field.Value();
				if (temp == "" || typeof(temp)=="undefined" || temp==null)
				{
					continue;
				}
				totalString += temp;
				
				elementsName[size] = field.FieldCName();
				objList[size] = obj;
				if (size == 0)
				{
					elementsAdd[size]= temp.length-1; 
				}
				else
				{
					elementsAdd[size]= elementsAdd[size-1] + temp.length; 
				}
				size++;
			}
			
            if(!field.VerifyMustNotNULL())
            {
                continue;
            }

			if(!field.PassNotNULLVerify())
			{
				alert(field_cn_name + "不能为空!");
				field.Focus();
				return 2;
			}

			if(!field.PassTypeVerify())
			{
				field_vtype = field.VerifyType();
				if(field_vtype == "Int")
				{
					alert(field_cn_name + "必须为整数!");
				}
				else if(field_vtype == "UInt")
				{
					alert(field_cn_name + "必须为正整数!");
				}
				else if(field_vtype == "Double")
				{
					alert(field_cn_name + "必须为浮点数!");
				}
				else if(field_vtype == "UDouble")
				{
					alert(field_cn_name + "必须为正浮点数!");
				}
				else if(field_vtype == "Date")
				{
					alert(field_cn_name + "请输入有效的日期格式，格式为：年-月-日，例如：2003-03-12！");
				}
				else if(field_vtype == "Time")
				{
					alert(field_cn_name + "请输入有效的时间格式，格式为：时:分:秒，例如：12:23:12！");
				}
				else if(field_vtype == "DateTime")
				{
					alert(field_cn_name + "请输入有效的日期和时间格式，格式为：年-月-日 小时:分钟:秒，例如：2003-03-12 12:30:00！");
				}
				else
				{
					alert(field_cn_name + "类型不匹配!");
				}
				field.Focus();
				return 2;
			}
			if(!field.PassLengthVerify())
			{
				alert(field_cn_name + "长度超限[" + field.VerifyMinLen() +  "," +field.VerifyMaxLen()  + "]!");
				field.Focus();
				return 2;
			}
		}
	}
	
	if (ifUseKeyword)
	{
		elementsAdd.length = size;
		elementsName.length = size;
		objList.length = size;
		return AlertKeywords(elementsAdd, elementsName, objList, totalString);
	}		
	return 0;
}

//-----------------------------------------------------------------------------------------------
// 执行表单自动校验，如果校验通过则提交表单
// 参数：
//		oForm:表单Form对象
//		oSender:当前触发事件的对象
//-----------------------------------------------------------------------------------------------

function On_FormVerifySubmitClick(oForm, oSender, ifUseKeyword)
{
	if(PassFormVerify(oForm, oSender, ifUseKeyword)==0)
	{
		oForm.submit();
	}
}


//-----------------------------------------------------------------------------------------------
// 执行文档表单自动校验，如果校验通过则提交表单
// 参数：
//		oForm:表单Form对象
//		oSender:当前触发事件的对象
//-----------------------------------------------------------------------------------------------

function On_DocumentFormVerifySubmitClick(oForm, oSender)
{
	//警告关键字校验处理
	/*if (AlertKeywords(oForm))
	{
		return false;
	}*/

	//表单校验处理
	On_FormVerifySubmitClick(oForm, oSender, false);
}


//----------------------------------------------------------------------
// 判断指定的串是否为日期格式(YYYY-MM-DD)
//----------------------------------------------------------------------
function checkDateField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isDate(value))
	{
		window.alert("请输入有效的日期，日期格式为：年-月-日，例如：2003-03-12");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}

//----------------------------------------------------------------------
// 判断指定的串是否为时间格式(HH:MM:SS)
//----------------------------------------------------------------------
function checkTimeField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isTime(value))
	{
		window.alert("请输入有效的时间，时间格式为：小时:分钟:秒，例如：12:30:00");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}

//----------------------------------------------------------------------
// 判断指定的串是否为日期时间格式(YYYY-MM-DD HH:MM:SS)
//----------------------------------------------------------------------
function checkDateTimeField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isDateTime(value))
	{
		window.alert("请输入有效的日期和时间，格式为：年-月-日 小时:分钟:秒，例如：2003-03-12 12:30:00");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}

//----------------------------------------------------------------------
// 判断指定的串是否为整数
//----------------------------------------------------------------------
function checkIntField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isInt(value))
	{
		window.alert("请输入整数！");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}


//----------------------------------------------------------------------
// 判断指定的串是否为正整数
//----------------------------------------------------------------------
function checkUIntField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isUInt(value))
	{
		window.alert("请输入非负整数！");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}

//----------------------------------------------------------------------
// 判断指定的串是否为浮点数
//----------------------------------------------------------------------
function checkDoubleField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isDouble(value))
	{
		window.alert("请输入数值！");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}


//----------------------------------------------------------------------
// 判断指定的串是否为正浮点数
//----------------------------------------------------------------------
function checkUDoubleField(obj)
{
	var value = obj.value;
	if (value.length > 0 && !isUDouble(value))
	{
		window.alert("请输入非负数值！");
		obj.focus();
		return false;
	}
	else
	{
		return true;
	}
}

//-------------------------------------------------------------
// 编辑或添加表单处理时回车键的事件处理
//-------------------------------------------------------------
function On_KeyPress(eve,oForm, oSender)
{
	var field;
	var e=window.event?window.event:eve;
	if(oSender.type != "textarea")
	{
		if (e.keyCode == 13)
		{
			field = new FormField(oForm, oSender);
			field = field.Next();
			if(field != null)
			{
				if(!field.isHidden())
				{
					field.Focus();
				}
			}
		}
	}
}

//-------------------------------------------------------------
// 清空当前表单
//-------------------------------------------------------------
function On_FormClearClick(oForm, oSender)
{
	var field = new FormField(oForm);
	while((field = field.Next()) != null)
	{
		if(field.isHidden())
		{
			continue;
		}
		field.setFieldValue(field.FieldDefaultValue());
	}
}


//-------------------------------------------------------------
// 表单加载事件处理
//-------------------------------------------------------------
function On_FormLoad(oForm)
{
	FocusToFirstFormField(oForm);
}

//-------------------------------------------------------------
// 编辑或添加表单加载时间焦点集中到第一个可见的表单元素
//-------------------------------------------------------------
function FocusToFirstFormField(oForm)
{
	var field = new FormField(oForm);
	while((field = field.Next()) != null)
	{
		if(field.isHidden())
		{
			continue;
		}
		field.Focus();
		break;
	}
}

//-------------------------------------------------------------
// 告警关键字提示
// 参数：
//		elementsAdd:记录Form各对象在合并字符串的起始位置
//		elementsName:记录所有需要处理的对象中文名称
//		totalString:所有需要处理对象的字符串合集
// 返回值:
//		true:出现关键字
//		false:没有关键字		
//------------------------------------------------------------
function AlertKeywords(elementsAdd, elementsName, objList, totalString)
{
	var keywordList = getKeywordAlert();
	var keylevelList = getKeywordLevel();
	var size;
	var i;
	var judgeAction = 0;
	if (keywordList.length == 0)
	{
		return false;
	}
	
	size = elementsAdd.length;
	
	var resultString = new Array(size);
	for (i=0; i<size; i++)
	{
		resultString[i]="";
	}
	for (i=0; i<keywordList.length; i++)
	{
		var index=0;
		var startIndex=0;
		var target = keywordList[i];
		var targetlevel = keylevelList[i];
		var position;
		if (size==1)
		{
			position = elementsAdd[0];	
		}
		else
		{
			position = elementsAdd[size-2];
		}			
		while (index < position)
		{
			index = totalString.indexOf(target, startIndex);
			if (index <0)
			{
				break;
			}
			
			for (var k=0; k<size; k++)
			{
				if (index<elementsAdd[k] && targetlevel==2)
				{
					objList[k].focus();
					alert(elementsName[k] + " 出现重要关键字: "+ target + " 禁止发布！");
					return 2;
				}
				else if (index<elementsAdd[k] && targetlevel==1)
				{
					judgeAction = 1;	
				}

				if (index<elementsAdd[k])
				{
					resultString[k] += target + " ";
					startIndex = elementsAdd[k];
					break;
				}
			}
		}
	}

	var totalAlertString=""; //记录页面所有出现过的关键字
	for (i=0; i<size; i++)
	{
		if (resultString[i] != "")
		{
			alertString = elementsName[i] + " 出现 : " + resultString[i];
			objList[i].focus();
			if (confirm(alertString+",是否继续？"))//出现关键字，继续执行
			{
				var arrayResult = resultString[i].split(" "); //将每个表单元素出现的关键字字符串，按" "拆分开
				for (var j=0; j<arrayResult.length; j++)
				{
					index = totalAlertString.indexOf(arrayResult[j]);//判断是否已经加入该关键字
					if (index < 0)
					{
						totalAlertString += " '" + arrayResult[j] + "'  ";//加入关键字
					}
				}
				continue;
			}
			else//出现关键字，取消执行
			{
				return 2;
			}
		}
	}
	if (totalAlertString != "")//检查完所有表单元素，将出现的关键字列出，判断是否继续
	{
		if (confirm("本页面出现了以下关键字: " + totalAlertString + ", 是否继续？"))
		{
			return judgeAction;
		}
		else
		{
			return 2;
		}
	}
	return judgeAction;
}

//---------------
//去掉字符串中的'\n'
//---------------
function cutn(string)
{
	if( string == null || typeof(string) == "undefined")
	{
		return "";
	}
	var text="";
	var i;
	for (i=0; i<string.length-1; i++)
	{
		c1=string.charAt(i);	
		c2=string.charAt(i+1);
		if (c1 == '\x0d' && c2 == '\x0a')
		{
			i++;
		}
		else
		{
			text += c1;
		}
	}
	if (string.charAt(i) != '\x0a')
	{
		text += string.charAt(i);
	}
	return text;	
}

function check_is_valid_ip4(sIPAddress)
{
    var exp=/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
    var reg = sIPAddress.match(exp);
   
    if(reg==null)
    {
        return false;
    }
    else
    {
        return true;
    }
}



//浮动任务栏
function float_toolBar(id)
{
var windowGeometry = {};
if (window.innerWidth) { // All browsers but IE
    windowGeometry.getViewportWidth = function( ) { return window.innerWidth; };
    windowGeometry.getViewportHeight = function( ) { return window.innerHeight; };
    windowGeometry.getHorizontalScroll = function( ) { return window.pageXOffset; };
    windowGeometry.getVerticalScroll = function( ) { return window.pageYOffset; };
}
else if (document.documentElement && document.documentElement.clientWidth) {
    // These functions are for IE 6 when there is a DOCTYPE
    windowGeometry.getViewportWidth =
        function( ) { return document.documentElement.clientWidth; };
    windowGeometry.getViewportHeight =
        function( ) { return document.documentElement.clientHeight; };
    windowGeometry.getHorizontalScroll =
        function( ) { return document.documentElement.scrollLeft; };
    windowGeometry.getVerticalScroll =
        function( ) { return document.documentElement.scrollTop; };
}
else if (document.body.clientWidth) {
    // These are for IE4, IE5, and IE6 without a DOCTYPE
    windowGeometry.getViewportWidth =
        function( ) { return document.body.clientWidth; };
    windowGeometry.getViewportHeight =
        function( ) { return document.body.clientHeight; };
    windowGeometry.getHorizontalScroll =
        function( ) { return document.body.scrollLeft; };
    windowGeometry.getVerticalScroll =
        function( ) { return document.body.scrollTop; };
}

   var floatbar = document.getElementById(id);
   if(floatbar)
   {
	floatbar.style.top = 
		parseInt(windowGeometry.getViewportHeight() + windowGeometry.getVerticalScroll() - floatbar.offsetHeight - 35) + "px"; 
	floatbar.style.left = 
		parseInt(windowGeometry.getViewportWidth() + windowGeometry.getHorizontalScroll() - floatbar.offsetWidth -55) + "px";
  	setTimeout(function(){float_toolBar(id);},100)
   }
}

//ajax request

    function sina_cms_ajax(_para, _cgi, _method, _callback)
    {
		function ajax_xmlHttp(){//为ajax建立xmlhttp对象
			var http_request  =  false;

			if(window.XMLHttpRequest) {
				http_request  =  new XMLHttpRequest();
				if (http_request.overrideMimeType) {
					http_request.overrideMimeType('text/xml');
				}
			}
			else if (window.ActiveXObject) {
			try {
				http_request  =  new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
			try {
				http_request  =  new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
			}
			}
			return http_request;
		}

		function encodeParas(_para_obj)
		{
			
			if (typeof _para_obj == "object")
			{
				var _tmp_str_ = "";
				for(var key in _para_obj)
				{
					_tmp_str_ += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(_para_obj[key]);
				}
				return _tmp_str_.length > 0?_tmp_str_.substr(1):"";

			}
			else if(typeof _para_obj == "string")
			{
				return _para_obj;
			}
			return _para_obj_.toString();
		}

        var xml_Http = ajax_xmlHttp();
        if (!xml_Http)
        {
            alert("建立ajax失败！");
            return 0;
        }

        xml_Http.onreadystatechange = function(){
            if (xml_Http.readyState  ==  4 && xml_Http.status  ==  200){
				if(_callback)
				{
					_callback(xml_Http.responseText);
				}
            }
        }
		if(_method == undefined) _method = "GET";

		if(_cgi == undefined)
		{
			alert("No cgi specified!");
			return false;
		}

		if(_para == undefined)
		{
			_para = "0";
		}

        xml_Http.open(_method, _cgi,true);
        xml_Http.send(encodeParas(_para));
    }

	//该函数用户获得包含汉字的字符长度，每个汉字是2个字符长度
    String.prototype.cn_len=function()
    {
          return this.replace(/[^\x00-\xff]/g,"hy").length;
    }

	//该函数用于截取字符串左侧长度的子字符串，每个汉字是2个字符
    String.prototype.Left = function(n, notshowDot)
    {
         var r = /[^\x00-\xff]/g;
         if(this.replace(r, "mm").length <= n) return this;
        // n = n - 3;
         var m = Math.floor(n/2);
         for(var i=m; i<this.length; i++)
         {
			 if(this.substr(0, i).replace(r, "mm").length>=n)
			 {
			 return this.substr(0, i) + "..";
			 }
         }
         return this;
  }

function onload_load(func)
{
	if (window.addEventListener)
		window.addEventListener("load", func, false);
	else if (window.attachEvent) window.attachEvent("onload", func);
	else window.onload = func();
}

function exitGSPS()
{
	if(confirm('您要退出发布系统吗？')){window.open('quitgsps.cgi','_self',''); }
}

function $hy$(id)
{
	if (typeof id == "object") return id;
	var target = document.getElementById(id);
	if(target) return target;

	target = document.getElementsByName(id);
	if(target.length > 0) return target[0];
	
	return null;
}

function getElementPosition(e)
{
	function getTop(e){
		var top = e.offsetTop;
		if(e.offsetParent != null) 
			{
		e = e.offsetParent;
			top += e.offsetTop;
			}
		return top;
	}
	function getLeft(e){
		var left = e.offsetLeft;
		if(e.offsetParent!=null) left += getLeft(e.offsetParent);
		return left;
	}

	return {top:getTop(e), left:getLeft(e)};
}
