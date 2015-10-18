
//==============================================================================================
// Common JavaScript Function
//==============================================================================================


//-------------------------------------------------------------
//	选择图片上传时如果图片超出规范，提醒用户
//	SINA图片规范请参考:http://221.pub.sina.com.cn:8080/sina/standard/photo_size/index.html
//	计算公式: 
//	--------------------------------------------------------------------------------
//  旧规范：
//	K=1.80 * (l * s/5800) (当l≥300) 许可误差9.0k
//	K=1.65 * (l * s/3200) (当200≤l<300) 许可误差4.0k
//	K=1.50 * (l * s/2000) (当100≤l<200) 许可误差2.0k
//	K=1.40 * (l * s/1800) (当50≤l<100) 许可误差1.0k
//	K=1.25 * (l * s/1300) (当l<50) 许可误差0.5k
//
//  新规范：	
//	K=(l*s/5800)*2.1 (当l≥300) 许可误差9.0k 
//	K=(l*s/3200)*1.9 (当200≤l<300) 许可误差4.0k 
//	K=(l*s/2000)*1.8 (当100≤l<200) 许可误差2.0k 
//	K=(l*s/1800)*1.7 (当50≤l<100) 许可误差1.0k 
//	K=(l*s/1300)*1.5 (当l<50) 许可误差0.5k


//  2008.2.18 规范
// 	K=(l*s/5800)*2.21 (当l≥300) 许可误差9.0k
//	K=(l*s/3200)*2.09 (当200≤l<300) 许可误差4.0k
//	K=(l*s/2000)*1.98 (当100≤l<200) 许可误差2.0k
//	K=(l*s/1800)*1.87 (当50≤l<100) 许可误差1.0k
//	K=(l*s/1300)*1.65 (当l<50) 许可误差0.5k

//	注：l=图片长边
//		s=图片短边
//		K=图片大小（k）
//-------------------------------------------------------------

/*
function getEscapeValue(val)
{  
	val=escape(val);  
	re = /%/gi;
	val=val.replace(re, "\\x");  
	val =eval ("val=" + "\"" + val +"\"");  
	return val;
}
*/
function getEscapeValue(val)
{
        val=escape(val);
        re = /%/gi;
        val=val.replace(re, "\\");
        val =eval ("\"" + val +"\"");
        return val;
}

function CheckImageSize(oForm, oSender)
{
	// 先判断当前的图片是否绝对链接
	var re = new RegExp(/^[a-zA-Z]+:\/\//i);
	var file = oSender.value;
	var img = null;
	
	if(!file.match(re)){
		img = document.createElement("img");
		img.style.position = "absolute";
		img.style.visibility = "hidden";
/*		img.attachEvent("onreadystatechange", ShowImageInfo);
		document.body.insertAdjacentElement("beforeend",img);
*/
		if(img.attachEvent){
			img.attachEvent("onreadystatechange", function(){
				if (img.readyState == "complete"){
					ShowImageInfo();
					document.body.removeChild(img);
				}
			});
			img.onerror = function(){
				var n = oSender.name.replace("_FORM_PF", "");
				var isErr = false;
				try {
					if (/Image/.test(oForm["_FORM_AP" + n].value)) isErr = true;
				}catch(e){
					return;
				}
				if (isErr) {
					alert("错误图片！");
					document.body.removeChild(img);
					
					// 清空value
					var tmpForm = document.createElement('form');
					document.body.appendChild(tmpForm);
					var pos = oSender.nextSibling;
					tmpForm.appendChild(oSender);
					tmpForm.reset();
					pos.parentNode.insertBefore(oSender, pos);
					document.body.removeChild(tmpForm);
				}
			}
			//img.src = file;
			document.body.insertAdjacentElement("beforeend",img);
		}
		else if(img.addEventListener){
	  
			img.addEventListener("readystatechange", ShowImageInfo, false);
			//img.src = 'file://localhost/'+file;
			img.onload=ShowImageInfo;
			document.body.insertBefore(img,document.body.nextSibling); 
		}
		img.src = 'file://localhost/'+ file;
	}

	function ShowImageInfo()
	{
		try {
			var fileSize = parseInt(img.fileSize) / 1024.0;
		}catch(e){
		}
		fileSize = fileSize.toFixed(1);
		var l;
		var s;
		var width = img.width;
		var height = img.height;
		var calFileSize;
		var valid = 1;
		if(fileSize > 0)
		{
			if(width > height){
				l = width;
				s = height;
			}
			else{
				l = height;
				s = width;
			}
			if(l < 50){
				calFileSize = 1.65 * (l * s/1300);
				if(fileSize - calFileSize > 0.5){
					valid = 0;
					calFileSize = calFileSize + 0.5;
				}
			}
			else if(l < 100){
				calFileSize = 1.87 * (l * s/1800);
				if(fileSize - calFileSize > 1.0){
					valid = 0;
					calFileSize = calFileSize + 1.0;
				}
			}
			else if(l < 200){
				calFileSize = 1.98 * (l * s/2000);
				if(fileSize - calFileSize > 2.0){
					valid = 0;
					calFileSize = calFileSize + 2.0;
				}
			}
			else if(l < 300){
				calFileSize = 2.09 * (l * s/3200);
				if(fileSize - calFileSize > 4.0){
					valid = 0;
					calFileSize = calFileSize + 4.0;
				}
			}
			else{
				calFileSize = 2.21 * (l * s/5800);
				if(fileSize - calFileSize > 9.0){
					valid = 0;
					calFileSize = calFileSize + 9.0;
				}
			}
			calFileSize = calFileSize.toFixed(1);
			if(valid == 0){
				alert("警告!禁止发布超标图片，您发布的图片将为空!!");

				var tmpForm = document.createElement('form');
				document.body.appendChild(tmpForm);
				var pos = oSender.nextSibling;
				tmpForm.appendChild(oSender);
				tmpForm.reset();
				pos.parentNode.insertBefore(oSender, pos);
				document.body.removeChild(tmpForm);
			}
		}
		return valid;
	}
}

function DisableAllRedundantElements(oForm)
{
	/*
	var length = oForm.elements.length;
	var field;
	var obj;
	var FIELD_NAME;
	var VF_NOTNULL_ELE_NAME;
	var VF_NOTNULL_ELE_OBJ;
	var VF_MIN_LENGTH_ELE_NAME;
	var VF_MIN_LENGTH_ELE_OBJ;
	var VF_MAX_LENGTH_ELE_NAME;
	var VF_MAX_LENGTH_ELE_OBJ;
	var VF_FTR_ELE_NAME;
	var VF_FTR_ELE_OBJ;
	var FCR_ELE_NAME;
	var FCR_ELE_OBJ;
	var FDV_ELE_NAME;
	var FDV_ELE_OBJ;
	for (var i=0; i<length; i++)
	{
		obj = oForm.elements[i];
		field = new FormField(oForm, obj);
		if (!field.isDictField())
		{
			continue;
		}
		FIELD_NAME = field.FieldName();
		VF_NOTNULL_ELE_NAME = DICT_VERIFY_NOT_NULL_PREFIX + FIELD_NAME;
		VF_NOTNULL_ELE_OBJ = oForm[VF_NOTNULL_ELE_NAME);

		VF_MIN_LENGTH_ELE_NAME = DICT_VERIFY_MIN_LEN_PREFIX + FIELD_NAME;
		VF_MIN_LENGTH_ELE_OBJ = oForm[VF_MIN_LENGTH_ELE_NAME);

		VF_MAX_LENGTH_ELE_NAME = DICT_VERIFY_MAX_LEN_PREFIX + FIELD_NAME;
		VF_MAX_LENGTH_ELE_OBJ = oForm[VF_MAX_LENGTH_ELE_NAME);

		VF_FTR_ELE_NAME = DICT_VERIFY_TYPE_PREFIX + FIELD_NAME;
		VF_FTR_ELE_OBJ = oForm[VF_FTR_ELE_NAME);

		FCR_ELE_NAME = DICT_FIELD_CNAME_PREFIX + FIELD_NAME;
		FCR_ELE_OBJ = oForm[FCR_ELE_NAME);

		FDV_ELE_NAME = DICT_FIELD_DEFAULT_VALUE_PREFIX + FIELD_NAME;
		FDV_ELE_OBJ = oForm[FDV_ELE_NAME);

		alert(FIELD_NAME);	

		alert(VF_NOTNULL_ELE_NAME);
		alert(VF_NOTNULL_ELE_OBJ);

		alert(VF_MIN_LENGTH_ELE_NAME);
		alert(VF_MIN_LENGTH_ELE_OBJ);

		alert(VF_MAX_LENGTH_ELE_NAME);
		alert(VF_MAX_LENGTH_ELE_OBJ);

		alert(VF_FTR_ELE_NAME);
		alert(VF_FTR_ELE_OBJ);

		alert(FCR_ELE_NAME);
		alert(FCR_ELE_OBJ);

		alert(FDV_ELE_NAME);
		alert(FDV_ELE_OBJ);
		break;
	}
	*/
}

//==============================================================================================
// 文档列表表单(document_list.cgi) JavaScript Function
//==============================================================================================


function On_DocumentListForm_PlusQueryClick(oForm, oSender)
{
	var iTotalSearchCount = oForm["_search_field_total_count"].value;
	var iStartIdx = parseInt(oSender.value);
	var oEntryObj;
	var search_field;
	var search_method;
	var search_value;
	var search_concat;
	var plus_query;
	if(oSender.checked)
	{
			oForm["_search_field_count"].value = iStartIdx;
			oEntryObj = "ID_QUERY_UNIT_" + iStartIdx;
			document.getElementById(oEntryObj).style.display = "inline";
			search_field = "_search_field_" + iStartIdx;
			search_method = "_search_method_" + iStartIdx;
			search_value = "_search_value_" + iStartIdx;
			search_concat = "_search_concat_" + iStartIdx;
			oForm[search_field].disabled = false;
			oForm[search_method].disabled = false;
			oForm[search_method].disabled = false;
			oForm[search_concat].disabled = false;
	}
	else
	{
		oForm["_search_field_count"].value = iStartIdx - 1;
		for(var i = iStartIdx; i <= iTotalSearchCount; i++)
		{
			oEntryObj = "ID_QUERY_UNIT_" + i;
		        document.getElementById(oEntryObj).style.display = "none";
			search_field = "_search_field_" + i;
			search_method = "_search_method_" + i;
			search_value = "_search_value_" + i;
			search_concat = "_search_concat_" + i;
			oForm[search_field].disabled = true;
			oForm[search_method].disabled = true;
			oForm[search_method].disabled = true;
			oForm[search_concat].disabled = true;
		}
		for(var i = iStartIdx; i < iTotalSearchCount; i++)
		{
			plus_query = "_plus_query_" + i;
			oForm[plus_query].checked = false;
		}
	}
}

//-------------------------------------------------------------
//文档全选
//-------------------------------------------------------------
function On_DocumentListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

//-------------------------------------------------------------
//	选择文档修改时调用
//-------------------------------------------------------------
function On_DocumentListForm_EditClick(oForm, oSender)
{
	var p_id = oForm["p_id"].value;
	var t_id = oForm["t_id"].value;
	var oCheckboxColl = oForm["d_id"];
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要编辑的文档!");
		return;
	}
	if(j > 1)
	{
		alert("一次只能修改一条文档!");
		return;
	}
	var cgi_url =  "document_edit.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	window.self.open(cgi_url, "_self");
}


