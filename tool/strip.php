<?php
$source = './t2';
$destination = './t3';
$path = array($source);
$file = array();
 
while ($path){
    $path_cur = array_pop($path);
    @mkdir(str_replace($source, $destination, $path_cur));
    $file = array_merge($file, glob($path_cur.'/*.php'));
    $path = array_merge($path, glob($path_cur.'/*', GLOB_ONLYDIR));
}
 
foreach ($file as $src){
    $dst = str_replace($source, $destination, $src);
    $content = file_get_contents($src);
    $code = compress($content);
    $code = str_replace("END_NAV_OF_GHC;", "END_NAV_OF_GHC;\n", $code);
    $code = str_replace("END_OF_GHC;", "END_OF_GHC;\n", $code);
    $code = str_replace("GHC_OF_END;", "GHC_OF_END;\n", $code);
    $code = str_replace("GHC_PRINT_END;", "GHC_PRINT_END;\n", $code);
    file_put_contents ($dst, $code, LOCK_EX);
    echo sprintf("%s : %s -> %s\n", $dst, filesize($src), filesize($dst));
}
 
function compress($content){
        $code = array();
        $tokens = token_get_all($content);
        foreach ($tokens as $i => $token) {
            if (is_string($token)){
                $code[$i] = $token;
            } else {
                switch ($token[0]) {
                    case T_OPEN_TAG:
                    //case T_CLOSE_TAG:
                        $code[$i] = '<?php ';
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
                    case T_FUNCTION:
                    case T_GLOBAL:
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
                    case T_VAR:
                        $code[$i] = $token[1].' ';
                        break;
                    case T_EXTENDS:
                    case T_AS:
                    case T_LOGICAL_AND:
                    case T_LOGICAL_OR:
                    case T_LOGICAL_XOR:
                        $code[$i] = ' '.$token[1].' ';
                        break;
                    default:
                        $code[$i] = $token[1];
                }
            }
        }
        return implode('', $code);
    }
?>

