<?php

$html_urlbase = "http://abc.com/";
$ch = substr($html_urlbase, strlen($html_urlbase) - 1, 1);
print "ch:$ch\n";
exit;
$html_urlbase = substr($html_urlbase, 0, strlen($html_urlbase) - 1);