//-------------------------------------------------------------
//	选择文档新建复制时调用
//-------------------------------------------------------------
function On_DocumentListForm_AddCopyClick(oForm, oSender)
{
	var p_id = oForm["p_id"].value;
	var t_id = oForm["t_id"].value;
	var oCheckboxColl = oForm["d_id"];
	var j = 0;
	if (oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if (len != null)
		{
			for (var i = 0; i < len; i++)
			{
				if (oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if (oCheckboxColl.type == "hidden" || oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if (j == 0)
	{
		alert("请选择需要复制的文档!");
		return;
	}
	if (j > 1)
	{
		alert("一次只能复制一个文档!");
		return;
	}
	var cgi_url =  "document_add_copy.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	var oCheckboxSel = oForm["_sel_field"];
	if (oCheckboxSel != null && oCheckboxSel.checked)
	{
		cgi_url += "&sel_field=yes";
	}	
	window.self.open(cgi_url, "_self");
}


//-------------------------------------------------------------
//	选择文档拒签时调用
//	杨明辉2004-06-09，用途：拒签文档时需登记原因
//-------------------------------------------------------------
function On_DocumentListForm_RejectClick(oForm, oSender)
{
	//取消拒签
	if(oSender.name == "_rej_cancel")
	{
		document.getElementById("ID_REJECT_REASON").style.display = "none";
		oForm["_rej_reason"].disabled = true;
		return;
	}
			
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要拒签的文档!");
		return;
	}
	
	//拒签确认
	if(oSender.name == "_rej_confirm")
	{
		if(oForm["_rej_reason"].value == "")
		{
			alert("请填写拒签原因!");
			oForm["_rej_reason"].focus();
			return;
		}
		oForm.action = "document_publish.cgi";
		oForm["_action"].value = "reject";
		oForm.submit();
		return;
	}
	
	//填写拒签原因
	document.getElementById("ID_REJECT_REASON").style.display = "inline";
	oForm["_rej_reason"].disabled = false;
}


//-------------------------------------------------------------
//	选择文档删除时调用
//	杨明辉2004-2-24修改，用途：删除文档时需登记原因
//	原函数备份为‘On_DocumentListForm_DeleteClick_BAK’
//-------------------------------------------------------------
function On_DocumentListForm_DeleteClick(oForm, oSender)
{
	//取消删除
	if(oSender.name == "_del_cancel")
	{
		document.getElementById("ID_DELETE_REASON").style.display = "none";
		oForm["_del_reason"].disabled = true;
		return;
	}
			
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	
	//删除确认
	if(oSender.name == "_del_confirm")
	{
		if(oForm["_del_reason"].value == "")
		{
			alert("请填写删除原因!");
			oForm["_del_reason"].focus();
			return;
		}
		//-------------------------------------------------------------------
		//删除文档时不发布
		//-------------------------------------------------------------------
		if(! oForm["_del_not_publish"].checked)
		{
			//---------------------------------------------------------------
			//采用标记删除方式
			//---------------------------------------------------------------
			//if(oForm["_marked_delete_mode"].checked)
			//{
				oForm["_action"].value = "marked_delete_publish";
			//}
			//else
			//{
			//	oForm["_action"].value = "delete_publish";
			//}
			oForm.action = "document_publish.cgi";
		}
		else
		{
			oForm["_action"].value = "delete";
		}
		oForm.submit();
		return;
	}
	
	//填写删除原因
	document.getElementById("ID_DELETE_REASON").style.display = "inline";
	oForm["_del_reason"].disabled = false;
	oForm["_del_terminal"].disabled = false;
}

//-------------------------------------------------------------
//	选择文档删除时调用BAK
//-------------------------------------------------------------
function On_DocumentListForm_DeleteClick_BAK(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	if(prompt("请确定是否删除！(yes/no)", "no") == "yes")
	{

		//-------------------------------------------------------------------
		//删除文档时不发布
		//-------------------------------------------------------------------
		if(! oForm["_del_not_publish"].checked)
		{
			//---------------------------------------------------------------
			//采用标记删除方式
			//---------------------------------------------------------------
			if(oForm["_marked_delete_mode"].checked)
			{
				oForm["_action"].value = "marked_delete_publish";
			}
			else
			{
				oForm["_action"].value = "delete_publish";
			}
			oForm.action = "document_publish.cgi";
		}
		else
		{
			oForm["_action"].value = "delete";
		}
		oForm.submit();
	}
}



//-------------------------------------------------------------
//	回收删除的文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_ReclaimClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要回收的文档!");
		return;
	}
	oForm["_action"].value = "reclaim";
	oForm.submit();
}




//-------------------------------------------------------------
//	同步文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_SyncClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要同步的文档!");
		return;
	}
	oForm.action = "document_sync.cgi";
	oForm.submit();
}

//-------------------------------------------------------------
//	预览文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_PreviewClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要预览的文档!");
		return;
	}
	oForm["_action"].value = "publish";
	oForm.action = "document_preview.cgi";
	oForm.submit();
}


//-------------------------------------------------------------
//	选择文档发布时调用
//-------------------------------------------------------------
function On_DocumentListForm_PublishClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要发布的文档!");
		return;
	}
	oForm["_action"].value = "publish";
	oForm.action = "document_publish.cgi";
	oForm.submit();
}

//-------------------------------------------------------------
//	选择文档批量修改时调用
//-------------------------------------------------------------
function On_DocumentListForm_BatchUpdateClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要批量修改的文档！");
		return;
	}
	oForm.action = "document_batch_update.cgi";
	//oForm.target = "_blank";
	oForm.submit();
}

//-------------------------------------------------------------
//	出来文档跨项目发往新闻中心button
//-------------------------------------------------------------
function On_DocumentListForm_CrossClick(oForm, oSender)
{
	var oCheckboxColl = oForm["d_id"];
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要发布的文档!");
		return;
	}
	oForm["_action"].value = "list";
	oForm.action = "jczs2dailynews.cgi";
	//oForm.target = "_blank";
	oForm.submit();
}

