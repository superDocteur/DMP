<?php

class Template {
	private $path;
	private $defaultLocale;
	
	private $vars;
	private $templatefile;

	public function __construct($path,$locale){
		$this->path=$path;
		$this->defaultLocale=$locale;
		$this->vars=array();
	}
	
	public function add($var,$val){
		if (is_object($val) || is_array($val)) {
			foreach($val as $k=>$v) {
				$vvar=$var.".".$k;
				$this->vars[$vvar]=$v;	
			}
		} else {
			$this->vars[$var]=$val;	
		}
		
	}
	
	public function load($template,$locale){
		if (is_null($locale)) {
			$locale=(is_null($this->defaultLocale)?NULL:$this->defaultLocale);
		}
		if (is_null($locale)) {
			$path=$this->path;
		} else {
			$pathLocale=$this->path.$locale."/";
		}
		
		if (file_exists($pathLocale.$template)) {
			$this->templatefile=file_get_contents($pathLocale.$template);
			$this->vars["templatefile.fullname"]=$pathLocale.$template;
			$this->vars["templatefile.name"]=$template;
			return TRUE;
		} elseif (file_exists($pathLocale.$template.".html")) {
			$this->templatefile=file_get_contents($pathLocale.$template.".html");
			$this->vars["templatefile.fullname"]=$pathLocale.$template.".html";
			$this->vars["templatefile.name"]=$template;
			return TRUE;
		} else {
			return NULL;
		}
	}
	public function render(){
		
		
		//$ret = preg_replace_callback(
        //'|\{£(.+?)\}|',
        //function ($matches) {
			//if ($this->vars[$matches[1]]) {
					//return $this->vars[$matches[1]];
			//} else {
				//return "[[[ TEMPLATE ERROR UNKN : ".$matches[1]."]]]";
			//}
        //},
        //$this->templatefile
    //);
    //var_dump($vars);
		print $this->renderPartial($this->templatefile,$this->vars);
		
	}
	private function renderPartial($part,$vars,$tempVars=NULL){
		//var_dump($vars);
		$ret = preg_replace_callback(
        '|\{ITER\s+£(.+?)\s+AS\s+£(.+?):£(.+?)\}(.+?)\{/ITER\}|',
        function ($matches) use ($vars,$tempVars) {
			print "IN ITER : "; var_dump($vars);
			if ($vars[$matches[1]]) {
					$NtempVars=$vars[$matches[1]];
					return renderPartial($matches[4],$vars,$NtempVars);
			} elseif ($tempVars[$matches[1]]) {
					$NtempVars=$vars[$matches[1]];
					return renderPartial($matches[4],$vars,$NtempVars);
			} else {
				return "[[[ TEMPLATE ITER ERROR UNKN : ".$matches[1]."/".$matches[2]."/".$matches[3]."/".$matches[4]."]]]";
			}
        },
        $part
    );
    //print $ret;
    //var_dump($vars);
		$ret = preg_replace_callback(
        '|\{£(.+?)\}|',
        function ($matches) use ($vars,$tempVars) {
			if ($vars[$matches[1]]) {
					return $vars[$matches[1]];
			} elseif ($tempVars[$matches[1]]) {
					return $tempVars[$matches[1]];
			} else {
				return "[[[ TEMPLATE ERROR UNKN : ".$matches[1]."]]]";
			}
        },
        $ret
    );
		return $ret;
	}
}
?>
