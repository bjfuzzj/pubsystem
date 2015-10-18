<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");
conProjDB($p_id, $t_id);
	
$i = 0;

foreach($tempdef_data as $kk=>$row)
{
	if($row[t_id] != $t_id) continue;
	if($row[if_into_db] != "y") continue;
	$sel_list .= "<option value=\"$row[cname]\">$row[cname]</option>";
}
?>


<select name=cond_type>
<option value="and">并且
<option value="or">或者
</select>

<select name=cond_field>
<?php echo $sel_list ?>
</select>

<select name=cond_equal>
<option value="=">等于
<option value="!=">不等于
<option value=">">大于
<option value="<">小于
</select>

<input name=cond_val>
<input type=button value="添加条件" onclick="add_cond_option(this.form);"><br><br>
<div id=cond_option_html></div>