//----------------------------------------
//响应"文档列表"Button
//----------------------------------------
function checkDocumentListForm_SearchSubmit(eve,form, obj)
{
	var e=window.event?window.event:eve;
	if(e.keyCode == 13)
	{
		obj.click();
	}
}

//-------------------------------------------------------------
//开始检索
//-------------------------------------------------------------
function On_DocumentListForm_SearchClick(oForm, oSender)
{
	oForm["_search_type"].value = "publish_db";
	if (oForm["_search_source"] != null)
	{
		oForm["_search_source"].value = "";
	}
	return On_DocumentListForm_FirstPageClick(oForm, oSender);
}

//-------------------------------------------------------------
//开始快速检索
//-------------------------------------------------------------
function On_DocumentListForm_quickSearchClick(oForm, oSender)
{
	oForm["_search_type"].value = "search_engine";
	if (oForm["_search_source"] != null)
	{
		oForm["_search_source"].value = "publish_db";
	}
	return On_DocumentListForm_FirstPageClick(oForm, oSender);
}

function checkPageSize(oForm)
{
	if (oForm["_search_type"] == null || oForm["_search_type"].value != "search_engine")
	{
		return true;
	}
	if (oForm["use_lucene"] != null && oForm["use_lucene"].checked)
	{
		var page_size = parseInt(oForm["_page_size"].value);
		if (isNaN(page_size) || page_size <= 0 || page_size > 90)
		{
			alert("使用新搜索，每页最高显示90条!");
			return false;
		}
	}
	return true;
}

function checkSearchKeySize(oForm)
{
	if (oForm["_search_key"] == null)
	{
		return false;
	}	
	var search_key = oForm["_search_key"].value;
	search_key = search_key.replace(/(^\s*)|(\s*$)/g, "");
	search_key = search_key.replace(/\s+/g, " ");
	if (search_key == null || search_key == "")
	{
		alert("请输入检索关键字!");
		return false;
	}
	
	if (oForm["use_lucene"] == null || !oForm.use_lucene.checked)
	{
		return true;
	}
	if (oForm["_search_class"] == null
	 || oForm["_search_class"].value == "media"
	 || oForm["_search_class"].value == "category"
	 || oForm["_search_class"].value == "subject"
	 || oForm["_search_class"].value == "creator"
	 || oForm["_search_class"].value == "mender")
	{
		return true;
	}

	var key_list = new Array();
	key_list = search_key.split(" ");
	if (key_list.length == 1)
	{
		if (search_key.length > 16)
		{
			alert("检索字符过多!请重新输入!");
			return false;
		}
	}
	else if (key_list.length > 1)
	{
		if (key_list.length > 6)
		{
			alert("检索字符过多!请重新输入!");
			return false;
		}
		for (i=0; i<key_list.length; i++)
		{
			if (key_list[i].length > 8)
			{
				alert("检索字符过多!请重新输入!");
				return false;
			}
		}
	}
	return true;
}

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_DocumentListForm_FirstPageClick(oForm, oSender)
{
	if (oForm["_search_type"] != null && oForm["_search_type"].value == "search_engine")
	{
		if (!checkSearchKeySize(oForm))
		{
			return false;
		}
		if (!checkPageSize(oForm))
		{
			return false;
		}
	}
	oForm["_goto_page"].value = "0";
	if (oForm["_search_source"] != null)
	{
		if (oForm["_search_type"].value == "search_engine")
		{
			oForm["_search_source"].value = "publish_db";
		}
		else
		{
			oForm["_search_source"].value = "";
		}
	}	
	oForm.submit();
}


//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_DocumentListForm_Init(oForm, oSender)
{
	var cur_page = parseInt(oForm["_goto_page"].value);	
	var def_page_size = parseInt(oForm["_page_size"].value);
	var cur_page_size = parseInt(oForm["_cur_page_size"].value);
	
	if (oForm["_search_source"] != null)
	{
		var search_source = oForm["_search_source"].value;
		var page_flag = parseInt(oForm["_page_flag"].value);
		if(cur_page == 0 && search_source == "publish_db")
		{
			oForm["goto_first_page"].disabled = true;
			oForm["goto_prev_page"].disabled = true;
		}
		if (page_flag == 0)
		{
			oForm["goto_first_page"].disabled = true;
			oForm["goto_prev_page"].disabled = true;
		}
		if(cur_page_size < def_page_size && search_source == "search_engine")
		{
			oForm["goto_next_page"].disabled = true;
		}
	}
	else
	{
		if(cur_page == 0)
		{
			oForm["goto_first_page"].disabled = true;
			oForm["goto_prev_page"].disabled = true;
		}
		if(cur_page_size < def_page_size)
		{
			oForm["goto_next_page"].disabled = true;
		}
	}
}


//-------------------------------------------------------------
//导航记录到上一页
//-------------------------------------------------------------
function On_DocumentListForm_PrevPageClick(oForm, oSender)
{
	if (oForm["_search_type"] != null && oForm["_search_type"].value == "search_engine")
	{
		if (!checkSearchKeySize(oForm))
		{
			return false;
		}
		if (!checkPageSize(oForm))
		{
			return false;
		}
	}
	var cur_page = parseInt(oForm["_goto_page"].value);
	var page_size = parseInt(oForm["_page_size"].value);
	if(isNaN(cur_page))
	{
		cur_page = 0;
	}	
	if(isNaN(page_size))
	{
		page_size = 30;
	}	
	var prev_page = cur_page - page_size;
	if(prev_page < 0)
	{
		prev_page = 0;
	}
	
	if (oForm["_search_source"] != null)
	{	
		var search_source = oForm["_search_source"].value;
		if (search_source == "publish_db")
		{
			oForm["_search_source"].value = "publish_db";
		}
		else
		{
			if (cur_page == 0)
			{
				oForm["_search_source"].value = "publish_db";
				if (oForm["_db_last_page"] != null)
				{
					prev_page = oForm["_db_last_page"].value;
				}
			}
			else
			{
				oForm["_search_source"].value = "search_engine";
			}
		}
	}
	
	oForm["_goto_page"].value = prev_page;	
	oForm.submit();
}

//-------------------------------------------------------------
//导航记录到下一页
//-------------------------------------------------------------
function On_DocumentListForm_NextPageClick(oForm, oSender)
{
	if (oForm["_search_type"] != null && oForm["_search_type"].value == "search_engine")
	{
		if (!checkSearchKeySize(oForm))
		{
			return false;
		}
		if (!checkPageSize(oForm))
		{
			return false;
		}
	}
	var cur_page = parseInt(oForm["_goto_page"].value);
	var page_size = parseInt(oForm["_page_size"].value);	
	var cur_page_size = parseInt(oForm["_cur_page_size"].value);
	if(isNaN(cur_page))
	{
		cur_page = 0;
	}	
	if(isNaN(page_size))
	{
		page_size = 30;
	}	
	var next_page = cur_page + page_size;	
	
	if (oForm["_search_source"] != null)
	{
		var search_source = oForm["_search_source"].value;
		if (search_source == "publish_db")
		{
			if (cur_page_size < page_size)
			{
				oForm["_search_source"].value = "search_engine";
				next_page = 0;
			}
			else
			{
				oForm["_search_source"].value = "publish_db";
			}	
		}
		else
		{
			oForm["_search_source"].value = "search_engine";
		}
	}
	
	oForm["_goto_page"].value = next_page;	
	oForm.submit();
}

//跳转到任意页面
function On_DocumentListForm_Jump(oForm, oSender)
{
	if (!checkPageSize(oForm))
	{
		return false;
	}
	var search_source = oForm["_search_source"].value;
	if (search_source == "search_engine")
	{
		var jump_page = parseInt(oForm["_jump_page"].value);
		if(isNaN(jump_page) || jump_page <= 0)
			jump_page = 1;
		var page_size = parseInt(oForm["_page_size"].value);
		if(isNaN(page_size) || page_size <= 0)
			page_size = 30;
		jump_page = (jump_page-1)*page_size;
		oForm["_goto_page"].value = jump_page;
		oForm.submit();
	}
}

//==============================================================================================
// 文档创建表单(document_add.cgi) JavaScript Function
//==============================================================================================


