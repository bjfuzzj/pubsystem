<?php

$src = "./t1/publish.php";
$content = file_get_contents($src);
$code = compress($content);

function compress($content){
	$code = array();
	$tokens = token_get_all($content);
	foreach ($tokens as $i => $token)
	{
		if (is_string($token)) continue;
		switch ($token[0])
		{
			case T_GLOBAL:
				break;
			case T_FUNCTION:
				break;
			case T_VAR:
				print_r($token);
				break;
			case T_OPEN_TAG:
			case T_CLOSE_TAG:
				break;
			case T_COMMENT:
			case T_DOC_COMMENT:
			case T_WHITESPACE:
				break;
			case T_CASE:
			case T_CLASS:
			case T_CLONE:
			case T_CONST:
			case T_ECHO:
			case T_IMPLEMENTS:
			case T_INTERFACE:
			case T_INCLUDE:
			case T_INCLUDE_ONCE:
			case T_INSTANCEOF:
			case T_NEW:
			case T_PRIVATE:
			case T_PUBLIC:
			case T_PROTECTED:
			case T_REQUIRE:
			case T_REQUIRE_ONCE:
			case T_RETURN:
			case T_STATIC:
			case T_THROW:
			case T_EXTENDS:
			case T_AS:
			case T_LOGICAL_AND:
			case T_LOGICAL_OR:
			case T_LOGICAL_XOR:
				break;
			default:
		}
	}
}
?>

