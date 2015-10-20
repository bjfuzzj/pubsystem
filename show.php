<?php
require_once("plib/db.php");
require_once("plib/GoogleAuthenticator.php");
$id=isset($_GET[id])?intval($_GET['id']):1;
$sqlstr ="select id, login, name, type, priv, allproj,  passwd, salt,secret from user where  id=$id limit 1";
$res = mysql_query($sqlstr,$pub_mysql) or exit("系统忙， 请稍候再试。".$sqlstr . ":\n" . mysql_error());
$data=array();
$row_user = mysql_fetch_array($res, MYSQL_ASSOC);
$ga = new PHPGangsta_GoogleAuthenticator();
$secret=$row_user['secret'];
$qrCodeUrl = $ga->getQRCodeGoogleUrl('www.17co8.com', $secret); //第一个参数是"标识",第二个参数为"安全密匙SecretKey" 生成二维码信息
echo "Google Charts URL for the QR-Code: ".$qrCodeUrl."<br/>";

echo "<html><body><img src='".$qrCodeUrl."'><body></html>";

$oneCode = $ga->getCode($secret); //服务端计算"一次性验证码"
echo "服务端计算的验证码是:".$oneCode."\n\n";
exit;