//-------------------------------------------------------------
//添加表单时手工指定文档创建日期及时间时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_SpecCreateTimeClick(oForm, oSender)
{
	var oIfSpecialCreateTime = oForm["special_createtime"];
	if(oSender.checked)
	{
		var now  =  new  Date();  
		var year =  now.getFullYear();  
		var mon  =  now.getMonth()+1;
		var day  =  now.getDate();

		var  hh  =  now.getHours();  
		var  mm  =  now.getMinutes();  
		var  ss  =  now.getTime()  %  60000;  
		ss  =  (ss  -  (ss  %  1000))  /  1000;  
		var  clock  =  hh+':';  
		if  (mm  <  10)  clock  +=  '0';  
		clock  +=  mm+':';  
		if  (ss  <  10)  clock  +=  '0';  
		clock  +=  ss;  

		var cur_dt = year + '-' + mon + '-' + day + ' ' + clock;

		oIfSpecialCreateTime.style.backgroundColor = "";
		oIfSpecialCreateTime.value = cur_dt;
		oIfSpecialCreateTime.disabled = false;
		oIfSpecialCreateTime.focus();
	}
	else
	{
		oIfSpecialCreateTime.style.backgroundColor = "darkgray";
		oIfSpecialCreateTime.value = "";
		oIfSpecialCreateTime.disabled = true;
	}
}


//-------------------------------------------------------------
//添加表单时手工指定文档的到期日期及时间时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_SpecExpiredDateClick(oForm, oSender)
{
	var oIfSpecialExpiredDate = oForm["special_expireddate"];
	if(oSender.checked)
	{
		oIfSpecialExpiredDate.style.backgroundColor = "";
		oIfSpecialExpiredDate.value = "";
		oIfSpecialExpiredDate.disabled = false;
		oIfSpecialExpiredDate.focus();
	}
	else
	{
		oIfSpecialExpiredDate.style.backgroundColor = "darkgray";
		oIfSpecialExpiredDate.value = "";
		oIfSpecialExpiredDate.disabled = true;
	}
}




//-------------------------------------------------------------
//仅仅新建文档时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_InsertClick(oForm, oSender)
{
	/*if (AlertKeywords(oForm))
	{
		return false;
	}
	if(PassFormVerify(oForm, oSender))
	*/
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	//if(PassFormVerify(oForm, oSender, true))
	else
	{
		oForm["_action"].value = "insert";

		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);

		oForm.submit();
		return false;
	}
}


//-------------------------------------------------------------
//新建并发布文档时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_PublishClick(oForm, oSender)
{
	//初始化发往专题、栏目信息
	if(oForm.t_type.value == '01')
	{
		//alert("In Get_PublishToTarget!Return !");
		//是文章模板,处理发往...
		//Get_PublishToTarget();
	}	
	dateObj1 = new Date();
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
        {
                return false;
        }
	//else if(PassJSVerify(oForm, oSender))
	else
	{
		if (PassFormVerifyValue == 1)
		{
			oForm["_action"].value = "insert";
		}
		else 
		{
			oForm["_action"].value = "insert_publish";
		}

		if(PassJSVerify(oForm, oSender))
		{
			//禁止发送所有服务器CGI不关心的数据元素
			DisableAllRedundantElements(oForm);

			// 检查URL看是否符合规则
			if(CheckURL(oForm, oSender))
			{
				oForm.submit();
			}
		}

	}
}


//-------------------------------------------------------------
//新建文档并发布预览时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_PreviewClick(oForm, oSender)
{
	
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);	
	if (PassFormVerifyValue == 2)
        {
                return false;
        }
	else 
	{
		//if (PassFormVerifyValue == 1)
		//{
		//	oForm["_action"].value = "insert";
		//}
		//else 
		//{
		//	oForm["_action"].value = "insert_publish_preview";
		//}
		oForm["_action"].value = "insert_publish";
		oForm.action = "document_preview.cgi";
		//禁止发送所有服务器CGI不关心的数据元素
		//DisableAllRedundantElements(oForm);
		
		oForm.submit();
	}
}

function On_DocumentEditForm_PreviewClick2(oForm, oSender)
{
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	oForm["_action"].value = "update_publish";
	oForm.action = "document_preview.cgi";

	oForm.submit();
}


//-------------------------------------------------------------
// 用途:在默认URL模板和引用外部文章外部文章之间切换
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		pm_id：当前样式ID
//-------------------------------------------------------------

function On_DocumentCreateForm_PolymSelectClick(oForm, oSender, pm_id)
{
	var value = oSender.value;
	var inner_url;
	var outer_url;
	var ref_url;
	ref_url = "ref_url" + "_" + pm_id;
	inner_url = "inner_url" + "_" + pm_id;
	outer_url = "outer_url" + "_" + pm_id;
	if(value == "inner")
	{
		oForm[inner_url].disabled = false;
		oForm[inner_url].style.backgroundColor = "";
		oForm[outer_url].disabled = true;
		oForm[outer_url].style.backgroundColor = "darkgray";
		oForm[inner_url].focus();
	}
	else if(value == "outer")
	{
		oForm[inner_url].disabled = true;
		oForm[inner_url].style.backgroundColor = "darkgray";
		oForm[outer_url].disabled = false;
		oForm[outer_url].style.backgroundColor = "";
		oForm[outer_url].focus();
	}
}

//-------------------------------------------------------------
// 用途:样式发布切换时调用
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		pm_id：当前样式ID
//-------------------------------------------------------------
function On_DocumentCreateForm_IfPublishClick(oForm, oSender, pm_id)
{
	var inner_url;
	var outer_url;
	var ref_url;
	ref_url = "ref_url" + "_" + pm_id;
	inner_url = "inner_url" + "_" + pm_id;
	outer_url = "outer_url" + "_" + pm_id;
	if(oSender.checked)
	{
		oForm[ref_url][0].disabled = false;
		oForm[ref_url][1].disabled = false;

		if(oForm[ref_url][0].checked)
		{
			oForm[inner_url].disabled = false;
			oForm[inner_url].style.backgroundColor = "";
		}

		if(oForm[ref_url][1].checked)
		{
			oForm[outer_url].disabled = false;
			oForm[outer_url].style.backgroundColor = "";
		}
	}
	else
	{
		oForm[ref_url][0].disabled = true;
		oForm[ref_url][1].disabled = true;
		oForm[inner_url].disabled = true;
		oForm[outer_url].disabled = true;
		oForm[inner_url].style.backgroundColor = "darkgray";
		oForm[outer_url].style.backgroundColor = "darkgray";
	}
}

//-------------------------------------------------------------
// 用途:文档栏目、专题等选择发往时调用
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//-------------------------------------------------------------
var PublishTo_win = null;

function On_DocumentEditForm_SendToClick(oForm, oSender)
{
	//不支持修改文档时的操作
	alert("不支持编辑文档时的发往操作");
	return;
}


function On_DocumentCreateForm_SendToClick(oForm, oSender)
{
	if(oForm.t_type.value != '01')
	{
		//不是文章模板
		return;
	}	
	Get_PublishToTargetModal();
	return;
	
	//parent.room.cols = "145,*,145";
	//调用父窗口方法以保留全局变量
	if(top.frmTop != null){
		top.frmTop.On_DocumentCreateForm_SendToClick2();
	}else{
		//open Modal window
		Get_PublishToTargetModal();
	}
	return;	

}

//top窗口变量PublishTo_win
var PublishTo_win = null;
function On_DocumentCreateForm_SendToClick2()
{
	var cgi = "/gsps/frmPublishTo.html";
	if(!PublishTo_win || PublishTo_win.closed){
		var property = "scrollbars=yes,height=550,width=300,toolbar=no,menubar=no,location=no,left=300,top=60,screenX=10,screenY=10";
		PublishTo_win = window.open(cgi, "PublishToWin", property);
	}
	else
	{
		PublishTo_win.moveTo(300,60)
		PublishTo_win.focus();
		PublishTo_win.nullText();
	}
}

//-------------------------------------------------------------
// 用途:获取发往的栏目、专题信息
// 参数:
//-------------------------------------------------------------
function Get_PublishToTarget()
{
	//调用父窗口方法以保留全局变量
	if(top.frmTop != null){
		top.frmTop.Get_PublishToTarget2();
	}else{
		//Get_PublishToTargetModal();
	}
}

function Get_PublishToTarget2()
{
	//top窗口变量PublishTo_win
	if(!PublishTo_win || PublishTo_win.closed){
	}
	else
	{
		top.frmPanel.frmCenter.document.myform.elements['subject_target'].value = PublishTo_win.getSubjectTarget();
		top.frmPanel.frmCenter.document.myform.elements['column_target'].value = PublishTo_win.getColumnTarget();
		top.frmPanel.frmCenter.document.myform.elements['daemon'].value = PublishTo_win.getDaemonFlag();
		//只使用一次,但必须使用一次
		PublishTo_win.nullText();
	}
}

