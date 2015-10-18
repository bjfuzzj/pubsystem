<?php
require_once("plib/config_inc.php");
$html_charset = HTML_CHARSET;
header("Content-type: text/html; charset=$html_charset");
require_once("plib/head.php");
$cgi = getCGI();
$p_id = $cgi[p_id];
$t_id = $cgi[t_id];
$f_id = $cgi[f_id];

if($p_id == "" || $t_id == "") sys_exit("参数错误");
conProjDB($p_id, $t_id);

if($f_id == "")
{
	$row = array('showorder'=>0, 'showwidth'=>0, 'showheight'=>'0', 'showmargin'=>0);
}
else
{
	$sqlstr = "select * from tempdef where f_id= $f_id";
	$res = mysql_query($sqlstr, $proj_mysql) or exit(mysql_error() . "\n" . $sqlstr);

	$row = mysql_fetch_array($res, MYSQL_ASSOC);
}

?>

<b>1.字段存储<br></b>
是否入库:
<select name="if_into_db" size=1>
<option  value=d <?php if($row[if_into_db] == 'd') echo "selected"; ?> >默认</option>
<option  value=y <?php if($row[if_into_db] == 'y') echo "selected"; ?> >是</option>
<option  value=n <?php if($row[if_into_db] == 'n') echo "selected"; ?> >否</option>
</select><br>

字段名称: <input type=text name=f_name value="<?php echo $row[f_name] ?>" size=25><br>
存储类型(在库中的真实数据类型): <input type=text name=real_type value="<?php echo $row[real_type] ?>" size=25><br>

<b>2.校验<br></b>
是否为空:<select name="ifnull" size=1>
<option <?php if($row[ifnull] == 'y') echo "selected"; ?> value=y>可空</option>
<option <?php if($row[ifnull] == 'n') echo "selected"; ?> value=n>非空</option>
</select><br>
数据校验(用Javascript函数)<br>
1)格式：function xxx(){...;return yyy;}2)如校验通过返回true,否则返回false <br>
&nbsp;&nbsp;&nbsp;&nbsp;
<textarea name=validate style="font-size:13px; width:600px;height:240;"><?php echo $row[validate] ?></textarea>


<br><b>3.显示<br></b>
显示顺序:<input type=text name=showorder size=2 value="<?php echo $row[showorder]; ?>">
显示宽度:<input type=text name=showwidth size=2 value="<?php echo $row[showwidth]; ?>">
显示高度:<input type=text name=showheight size=2 value="<?php echo $row[showheight] ?>">
显示边距:<input type=text name=showmargin size=2 value="<?php echo $row[showmargin] ?>">
<select name="hide" size="1">
<option  <?php if($row[hide] == 'n') echo "selected"; ?> value="n">显示</option>
<option  <?php if($row[hide] == 'y') echo "selected"; ?> value="y">隐藏</option>
</select>
