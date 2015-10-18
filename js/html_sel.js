function gen_sel_options(p_id, this_sel, sqlstr, vv, pv, pf)
{

	if(pf != "" && pv == "") return;
	var xmlhttp;
	var flag = 0;
	try{
		xmlhttp = new XMLHttpRequest();
		
		}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e) { }
	}
	
	var tm = new Date();
	cgi_prog = "get_sel_data.php?p_id=" + p_id + "&sqlstr=" + sqlstr + "&pf=" +pf + "&pv=" + pv;
	cgi_prog += "&tm=" + tm.getTime();

	//alert(cgi_prog);
	xmlhttp.open("get", cgi_prog, false);
	xmlhttp.send(null);

	var sel_str = xmlhttp.responseText;

	try{ eval(sel_str); } catch(e) { alert(sel_str); };
	this_sel.options.length = 1;
	for(i=0, ii=1; i<sel_data.length; i++)
	{
		this_option = sel_data[i];

		this_sel.options[ii] = new Option();
		this_sel.options[ii].value = this_option['value'];
		this_sel.options[ii].text  = this_option['text'];

		if(this_sel.options[ii]['value'] == vv) this_sel.options[ii].selected = true;
		ii++;
	}
}