function Get_PublishToTargetModal()
{
        var out;
        out = window.showModalDialog("/document/tech/pc/subject/list/index.html",out,"dialogHeight: 550px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
        
        if(out != null && out[0] == "true")
        {
                self.document.myform.subject_target.value = out[1];
                self.document.myform.column_target.value = out[2];
                self.document.myform.daemon.value = out[3];
        }       
}


//==============================================================================================
// 文档修改表单(document_edit.cgi) JavaScript Function
//==============================================================================================



//-------------------------------------------------------------
//修改表单时手工指定文档创建日期及时间时调用
//-------------------------------------------------------------
function On_DocumentEditForm_SpecCreateTimeClick(oForm, oSender)
{
	return  On_DocumentCreateForm_SpecCreateTimeClick(oForm, oSender);
}


//-------------------------------------------------------------
//修改表单时手工指定文档的到期日期及时间时调用
//-------------------------------------------------------------
function On_DocumentEditForm_SpecExpiredDateClick(oForm, oSender)
{
	return On_DocumentCreateForm_SpecExpiredDateClick(oForm, oSender);
}




//-------------------------------------------------------------
//仅仅修改文档时调用
//-------------------------------------------------------------
function On_DocumentEditForm_UpdateClick(oForm, oSender)
{
	/*if (AlertKeywords(oForm))
	{
		return false;
	}
	if(PassFormVerify(oForm, oSender))
	*/
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	else
	{
		oForm["_action"].value = "update";

		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);


		oForm.submit();
	}
}

//-------------------------------------------------------------
//修改并发布文档时调用
//-------------------------------------------------------------
function On_DocumentEditForm_PublishClick(oForm, oSender)
{
	//if(PassJSVerify(oForm, oSender))
	//if(PassFormVerify(oForm, oSender) && PassJSVerify(oForm, oSender))
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	//else if (PassFormVerifyValue != 2 && PassJSVerify(oForm, oSender))
	else if (PassFormVerifyValue != 2)
	{
		if (PassFormVerifyValue == 1)
		{
			oForm["_action"].value = "update";
		}
		else
		{
			oForm["_action"].value = "update_publish";
		}
		
		if(PassJSVerify(oForm, oSender))
		{
			//禁止发送所有服务器CGI不关心的数据元素
			DisableAllRedundantElements(oForm);

			// 检查URL看是否符合规则
			if(CheckURL(oForm, oSender))
			{
				oForm.submit();
			}
		}
	}
}

//-----------------------------------------------------------------
//区块审核预览
//-----------------------------------------------------------------
function On_DocumentEditForm_RegionAuditClick(oForm, oSender)
{
	//var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	//if(PassFormVerifyValue == 2)
	//{
	//	return false;
	//}
	//else if(PassFormVerifyValue != 2)
	//{
	//	oForm.action = "region_audit.cgi";
	//	oForm.submit();
	//}
	var p_id = oForm["p_id"].value;
	var t_id = oForm["t_id"].value;
	var d_id = oForm["d_id"].value;
	var url="region_audit.cgi";//?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	var isIE=false;
	var userAgent = navigator.userAgent.toLowerCase();
	if ((userAgent.indexOf('msie') != -1) && (userAgent.indexOf('opera') == -1)) {
		isIE = true;
	}
	if(isIE){
		oForm.action=url;
		oForm.target = "_blank";
		oForm.submit();
		//var arg = new Array();
		//window.showModalDialog(url, oForm, "dialogWidth:800px;dialogHeight:600px;status:yes;scroll:yes;help:no");
		//oForm.submit();
		//oForm.action=url;
		//oForm.target = "_blank";
		//oForm.submit();
	}
	else
	{
		var win = window.open(url, 'newwin', "Width=800px,Height=600px,scrollbars=yes");
		win.dialogArguments = oForm;
		oForm.target='newwin';
		oForm.action=url;
		oForm.submit();
	}

	oForm.action = "";
	oForm.target = "";
}


//-------------------------------------------------------------
// 开始检查文档URL是否符合规则
//-------------------------------------------------------------
function CheckURL(oForm, oSender)
{
	var pm_id;
	var ref_url;
	var chk_pm_id = oForm["pm_id"];
	if(chk_pm_id != null)
	{
		var len = chk_pm_id.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(chk_pm_id[i].checked)
				{
					pm_id = chk_pm_id[i].value;
					ref_url = "ref_url_" + pm_id;
					if(!CheckRefURL(oForm, oForm[ref_url], pm_id))
					{
						return false;
					}
				}
			}
		}
		else
		{
			pm_id = chk_pm_id.value;
			ref_url = "ref_url_" + pm_id;
			if(!CheckRefURL(oForm, oForm[ref_url], pm_id))
			{
				return false;
			}
		}
	}
	
	return true;
}



//-------------------------------------------------------------
// 开始检查文档URL是否符合规则
//-------------------------------------------------------------
function CheckRefURL(oForm, oRefUrl, pm_id)
{
	var ref_url;
	var url_name;
	for(var i=0; i<oRefUrl.length; i++)
	{
		if(oRefUrl[i].checked)
		{
			ref_url = oRefUrl[i].value;
			if(ref_url == "inner")
			{
				url_name = "inner_url_" + pm_id;
			}
			else
			{
				url_name = "outer_url_" + pm_id;
			}
			
			var res = CheckURLRule(oForm, url_name);
			if (res != 0)
			{
				if(res == -1)
				{
					alert("错误:指定的URL没有后缀名!");
				}
				else if (res == -2)
				{
					alert("错误:指定的URL不合法!请参照正确的语法填写!\n规范:URL只能由[a-zA-Z0-9.-_/]等字符组成,不能包含汉字,空格等非法字符!");
				}
				oForm[url_name].focus();
				oForm[url_name].select();
				return false;
			}

			if(pm_id == "1")
			{
				if (!CheckURLChange(oForm, url_name))
				{
					return false;
				}
			}
		}
	}
	return true;

}


//-------------------------------------------------------------
// 开始检查文档URL是否发生了改变
//-------------------------------------------------------------
function CheckURLChange(oForm, url_name)
{
	var old_url1 = oForm["old_url_1"];
	if (old_url1 != null)
	{
		if (old_url1.value != oForm[url_name].value)
		{
			if (!confirm("URL做了修改,原文档将被设为跳转到新URL!\n是否继续?"))
			{
				return false;
			}
		}
	}
	return true;
}


//-------------------------------------------------------------
// 检查文档的URL是否符合规则
// URL规则定义如下：
//	URL只能由如下字符构成	
//		a-zA-Z0-9
//		-_./
//		${XXX}
//		
//-------------------------------------------------------------
function CheckURLRule(oForm, url_name)
{
	var i;
	var c;
	var code;
	var Url = oForm[url_name].value;
	
	//先检查给定的URL是否是绝对链接
	var re = new RegExp("^[a-zA-Z]{3,}:\\/\\/.*", "i");
	if(Url.match(re))
	{
		return 0;
	}
	
	if (oForm["pub_no_suffix"].value == 'false')
	{
		var re = new RegExp("(.*)\\.([^\\\\\/]+)$", "i");
		if(!Url.match(re))
		{
			return -1;
		}
	}
	
	//先将${XXX}替换为空串,不参与检查
	Url = Url.replace(/\$\{[^\}]*\}/g, "");

	for(i=0; i<Url.length; i++)
	{
		code = Url.charCodeAt(i);
		if(code > 127)
		{
			return -2;
		}
		c = Url.charAt(i);
		if(!isalpha(c) 
			&& !isdigit(c)
			&& c != '.'
			&& c != ','
			&& c != '-'
			&& c != '_'
			&& c != '/')
		{
			return -2;
		}
	}
	return 0;
}


