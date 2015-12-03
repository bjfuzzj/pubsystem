<?php
class PY {
	private $py;
	function __construct(){
		require_once('pinyin_table.php');
		$this->py = $pinyin_table;
	}

	public function _get_pinyin($string=''){
		/**
			preg_match_all("/[\x80-\xff]?./", $string, $arr);
			'我爱北京天安 门asdfasdf陈健123技术部a123　ａｂｃ全角？朝'
		**/
		$pinyin = '';
		$string = trim(preg_replace('/(\s+)/', ' ', strtolower($string)));
		if ($string == ''){
			return '';
		}else{
			//echo $this->_get_pinyin_string($string);
			return $this->_get_pinyin_string($string);
		}
	}

	private function _get_pinyin_string($string=''){
		$flow = array();
		$tag = 0;
		for ($i=0; $i<strlen($string); $i++){
			if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe){
				//if ($tag == 1){
				//	array_push($flow, ord(' '));
				//}
				//$tag = 0;
				$h = ord($string[$i]);
				if (isset($string[$i+1])){
					$i++;
					$l = ord($string[$i]);
					if (isset($this->py[$h][$l])){
						array_push($flow,$this->py[$h][$l]);
					}else{
						array_push($flow,$h);
						array_push($flow,$l);
					}
				}else{
					array_push($flow,ord($string[$i]));
				}
			}else{
				//if ($tag == 0){
				//	array_push($flow, ord(' '));
				//}
				//$tag = 1;
				array_push($flow,ord($string[$i]));
			}
		}
		if ($flow){
			$string = '';
			foreach($flow as $v){
				if (is_array($v)){
					/**
					if (count($v) > 1){
						foreach ($v as $key => $val){
							echo $val.'|';
						}
						echo '<br/>';
					}
					**/
					if (is_numeric($v[0])){
						$string .= chr($v[0]);
					}else{
						$string .= $v[0];
					}
				}else{
					//echo $v;
					if (is_numeric($v)){
						$string .= chr($v);
					}else{
						$string .= $v;
					}
				}
			}
			//return $string.chr(0);
			return $string;
		}else{
			return '';
		}
	}
}

?>
