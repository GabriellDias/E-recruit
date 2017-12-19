<?php
/*
 *   Author: Samuel de Souza Silva
 */
//Define o diretorio raiz do sistema
define('DIR_RAIZ', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

//Inclusao de arquivos necessarios para o carregamento do sistema
require_once 'System/Autoload.php';

//AutoLoad de classes instanciadas
$autoLoad = new Autoload();
$autoLoad->setPath(DIR_RAIZ);
spl_autoload_register(array($autoLoad, 'loadSystem'));
spl_autoload_register(array($autoLoad, 'loadControllers'));
spl_autoload_register(array($autoLoad, 'loadModels'));

//Carregamento do Sistema
$sistema = new System();
$sistema->start();