//-------------------------------------------------------------
//检查c是否字母
//-------------------------------------------------------------
function isalpha(c)
{
	if((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z'))
	{
		return true;
	}
	return false;
}

//-------------------------------------------------------------
//检查c是否数字
//-------------------------------------------------------------
function isdigit(c)
{
	if(c >= '0' && c <= '9')
	{
		return true;
	}
	return false;
}


//-------------------------------------------------------------
//修改文档并发布预览时调用
//-------------------------------------------------------------
function On_DocumentEditForm_PreviewClick(oForm, oSender)
{
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	else
	{
		if (PassFormVerifyValue == 1)
		{
			oForm["_action"].value = "update";
		}
		else
		{
			oForm["_action"].value = "update_preview_publish";
		}
		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);

		oForm.submit();
	}
}


//-------------------------------------------------------------
//修改文档并审核预览时调用
//-------------------------------------------------------------
function On_DocumentEditForm_AuditPreviewClick(oForm, oSender, param)
{	
	var p_id = oForm["p_id"].value;
	var t_id = oForm["t_id"].value;
	var d_id = oForm["d_id"].value;
	//var url="region_audit.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id + "&" + param;
	var url = "field_compare.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id + "&" + param;
	var args = new Array();
	args[0] = oForm;
var property = "scrollbars=yes,height=750,width=600,toolbar=no,menubar=no,location=no,left=300,top=60,screenX=10,screenY=10";
//                 window.open(url, "PublishToWin", property);
//	window.showModalDialog(url, args, "dialogWidth:760px;dialogHeight:600px;status:yes;scroll:yes;help:no");

	var isIE=false;
	var userAgent = navigator.userAgent.toLowerCase();
	if ((userAgent.indexOf('msie') != -1) && (userAgent.indexOf('opera') == -1)) {
		isIE = true;
	}
	if(isIE){
		var arg = new Array();
	//	alert(1);
		window.showModalDialog(url, oForm, "dialogWidth:800px;dialogHeight:600px;status:yes;scroll:yes;help:no");

	}
	else{
		var win = window.open(url, null, "Width=800px,Height=600px,scrollbars=yes");
		win.dialogArguments = oForm;
	}
}



//-------------------------------------------------------------
// 处理文本的排版工作
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oTarget:需要排版的表单对象
//		oStyle:包含排版样式的表单对象
//-------------------------------------------------------------
function On_Text_TypesetClick(oForm, oSender, oTarget, oStyle)
{
	var old_caption = getValueByRef(oForm, oSender);
	var value = getValueByRef(oForm, oTarget);
	setValueByRef(oForm, oSender, "正在排版...");
	value = formattext(value, 1);
	setValueByRef(oForm, oTarget, value);
	setValueByRef(oForm, oSender, old_caption);

	/* 分页处理 start 2008.1.8 zhangping1 modify */
	//handlerSplitPage(oTarget);
	/* 分页处理 end */
}


//-------------------------------------------------------------
// 关键词热链接处理
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oCgi:目标cgi
//		oTargetName:需要进行关键字处理的文本框名称
//-------------------------------------------------------------
function doKeywordHotLink(oForm, oSender, oCgi, oTargetName)
{
	/*
	// why get id? 
	//-----------------------
	//当前项目ID
	//-----------------------
	var p_id = getValueByName(oForm, "p_id");

	//-----------------------
	//当前模板ID
	//-----------------------
	var t_id = getValueByName(oForm, "t_id");
	*/

	try {
		tinyMCE.get(oTargetName).save();
	}catch(e){
		// no tinyeditor, do nothing
	}
	
	//-----------------------
	//当前文本框中的值
	//-----------------------
	var txtContent = getValueByName(oForm, oTargetName);
	if (txtContent == "")
	{
		alert("没有内容！");
		return false;
	}
	doKeywordReplace('/cgi-bin/gsps/keyword/www_agent.cgi',
		         oCgi,
	oForm,oTargetName,false);
}

//-------------------------------------------------------------
// 关键词热链接处理(新)
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oCgi:目标cgi
//		oTargetName:需要进行关键字处理的文本框名称
//	note by yongbin: 好像没用
//-------------------------------------------------------------
function doNewKeywordHotLink(oForm, oSender, oCgi, oTargetName)
{

	try {
		tinyMCE.get(oTargetName).save();
	}catch(e){
		// no tinyeditor, do nothing
	}
	
	//-----------------------
	//当前文本框中的值
	//-----------------------
	var txtContent = getValueByName(oForm, oTargetName);
	if (txtContent == "")
	{
		alert("没有内容！");
		return false;
	}
	doNewKeywordReplace('/cgi-bin/gsps/keyword/www_agent.cgi',
		         oCgi,
	oForm,oTargetName,false);
}

//==============================================================================================
//处理关键字相关的 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
// 转发关键字链接排版的cgi处理
// 参数:
//		agent:调用的代理cgi名称
//		cgi:目标cgi
//		form:当前的表单对象
//		target:事件发送者
//		pause:是否自动提交
//-------------------------------------------------------------
function doKeywordReplace(agent,cgi,form,target,pause)
{
	// yongbin
        var b_agent=navigator.appName;
        if(b_agent == 'Netscape')
        {
                //title = getEscapeValue(title);
                browse = "Netscape";
        }
        else
        {
                browse = "IE";
                try {
                        document.myApplet.getEncodeValue("");
                }catch(e){
			alert("Java虚拟机未安装或版本太低！");
                        return;
                }
        }       

		var browse;
		var url;
		url = cgi;
		var p_id = getValueByName(form, "p_id");
		var target_value = getValueByName(form, target);
		//var newwin;
		var screen_width = window.screen.width;
		var screen_height = window.screen.height;
		var left = (screen_width - 600)/2;
		var top = (screen_height - 400)/2;
		var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
		property = property + ",top="+top+",left="+left;
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
		subReplaceWin.focus();
		subReplaceWin.document.open("text/html");
		subReplaceWin.document.writeln("<html>");
		subReplaceWin.document.writeln("<head>");
		subReplaceWin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		subReplaceWin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">");
		subReplaceWin.document.writeln("</head>");
		subReplaceWin.document.writeln("<body>"); 
		subReplaceWin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
		subReplaceWin.document.writeln("Please Waiting.....");
		
	        target_value = target_value.replace(/镕/gi,"#Rong#");		
		target_value = target_value.replace(/—/gi,"#Squote#");
		target_value = target_value.replace(/喆/gi,"#Zhe#");
		target_value = target_value.replace(/·/gi,"#zhPoint#");
		//target_value = target_value.replace("&", "#amp#");
		subReplaceWin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
		subReplaceWin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + target + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"_p_id\" value=\"" + p_id +"\">");
		if(b_agent == "Netscape")
		{
			target_value = target_value.replace(/\"/g,"&quot;");
			//target_value = encodeURIComponent(target_value);
		}
		else
		{
			if(target_value != "")
			{
				//target_value = target_value.replace(/&nbsp;/gi, " ");
				target_value = document.myApplet.getEncodeValue(target_value);
			}
		}
		//alert(target_value);
		subReplaceWin.document.writeln("<input type=hidden name=\"" + target +  "\" value=\"" + target_value+ "\">");
		if(b_agent == "Netscape")
		{
			cgi =  getEscapeValue(cgi);
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
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

//-------------------------------------------------------------
// 转发关键字链接排版的cgi处理
// 参数:
//		agent:调用的代理cgi名称
//		cgi:目标cgi
//		form:当前的表单对象
//		target:事件发送者
//		pause:是否自动提交
//-------------------------------------------------------------
function doNewKeywordReplace(agent,cgi,oform,otarget,pause)
{ 
		//yongbin
	        var b_agent=navigator.appName;
        	if(b_agent == 'Netscape')
        	{
                	//title = getEscapeValue(title);
	                browse = "Netscape";
       		 }
       		 else
        	{
                	browse = "IE";
        	        try {
                	        document.myApplet.getEncodeValue("");
	                }catch(e){
        	                alert("Java虚拟机未正常安装或版本太低！");
                	        return;
                	}
	        }       

		var browse;
		var url;
		url = cgi;
		var p_id = getValueByName(oform, "p_id");
		var target_value = getValueByName(oform, otarget);
		var elementName = otarget.substr(9);
		
		var ifUseGlobalKeyword;		
		if (oform[elementName + "_ifUseGKeyword"].checked == true)
		{
			ifUseGlobalKeyword = "Y";	
		}
		else
		{
			ifUseGlobalKeyword = "N";	
		}
		
		var kcIDList = "";
		var usekcID;
		if (oform[elementName+"_usekcID"] == null || oform[elementName+"_usekcID"].checked == false)
		{
			kcIDList = "0";	
		}
		else if (oform[elementName+"_usekcID"].checked == true)
		{
			var chooseKcIDList = oform[elementName + "_choose_kcid"];
			for(var i=0; i<chooseKcIDList.length; i++)
			{
				if (chooseKcIDList[i].checked == true)
				{
					if (kcIDList == "")
					{
						kcIDList = chooseKcIDList[i].value;	
					}
					else
					{
						kcIDList += "," + chooseKcIDList[i].value;
					}	
				}	
			}
		}
		
		if (kcIDList == "")
		{
			kcIDList = "0";	
		}		
		
		//var newwin;
		var screen_width = window.screen.width;
		var screen_height = window.screen.height;
		var left = (screen_width - 600)/2;
		var top = (screen_height - 400)/2;
		var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
		property = property + ",top="+top+",left="+left;
		if(navigator.appName.indexOf("Netscape") != -1)
		{
			subReplaceWin=window.open("",null,property);
	  	}
		else
		{ 
			if(subReplaceWin != null)
			{
			//	subReplaceWin.close();
				subReplaceWin = null;
				subReplaceWin=window.open("","",property);
			}
			else
			{
				subReplaceWin=window.open("","",property);
			} 
		}
		subReplaceWin.focus();
		subReplaceWin.document.open("text/html");
		subReplaceWin.document.writeln("<html>");
		subReplaceWin.document.writeln("<head>");
		subReplaceWin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		subReplaceWin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">");
		subReplaceWin.document.writeln("</head>");
		subReplaceWin.document.writeln("<body>"); 
		subReplaceWin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
		subReplaceWin.document.writeln("Please Waiting.....");
		
		target_value = target_value.replace(/镕/gi,"#Rong#");		
		target_value = target_value.replace(/—/gi,"#Squote#");
		target_value = target_value.replace(/喆/gi,"#Zhe#");
		subReplaceWin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
		subReplaceWin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + otarget + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"_p_id\" value=\"" + p_id +"\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"kc_id\" value=\"" + kcIDList + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"ifUseGlobalKeyword\" value=\"" + ifUseGlobalKeyword + "\">");
		
		if(b_agent == "Netscape")
		{
			target_value = target_value.replace(/\"/g,"&quot;");
			target_value = getEscapeValue(target_value);
		}
		else
		{
			if(target_value != "")
			{
				target_value = target_value.replace(/·/gi,".");
				//target_value = target_value.replace(/&nbsp;/gi, " ");
				target_value = document.myApplet.getEncodeValue(target_value);
			}
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"" + otarget +  "\" value=\"" + target_value+ "\">");
		if(b_agent == "Netscape")
		{
			cgi =  getEscapeValue(cgi);
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
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

//==============================================================================================
// 新闻回收库文档列表表单(admin_doc/document_list.cgi) JavaScript Function
// 2004-02-25 by minghui
//==============================================================================================

//-------------------------------------------------------------
//文档全选
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm["id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

//-------------------------------------------------------------
//	搜索文档时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_SearchClick(oForm, oSender)
{
	oForm["_action"].value = "";
	On_DocumentListForm_SearchClick(oForm, oSender);
}


//-------------------------------------------------------------
//	选择文档查看时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_ViewClick(oForm, oSender)
{
	var oCheckboxColl = oForm["id"];
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要查看的文档!");
		return;
	}
	if(j > 1)
	{
		alert("一次只能查看一条文档!");
		return;
	}
	var cgi_url =  "doc_view.cgi?id=" + id;
	window.self.open(cgi_url, "_blank");
}


//-------------------------------------------------------------
//	选择文档删除时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm["id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	if(prompt("请确定是否删除！(yes/no)", "no") == "yes")
	{
		oForm["_action"].value = "delete";
		oForm.submit();
	}
}



//-------------------------------------------------------------
//	回收删除的文档时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_ReclaimClick(oForm, oSender)
{
	var oCheckboxColl = oForm["id"];
	
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要回收的文档!");
		return;
	}
	oForm["_action"].value = "reclaim";
	oForm.submit();
}


//-------------------------------------------------------------
//	列表对象快速检索
//-------------------------------------------------------------
function On_QuickFindPress(eve,oSender, oReceiver, oCaseObj)
{
	var e=window.event?window.event:eve;
	if(e.keyCode == 13)
	{
		var found = On_QuickFindClick(oSender, oReceiver, oCaseObj);
		if(found == 1)
		{
			oSender.focus();
		}
	}
}



//-------------------------------------------------------------
//	列表对象快速检索
//-------------------------------------------------------------
function On_QuickFindClick(oTextObj, oSelectObj, oCaseObj)
{
	var key = oTextObj.value;
	var len = oSelectObj.length;
	var selectedIndex = oSelectObj.selectedIndex;	
	var value;
	var text;
	var key_find;
	var value_find;
	var text_find;
	var i;
	var matchcase = false;
	if (oCaseObj != null)
	{
		matchcase = oCaseObj.checked;
	}
	
	//先查找下半部分
	for (i = selectedIndex + 1; i < len; i++)
	{
		value = oSelectObj.options[i].value;
		text = oSelectObj.options[i].text;

		if (matchcase)
		{
			key_find = key;
			value_find = value;
			text_find = text;
		}
		else
		{
			key_find = key.toLowerCase();
			value_find = value.toLowerCase();
			text_find = text.toLowerCase();
		}
		
		if (text_find.indexOf(key_find) >= 0 || value_find.indexOf(key_find) >= 0)
		{
			oSelectObj.value = value;
			//oSelectObj.fireEvent("onchange");
			return 1;
		}
	}

	//再查找上半部分
	for (i = 0; i < selectedIndex; i++)
	{
		value = oSelectObj.options[i].value;
		text = oSelectObj.options[i].text;

		if (matchcase)
		{
			key_find = key;
			value_find = value;
			text_find = text;
		}
		else
		{
			key_find = key.toLowerCase();
			value_find = value.toLowerCase();
			text_find = text.toLowerCase();
		}
		
		if (text_find.indexOf(key_find) >= 0 || value_find.indexOf(key_find) >= 0)
		{
			oSelectObj.value = value;
				//oSelectObj.fireEvent("onchange");
			return 1;
		}
	}

	//如果都没找到
	oSelectObj.selectedIndex = selectedIndex;
	value = oSelectObj.options[selectedIndex].value;
	text = oSelectObj.options[selectedIndex].text;

	if (matchcase)
	{
		key_find = key;
		value_find = value;
		text_find = text;
	}
	else
	{
		key_find = key.toLowerCase();
		value_find = value.toLowerCase();
		text_find = text.toLowerCase();
	}
		
	if (text_find.indexOf(key_find) >= 0 || value_find.indexOf(key_find) >= 0)
	{
		return 1;
	}
	
	alert("对不起，没找到！");
	return 0;
}


//-------------------------------------------------------------
//	跨项目表单显示/隐藏切换
//-------------------------------------------------------------
function On_CrossPublishFormSwitch(oForm, oCrossTable, oSender)
{
	if(oSender.checked)
	{
		oCrossTable.style.display = "inline";
	}
	else
	{
		oCrossTable.style.display = "none";
	}
}



//==============================================================================================
// 文档添加、编辑表单之CGICall算法 JavaScript Function
//==============================================================================================
function On_DocumentForm_CGICallClick(oForm, oSender, _data_input, url, params)
{
	if (url != "")
	{
		var _action = oForm._action.value;
		params += "&_action=" + _action;
		params += "&_p_id=" + oForm.p_id.value;
		params += "&_t_id=" + oForm.t_id.value;
		if (oForm["d_id"] != null)
			params += "&_d_id=" + oForm.d_id.value;
		var get_input_value = "oForm." + _data_input + ".value";
		params += "&_data=" + eval(get_input_value);
		/*
			_action=<action>
			_p_id=<p_id>
			_t_id=<t_id>
			_d_id=<d_id>
			_data=<data>
			_new_data=<new_data>
			param_name1=&<param_value1>
			param_name2=&<param_value2>
		*/

		if (params)
			url += "?" + params;
		//alert(url);
		
	        var out;
	        out = window.showModalDialog(url,out,"dialogHeight: 550px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
	        if(out != null && out[0] == "true")
	        {
	        	if (_action == "insert")
	        	{
	        		//每次都视为重新选择
	                	var return_value = "self.document.myform." + _data_input + ".value = '" + out[1] + "'";
	                	eval(return_value);
	                }
	        	else if (_action == "update")
	        	{
	        		//保留原始值和重新选择的值,连接###
	        		var return_value = "self.document.myform." + _data_input + ".value = '" + out[1] + "'";
	        		eval(return_value);
	        	}
	        	else
        		{
        			alert("操作无效[非添加、编辑表单]！");
        		}
	        }
	}
	else
	{
		alert("算法配置错误：from_cgi空！");
	}
		
	return;
}


//==============================================================================================
// 文档列表文档拒签原因查看 JavaScript Function
//==============================================================================================
function On_DocumentForm_RejectReasonClick(p_id, t_id, d_id)
{

	if (p_id != "" && t_id != "" && d_id != "")
	{
	        var out;
	        var url = "document_reject_reason.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	 //       out = window.showModalDialog(url,out,"dialogHeight: 350px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
var property = "scrollbars=yes,height=350,width=300,toolbar=no,menubar=no,location=no,left=300,top=60,screenX=10,screenY=10";
                 window.open(url, "PublishToWin", property);
	}
	else
	{
		alert("参数错误：p_id,t_id,d_id！");
	}
		
	return;
}

//=====================================
// 预览水印
//====================================
function doPreviewWM(oForm, target,flag)
{
	if (flag == 'U')
	{
		var changeradio = oForm["change_"+target][0].checked;
		if(changeradio)
		{
			alert("选择需要原始文件!");
			return;
		}
	}
	else
	{
		if (oForm[target].value == "")
		{
			alert("没有选择文件");
			return;
		}
	
		var wmField = target.replace("PF", "WM");
		if (oForm[wmField].value == "0")
		{
			alert("没有选择水印图");
			return;
		}
	}
	oForm.target="_blank";
	oForm.action = "/cgi-bin/gsps/watermark_preview.cgi";
	oForm["wm_previewField"].value = target;
	oForm.submit();
}

function On_Show_KeywordCategory(oForm,oSender,elementName)
{
	var list = document.getElementById(elementName+"_keyword_category_list");
	if (oSender.checked == true)
	{
		list.style.display = "inline";
	}
	else	
	{
		list.style.display = "none";
	}
}

function onClick_SwitchUrl(oForm,pm_id)
{
	var defaultUrl = oForm["default_url_" + pm_id].value;
	oForm["inner_url_"+pm_id].value=defaultUrl;
}

function on_Click_ShowUrlHistoryModal(oForm)
{
	var p_id=oForm["p_id"].value;
	var t_id=oForm["t_id"].value;
	var d_id=oForm["d_id"].value;
	var url = "check_urlHistory.cgi?p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	
//window.showModalDialog(url);
 var property = "scrollbars=yes,height=550,width=300,toolbar=no,menubar=no,location=no,left=300,top=60,screenX=10,screenY=10";
                PublishTo_win = window.open(url, "PublishToWin", property);
	return;
}


//-------------------------------------------------------------
// 用途:在文件上传选项之间切换
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//-------------------------------------------------------------
function OnClick_SwitchFileUpload(oForm, oSender)
{
	var change_name = oSender.name;
	if (change_name.substr(0, 7) != "change_")
	{
		return;
	}
		
	var yes_name = change_name.substr(7);
	var no_name = "no_" + yes_name;
	var abs_name = "abs_" + yes_name;
	
	var yes_elem = oForm[yes_name];
	var no_elem = oForm[no_name];
	var abs_elem = oForm[abs_name];
	
	if(oSender.value == "yes")
	{		
		if (no_elem != null)
		{
			no_elem.disabled = true;
			no_elem.style.backgroundColor = "darkgray";
		}
		
		if (abs_elem != null)
		{
			abs_elem.disabled = true;
			abs_elem.style.backgroundColor = "darkgray";
		}
		
		if (yes_elem != null)
		{
			yes_elem.disabled = false;
			yes_elem.style.backgroundColor = "";
			yes_elem.focus();
		}
	}
	else if(oSender.value == "no")
	{
		if (yes_elem != null)
		{
			yes_elem.disabled = true;
			yes_elem.style.backgroundColor = "darkgray";
		}
		
		if (abs_elem != null)
		{
			abs_elem.disabled = true;
			abs_elem.style.backgroundColor = "darkgray";
		}
		
		if (no_elem != null)
		{
			no_elem.disabled = false;
			no_elem.style.backgroundColor = "";
			no_elem.focus();
		}
	}
	else if(oSender.value == "abs")
	{
		if (no_elem != null)
		{
			no_elem.disabled = true;
			no_elem.style.backgroundColor = "darkgray";
		}
		
		if (yes_elem != null)
		{
			yes_elem.disabled = true;
			yes_elem.style.backgroundColor = "darkgray";
		}
		
		if (abs_elem != null)
		{
			abs_elem.disabled = false;
			abs_elem.style.backgroundColor = "";
			abs_elem.focus();
		}
	}
}

Function.prototype.Bind = function() { 
	var __m = this, object = arguments[0], args = new Array(); 
	for(var i = 1; i < arguments.length; i++){
		args.push(arguments[i]);
	}
	
	return function() {
		return __m.apply(object, args);
	}
};
Function.prototype.bindAsEventListener = function(object) {
  var __method = this;
  return function(event) {
    return __method.call(object, event || window.event);
  }
}
var isIE = false;
var userAgent = navigator.userAgent.toLowerCase();
if ((userAgent.indexOf('msie') != -1) && (userAgent.indexOf('opera') == -1)) {
	isIE = true;
}


if(typeof IO == 'undefined' )IO = {};
IO.Script = function(){
	this.Init.apply(this, arguments);
};

IO.Script.prototype = {
	_scriptCharset: 'gb2312',
	_oScript: null,
	
	/**
	 * Constructor
	 * 
	 * @param {Object} opts
	 */
	Init : function(opts){
		this._setOptions(opts);
	},
	
	_setOptions: function(opts) {
		if (typeof opts != 'undefined') {
			if (opts['script_charset']) {
				this._scriptCharset = opts['script_charset'];
			}
		}
	},
	
	_clearScriptObj: function() {
		if (this._oScript) {
			try {
				this._oScript.onload = null;
				if (this._oScript.onreadystatechange) {
					this._oScript.onreadystatechange = null;
				}
				
				this._oScript.parentNode.removeChild(this._oScript);
				//this._oScript = null;
			} catch (e) {
				// Do nothing here
			}
		}
	},
	
	_callbackWrapper: function(callback) {
		if (this._oScript.onreadystatechange) {
			if (this._oScript.readyState != 'loaded' && this._oScript.readyState != 'complete') {
				return;
			}
		}
		
		if (typeof callback != 'undefined') {
			callback();
		}
		
		this._clearScriptObj();
	},
	
	load: function(url, callback){
		this._oScript = document.createElement('SCRIPT');
		this._oScript.type = "text/javascript";
		
		if (isIE) {
			this._oScript.onreadystatechange = this._callbackWrapper.Bind(this, callback);
		} else {
			this._oScript.onload = this._callbackWrapper.Bind(this, callback);
		}
		
		this._oScript.charset = this._scriptCharset;
		this._oScript.src = url;
		
		//document.body.appendChild(this._oScript);
		document.getElementsByTagName('head')[0].appendChild(this._oScript);
	}
} 

	var Select_linkage = function(){
		this.Init.apply(this, arguments);
	};
	Select_linkage.prototype={
		/*初始化*/
		Init:function(cgi_url,parm,sub_id){

			this.sub_id = sub_id;
			this.cgi_url = cgi_url;
			this.parm = parm;
			var sendurl=this.cgi_url;
			if (sendurl.indexOf("?") > 0){
				sendurl += "&" + this.parm + "&randnum=" + Math.random();
			}
			else{
				sendurl += "?" + this.parm + "&randnum=" + Math.random();
			}
		
	//	document.getElementById("test1").value=sendurl;	
			(new  IO.Script()).load(sendurl,this._getResultHtml.Bind(this));
		},

		
		/*填充子下拉框*/
		_getResultHtml:function(){
		
			subselect = document.myform[this.sub_id];
		
			try{
				
				var info = eval("subselect_" + this.sub_id) ; 
			
			
				
				/*while(subselect.length>0) { 
				     subselect.remove(subselect.options[0]);
				}*/
				subselect.innerHTML='';
				for(var i = 0; i <info.length; i++) {
					var oo=document.createElement('option');
					oo.text=(i)+"---"+info[i].key;
					oo.value=info[i].value;
					if(isIE)
						subselect.add(oo)
					else
						subselect.appendChild(oo);
					//
				} 
			}
			catch(e)
		 {
	/*	 	while(subselect.length>0) {
				     subselect.remove(subselect.options[0]);
				}*/
subselect.innerHTML='';
		 	var oo=document.createElement('option');
		 	oo.text="联动出错";
					oo.value="";
					if(isIE)
						subselect.add(oo)
					else
						subselect.appendChild(oo);
		}
			

		}
	}

// open insert survey window, yongbin
function openSurvey(f_id, p_id){
	try {
		var t_id = document.myform.elements['t_id'].value;
	}catch(e) {
		var t_id = 1;
	}
	window.open("/gsps/htmleditor/survey.html?f_id=" + f_id + "&p_id=" + p_id + "&t_id=" + t_id, null, "width=500px,height=420px,scrollbars=yes");
}

// add by qixiu audit multi-doc
function On_DocumentListForm_AuditClick(oForm, oSender)
{
		oForm["_action"].value = '';
		oForm.action = "news_audit.cgi";
		oForm.submit();
}


function On_DocumentListForm_Associate(oForm, oSend)
{
	var oCheckboxColl = oForm["d_id"];

	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i = 0; i < len; i ++)
			{
				if(oCheckboxColl[i].checked)
				{
					d_id = oCheckboxColl[i].value;
					j ++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j ++
			}
		}
	}

	if(j == 0)
	{
		alert("请选择需要关联分页的文档!");
		return;
	}
	
	oForm["_action"].value = "associate";
	oForm.action = "doc_associate.cgi";
	oForm.submit();
}

