<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class System{
	private $controller;
	private $url;
	private $_explode;
	private $controllersPermitidos = array("vaga","area","usuario","pergunta","entrevista");

	public function __construct(){
		$this->setUrl();
		$this->setExplode($this->url);
		$this->setController();
	}

	protected function setUrl(){
		$this->url = (isset($_GET['url']) ? htmlentities(strip_tags($_GET['url'])) : 'home');
	}

	//pega a url e divide os parametros que
	//estao entre a / colocando-os num array
	protected function setExplode($url){
		$this->_explode = explode( '/' , $url);
	}

	private function setController(){
		$this->controller = $this->_explode[0];
	}

	public function start(){
		if(in_array($this->controller, $this->controllersPermitidos)){
			$this->controller = ucfirst($this->controller).'Controller';
			$controlador = new $this->controller($this->_explode);
		}else{
			$this->errorPage();
		}
	}

	public function errorPage(){
		//echo 'Erro 404. Page Not Found.';
		header("HTTP/1.0 404 Not Found");
		die();
	}
}

?>
