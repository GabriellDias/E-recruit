<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class Autoload{

	public function setPath($path){
		set_include_path($path);
	}

	public function loadSystem($className){
		$file = get_include_path().DS.'System'.DS.$className.'.php';
		if (is_readable($file)) {
			include $file;
		}
	}

	public function loadControllers($className){
		$file = get_include_path().DS.'Controllers'.DS.$className.'.php';
		if (is_readable($file)) {
			include $file;
		}
	}

	public function loadModels($className){
		$file = get_include_path().DS.'Models'.DS.$className.'.php';
		if (is_readable($file)) {
			include $file;
		}
	}


}
