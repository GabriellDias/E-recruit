<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class ConnectionFactory{

	const USER = "bot";
	const PASS = "123";
	const DATABASE = "mysql";
	const NAME_DB = "recruit";
	const HOST = "localhost";

	private static $conexao;
	public function __construct()
	{
		echo 'conexao aberta';
	}
	public static function conectar(){
		try{
			if(!isset(self::$conexao)):
				$dsn = self::DATABASE.":host"."=".self::HOST.";dbname=".self::NAME_DB;
				self::$conexao = new PDO($dsn, self::USER, self::PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			endif;
		}catch (PDOException $e){
			echo "Erro: " . $e->getMessage();
		}
		return self::$conexao;
	}

}

?>
