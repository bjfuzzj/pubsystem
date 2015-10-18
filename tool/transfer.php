<?php
if($argc < 4)
{
	printf("php %s <path> <gbk|utf-8> <gbk|utf-8>\n", $argv[0]);
	exit;
}

print "function error_exit
{
	echo \"\$1\" 1>&2
	exit 1
}\n";


$findpath = $argv[1];
$fcode = $argv[2];
$tcode = $argv[3];

$data = `find $findpath -name "*.*"`;
$sp = explode("\n", $data);
foreach($sp as $line)
{
	transfer($line);
}

function transfer($line)
{
	global $fcode, $tcode;
	$filename = trim($line);
	if(!is_file($filename)) return;
	$pos = strrpos($filename, "/");
	$filepath = substr($filename, 0, $pos);
	$filename = substr($filename, $pos+1);

	$pos = strrpos($filename, ".");
	$posix = substr($filename, $pos);
	$posix = strtolower($posix);
	if($posix == ".gif" || $posix == ".jpg"  || $posix == ".png"  
		|| $posix == ".bmp"  || $posix == ".swf"
		|| $posix == ".zip"  || $posix == ".exe"
	)
	{
		return;
	}

	$cmd = "(cd $filepath; iconv -f $fcode -t $tcode $filename > /tmp/ghc.txt && mv -f /tmp/ghc.txt $filename)  || error_exit \"$filepath/$filename\"";
	print "$cmd\n";
}

?>
